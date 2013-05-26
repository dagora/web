<?php

namespace Dagora\ApiBundle\Controller;

use Dagora\CoreBundle\Controller\Base\Controller,
    Nelmio\ApiDocBundle\Annotation\ApiDoc,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * API calls related to sources
 *
 * @Route("/sources")
 */
class SourceController extends Controller
{
    /**
     * Get a list of sources
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Get a list of sources",
     *  filters={
     *      {"name"="lat", "dataType"="float"},
     *      {"name"="lng", "dataType"="float"},
     *      {"name"="num", "dataType"="int", "default"=20},
     *      {"name"="s", "dataType"="string"},
     *      {"name"="start", "dataType"="int", "default"=0}
     *  },
     *  output="It returns a list of Sources",
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     *
     * @Route(".json", name="sources_list")
     * @Method({"GET"})
     */
    public function listAction()
    {
        // get parameters
        $params = $this->getQueryParameters();

        $start = isset($params['start']) ? intval($params['start']) : 0;

        // count number of results
        $totalResults = $this->get('dlayer')->count('Source', $params);

        $results = $resultIds = array();
        if ( $totalResults > 0 && $params['start'] < $totalResults ) {

            // get sources
            $sources = $this->get('dlayer')->findAll('Source', $params);

            // build response
            $results  = $this->buildResults($sources);
        }

        $response = array(
            'start'        => $start,
            'resultCount'  => count($results),
            'totalResults' => intval($totalResults),
            'resultsList'  => $results
        );

        // return the response, but do not cache as it has user data
        return $this->returnJSON($response, 'sources', null);
    }

    /**
     * Create a new source with data
     *
     * @ApiDoc(
     *  description="Create a new source",
     *  output="It returns a source",
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     *
     * @Route(".json", name="sources_create")
     * @Method({"POST"})
     */
    public function createAction()
    {
        $conn = $this->container->get('doctrine.dbal.default_connection');

        // it's a form
        if ( $this->getRequest()->get('title') ) {
            $params = array(
                'title' => $this->getRequest()->get('title'),
                'link'  => $this->getRequest()->get('link'),
                'unit'  => $this->getRequest()->get('unit'),
                'data'  => $this->getRequest()->get('data')
            );

            $this->importCsv($params);

            return $this->returnJSON(array(), 'sources');
        }
        // it's a JSON request
        else {
            $params = json_decode($this->getRequest()->getContent(), true);

            // update source
            $source = $this->get('dlayer')->create('Source', $params);

            return $this->returnShow($source, false);
        }
    }

    /**
     * Import a CSV file
     * @param  array $params
     */
    private function importCsv($params) {

        $conn = $this->container->get('doctrine.dbal.default_connection');

        // set content in file handler
        $fiveMBs = 5 * 1024 * 1024;
        $fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
        fputs($fp, $params['data']);
        rewind($fp);

        // get array from CSV
        $data = array();
        while (($row = fgetcsv($fp, 1000, ",")) !== FALSE) {
            $data[] = $row;
        }
        fclose($fp);

        // let's check how the CSV is structured. There can be 3 options

        // Option 1.
        // date, value
        // date2, value2

        // Option 2.
        // x,             date1,   date2
        // source_title,  value11, value12
        // source_title2, value21, value22

        // so let's check the first field to see if it's a date
        $firstField = $data[0][0];
        $dateAr     = \Dagora\CoreBundle\Entity\Data::convertDate($firstField);

        $dataToInsert    = array();
        $sourcesToInsert = array();

        // it's a date, so Option 1
        if ( $dateAr ) {

            $sourceHash  = md5($params['title']);

            $sourcesToInsert[] = '('
                . $conn->quote(trim($params['title']), 'string').', '
                . $conn->quote($sourceHash, 'string').', '
                . $conn->quote($params['unit'], 'string').', '
                . $conn->quote($params['link'], 'string').', now(), now())';

            // the data is already on the desired format
            $dataToInsert[ $sourceHash ] = $data;
        }
        // Option 2.
        else {

            // get dates which are on the first line
            $dates           = array_slice($data[0], 1);
            $avoidFirst      = true;
            foreach ($data as $lineData) {

                // do not insert first line
                if ( $avoidFirst ) {
                    $avoidFirst = false;
                    continue;
                }

                // source title
                $titleSuffix = $lineData[0];
                $sourceTitle = trim($params['title']) . ' - ' . trim($titleSuffix);
                $sourceHash  = md5($sourceTitle);

                $sourcesToInsert[] = '('
                    . $conn->quote($sourceTitle, 'string').', '
                    . $conn->quote($sourceHash, 'string').', '
                    . $conn->quote($params['unit'], 'string').', '
                    . $conn->quote($params['link'], 'string').', now(), now())';

                // values
                $values = array_slice($lineData, 1);

                $dataToInsert[ $sourceHash ] = array_combine($dates, $values);
            }
        }

        $now = date('Y-m-d H:m:s');

        // insert masivo de sources
        $r = $conn->executeUpdate('INSERT INTO source (title, hash, unit, link, created_at, updated_at)
            VALUES '.join(',', $sourcesToInsert));

        // get all sources
        $results = $conn->fetchAll("SELECT id, hash FROM source where created_at > ?", array($now));

        // create array that identifies source
        $sources = array();
        foreach ($results as $r) {
            $sources[ $r['hash'] ] = $r['id'];
        }
        unset($results);

        $insert = array();
        foreach ($dataToInsert as $sourceCode => $data) {

            foreach ($data as $date => $value ) {

                $dateAr = \Dagora\CoreBundle\Entity\Data::convertDate($date);

                $date     = $dateAr['date'];
                $dateType = $dateAr['dateType'];

                $insert[] = '('.$sources[ $sourceCode ].', '
                    .$conn->quote($date, 'string').', '
                    .$conn->quote($dateType, 'string').', '
                    .$conn->quote($value, 'string').', now(), now())';
            }
        }

        $offset = 0;
        $MAX    = 10000;
        $total  = count($insert);

        //$this->output->writeln('Hay '.$total.' data points');

        while ( $offset < $total ) {

            $insertNow = array_slice($insert, $offset, $MAX);

            // hacer insert masivo
            $r = $conn->executeUpdate('INSERT INTO data (source_id, date, date_type, value, created_at, updated_at)
                VALUES '.join(',', $insertNow));

            $offset += $MAX;

            //$this->output->writeln('  ...añadidos '.$offset);
        }
    }

    /**
     * Get the info of a source
     *
     * @ApiDoc(
     *  description="Get the info of a source",
     *  output="It returns a source",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when the source is not found"
     *  }
     * )
     *
     * @Route("/{source_id}.json", requirements={"source_id" = "\d+"},
     *  name="sources_show")
     * @Method({"GET"})
     *
     * @param int $source_id source Id
     */
    public function showAction($source_id)
    {
        // get source
        $source = $this->get('dlayer')->find('Source', array('id' => $source_id));

        // no object
        if ( !$source ) {
            throw $this->createNotFoundException('No source found');
        }

        return $this->returnShow($source);
    }

    /**
     * Private function that returns a source
     *
     * @param Source $source
     */
    private function returnShow($source)
    {
        $data = $this->get('dlayer')->findAll('Data', array(
            'source_id' => array($source->getId()),
        ));

        $response = $source->asApiArray(array(
            'data'            => $data
        ));

        return $this->returnJSON($response, 'source');
    }

    /**
     * Update a source
     *
     * @ApiDoc(
     *  description="Update a source",
     *  output="It returns a source",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when it is not found"
     *  }
     * )
     *
     * @Route("/{source_id}.json", requirements={"source_id" = "\d+"},
     *   name="sources_update")
     * @Method({"PUT"})
     *
     * @param int $source_id source Id
     */
    public function updateAction($source_id)
    {
        // disable
        $this->get('dlayer')->createAccessDeniedException();

        // get source
        $source = $this->get('dlayer')->find('source', array('id' => $source_id));

        // get parameters
        $params = json_decode($this->getRequest()->getContent(), true);

        // update source
        $source = $this->get('dlayer')->update($source, $params);

        return $this->returnShow($source);
    }

    /**
     * Delete a source
     *
     * @ApiDoc(
     *  description="Delete a source",
     *  output="Empty",
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when it is not found"
     *  }
     * )
     *
     * @Route("/{source_id}.json", requirements={"source_id" = "\d+"},
     *   name="sources_delete")
     * @Method({"DELETE"})
     *
     * @param int $source_id source Id
     */
    public function deleteAction($source_id)
    {
        // disable
        $this->get('dlayer')->createAccessDeniedException();

        // get source
        $source = $this->get('dlayer')->find('Source', array('id' => $source_id));

        // delete source
        $this->get('dlayer')->delete($source);

        return $this->returnJSON(array(), 'source');
    }
}
