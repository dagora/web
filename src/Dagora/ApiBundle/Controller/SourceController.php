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
        // get parameters
        $params = json_decode($this->getRequest()->getContent(), true);

        // create source
        $source = $this->get('dlayer')->create('Source', $params);

        if ( isset($params['data']) ) {

            foreach ($params['data'] as $data) {
                $data['source'] = $source;
                $this->get('dlayer')->create('Data', $data);
            }
        }

        return $this->returnShow($source, false);
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
        // get source
        $source = $this->get('dlayer')->find('Source', array('id' => $source_id));

        // delete source
        $this->get('dlayer')->delete($source);

        return $this->returnJSON(array(), 'source');
    }
}
