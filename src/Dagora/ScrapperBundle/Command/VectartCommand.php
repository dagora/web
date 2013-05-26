<?php

namespace Dagora\ScrapperBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VectartCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dagora:import:vectart')
            ->setDescription('Import Vectart')
        ;
    }

    // entity manager
    private $em     = null;
    private $conn   = null;
    private $dlayer = null;
    private $output = null;

    private $cities = array();

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get access to services
        $this->output = $output;
        $this->em     = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->conn   = $this->getContainer()->get('doctrine.dbal.default_connection');
        $this->dlayer = $this->getContainer()->get('dlayer');

        // disable sql logger (avoid memory problems)
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->output->writeln('Importing vectart');

        // get cities
        $this->getCities();

        $this->importDensidad();
        $this->importPoblacion();
        $this->importPresupuestos();
    }

    /**
     * Get cities
     */
    private function getCities() {

        $results = $this->conn->fetchAll("SELECT id, title, vectart_code FROM city");

        foreach ($results as $r) {
            $this->cities[ $r['vectart_code'] ] = array(
                'id'           => $r['id'],
                'title'        => $r['title']
            );
        }

        $this->output->writeln('Leyendo ciudades de la DB...');
    }

    /**
     * Import densidad
     */
    private function importDensidad() {

        $results = $this->conn->fetchAll("SELECT * FROM vectart.demo_densidad");

        $this->output->writeln('Importando densidad de población...');

        $sourcesToInsert = array();
        $dataToInsert    = array();
        foreach ($results as $r) {

            $cityId    = $this->cities[ $r['Codigo'] ]['id'];
            $cityTitle = $this->cities[ $r['Codigo'] ]['title'];

            //$this->output->writeln( $cityTitle);

            $sourceTitle       = "Densidad de población de ".$cityTitle;
            $sourcesToInsert[] = '('.$cityId.', "'.$sourceTitle.'", "'.md5($sourceTitle).'", "personas/km^2", "http://data.vectart.com/", now(), now())';
            $sourceCode        = md5($sourceTitle);
            unset($r['Codigo']);

            foreach ($r as $year => $value) {

                // init array
                if ( !isset($dataToInsert[ $sourceCode ]) ) {
                    $dataToInsert[ $sourceCode ] = array();
                }

                // there may be not be a value
                if ( $value ) {
                    $dataToInsert[ $sourceCode ][] = array(
                        'year'  => $year,
                        'value' => $value
                    );
                }
            }

            unset($r);
            unset($source);
        }

        $this->insert($sourcesToInsert, $dataToInsert);
    }

    /**
     * Import población
     */
    private function importPoblacion() {

        $results = $this->conn->fetchAll("SELECT * FROM vectart.demo_poblacion");

        $this->output->writeln('Importando población...');

        $sourcesToInsert = array();
        $dataToInsert    = array();
        foreach ($results as $r) {

            $cityId    = $this->cities[ $r['Codigo'] ]['id'];
            $cityTitle = $this->cities[ $r['Codigo'] ]['title'];

            //$this->output->writeln( $cityTitle);

            $sourceTitle       = "Población - Número de habitantes de ".$cityTitle;
            $sourcesToInsert[] = '('.$cityId.', "'.$sourceTitle.'", "'.md5($sourceTitle)
                .'", "personas", "http://data.vectart.com/", now(), now())';
            $sourceCodeTotal   = md5($sourceTitle);

            $sourceTitle       = "Población - Número de hombres de ".$cityTitle;
            $sourcesToInsert[] = '('.$cityId.', "'.$sourceTitle.'", "'.md5($sourceTitle)
                .'", "personas", "http://data.vectart.com/", now(), now())';
            $sourceCodeHombres = md5($sourceTitle);

            $sourceTitle       = "Población - Número de mujeres de ".$cityTitle;
            $sourcesToInsert[] = '('.$cityId.', "'.$sourceTitle.'", "'.md5($sourceTitle)
                .'", "personas", "http://data.vectart.com/", now(), now())';
            $sourceCodeMujeres = md5($sourceTitle);

            unset($r['Codigo']);
            foreach ($r as $key => $value) {

                $year = substr($key, 0, 4);

                switch ( substr($key, 5, 1) ) {
                    case 'T':
                        $sourceCode = $sourceCodeTotal;
                        break;

                    case 'V':
                        $sourceCode = $sourceCodeHombres;
                        break;

                    case 'M':
                        $sourceCode = $sourceCodeMujeres;
                        break;
                }

                // init array
                if ( !isset($dataToInsert[ $sourceCode ]) ) {
                    $dataToInsert[ $sourceCode ] = array();
                }

                // there may be not be a value
                if ( $value && $value != 'N.E.' ) {
                    $dataToInsert[ $sourceCode ][] = array(
                        'year'  => $year,
                        'value' => $value
                    );
                }
            }

            unset($r);
            unset($source);
        }

        $this->insert($sourcesToInsert, $dataToInsert);
    }

    /**
     * Import presupuestos
     */
    private function importPresupuestos() {

        $this->output->writeln('Importando PRESUPUESTOS');

        $capitulos = array(
            'i1' => "Ingresos - Capitulo I - Impuestos directos - ",
            'i2' => "Ingresos - Capitulo II - Impuestos indirectos - ",
            'i3' => "Ingresos - Capitulo III - Tasas y otros ingresos - ",
            'i4' => "Ingresos - Capitulo IV - Transferencias corrientes - ",
            'i5' => "Ingresos - Capitulo V - Ingresos patrimoniales - ",
            'i6' => "Ingresos - Capitulo VI - Enajenación de inversiones reales - ",
            'i7' => "Ingresos - Capitulo VII - Transferencias de capital - ",
            'i8' => "Ingresos - Capitulo VIII - Activos financieros - ",
            'i9' => "Ingresos - Capitulo IX - Pasivos financieros - ",
            'iT' => "Ingresos Totales en ",
            'g1' => "Gastos - Capitulo I - Gastos de personal - ",
            'g2' => "Gastos - Capitulo II - Gastos en bienes corrientes y servicios - ",
            'g3' => "Gastos - Capitulo III - Gastos financieros (intereses) - ",
            'g4' => "Gastos - Capitulo IV - Transferencias corrientes - ",
            //'g5' => "Gastos - Capitulo V - Fondo de Contingencia presupuestaria - ",
            'g6' => "Gastos - Capitulo VI - Inversiones reales - ",
            'g7' => "Gastos - Capitulo VII - Transferencias de capital - ",
            'g8' => "Gastos - Capitulo VIII - Activos financieros - ",
            'g9' => "Gastos - Capitulo IX - Pasivos financieros - ",
            'gT' => "Gastos Totales en "
        );

        // create sources first
        foreach ($this->cities as $key => $city) {

            $cityId    = $city['id'];
            $cityTitle = $city['title'];
            $titles = array(
                $capitulos['i1'] . $cityTitle,
                $capitulos['i2'] . $cityTitle,
                $capitulos['i3'] . $cityTitle,
                $capitulos['i4'] . $cityTitle,
                $capitulos['i5'] . $cityTitle,
                $capitulos['i6'] . $cityTitle,
                $capitulos['i7'] . $cityTitle,
                $capitulos['i8'] . $cityTitle,
                $capitulos['i9'] . $cityTitle,
                $capitulos['iT'] . $cityTitle,
                $capitulos['g1'] . $cityTitle,
                $capitulos['g2'] . $cityTitle,
                $capitulos['g3'] . $cityTitle,
                $capitulos['g4'] . $cityTitle,
                //$capitulos['g5'] . $cityTitle,
                $capitulos['g6'] . $cityTitle,
                $capitulos['g7'] . $cityTitle,
                $capitulos['g8'] . $cityTitle,
                $capitulos['g9'] . $cityTitle,
                $capitulos['gT'] . $cityTitle,
            );

            foreach ($titles as $title) {
                $sourcesToInsert[] = '('.$cityId.', "'.$title.'", "'.md5($title).'", "€", '
                    .'"http://data.vectart.com/", now(), now())';
            }
        }

        $this->insert($sourcesToInsert, array());
        unset($sourcesToInsert);

        // get data
        $years = array(2000,2001,2002,2003,2004,2005,2006,2007,2008,2009,2010);

        foreach ( $years as $year ) {

            $dataToInsert = array();

            $results = $this->conn->fetchAll("SELECT * FROM vectart.eco_muni_presupuestos".$year);

            $this->output->writeln('Importando presupuestos año '.$year.'...');

            foreach ($results as $r) {

                $cityId    = $this->cities[ $r['Codigo'] ]['id'];
                $cityTitle = $this->cities[ $r['Codigo'] ]['title'];

                //$this->output->writeln( $cityTitle);

                $titles = array(
                    'sourceCodeIngresos1'       => $capitulos['i1'] . $cityTitle,
                    'sourceCodeIngresos2'       => $capitulos['i2'] . $cityTitle,
                    'sourceCodeIngresos3'       => $capitulos['i3'] . $cityTitle,
                    'sourceCodeIngresos4'       => $capitulos['i4'] . $cityTitle,
                    'sourceCodeIngresos5'       => $capitulos['i5'] . $cityTitle,
                    'sourceCodeIngresos6'       => $capitulos['i6'] . $cityTitle,
                    'sourceCodeIngresos7'       => $capitulos['i7'] . $cityTitle,
                    'sourceCodeIngresos8'       => $capitulos['i8'] . $cityTitle,
                    'sourceCodeIngresos9'       => $capitulos['i9'] . $cityTitle,
                    'sourceCodeIngresosTotales' => $capitulos['iT'] . $cityTitle,
                    'sourceCodeGastos1'         => $capitulos['g1'] . $cityTitle,
                    'sourceCodeGastos2'         => $capitulos['g2'] . $cityTitle,
                    'sourceCodeGastos3'         => $capitulos['g3'] . $cityTitle,
                    'sourceCodeGastos4'         => $capitulos['g4'] . $cityTitle,
                    //'sourceCodeGastos5'         => $capitulos['g5'] . $cityTitle,
                    'sourceCodeGastos6'         => $capitulos['g6'] . $cityTitle,
                    'sourceCodeGastos7'         => $capitulos['g7'] . $cityTitle,
                    'sourceCodeGastos8'         => $capitulos['g8'] . $cityTitle,
                    'sourceCodeGastos9'         => $capitulos['g9'] . $cityTitle,
                    'sourceCodeGastosTotales'   => $capitulos['gT'] . $cityTitle
                );

                foreach ($titles as $key => $sourceTitle) {
                    $$key       = md5($sourceTitle);
                }

                unset($r['Codigo']);
                foreach ($r as $key => $value) {

                    if ( 'I' == substr($key, 0, 1) ) {
                        $cap        = substr($key, 12);
                        $varName    = 'sourceCodeIngresos'.$cap;
                        $sourceCode = $$varName;
                    }
                    else if ( 'Total_Ingresos' == $key ) {
                        $varName    = 'sourceCodeIngresosTotales';
                        $sourceCode = $$varName;
                    }
                    else if ( 'G' == substr($key, 0, 1) ) {
                        $cap        = substr($key, 10);
                        $varName    = 'sourceCodeGastos'.$cap;
                        $sourceCode = $$varName;
                    }
                    else if ( 'Total_Gastos' == $key ) {
                        $varName    = 'sourceCodeGastosTotales';
                        $sourceCode = $$varName;
                    }

                    // init array
                    if ( !isset($dataToInsert[ $sourceCode ]) ) {
                        $dataToInsert[ $sourceCode ] = array();
                    }

                    // there may be not be a value
                    if ( $value && $value != 'N.E.' ) {
                        $dataToInsert[ $sourceCode ][] = array(
                            'year'  => $year,
                            'value' => $value
                        );
                    }
                }

                unset($r);
                unset($source);
            }

            $this->insert(array(), $dataToInsert);

            $sourcesCreated = true;
        }
    }

    /**
     * Insert multiple sources
     *
     * @param  array $sourcesToInsert
     * @param  array $dataToInsert
     */
    private function insert($sourcesToInsert, $dataToInsert) {

        $offset = 0;
        $MAX    = 5000;
        $total  = count($sourcesToInsert);

        $this->output->writeln('Hay '.$total.' fuentes');

        while ( $offset < $total ) {

            $insertNow = array_slice($sourcesToInsert, $offset, $MAX);

            // insert masivo de sources
            $r = $this->conn->executeUpdate('INSERT INTO source (city_id, title, hash, unit, link, created_at, updated_at)
                VALUES '.join(',', $insertNow));

            $offset += $MAX;

            $this->output->writeln('  ...añadidas '.$offset);

            unset($r);
        }
        unset($sourcesToInsert);

        // get all sources
        $results = $this->conn->fetchAll("SELECT id, hash FROM source");

        // create array that identifies source
        $sources = array();
        foreach ($results as $r) {
            $sources[ $r['hash'] ] = $r['id'];
        }
        unset($results);

        $insert = array();
        foreach ($dataToInsert as $sourceCode => $data) {

            foreach ($data as $d ) {

                $insert[] = '('.$sources[ $sourceCode ].', "'.$d['year'].'-01-01", "y", "'
                    .$d['value'].'", now(), now())';
            }
        }

        $offset = 0;
        $MAX    = 10000;
        $total  = count($insert);

        $this->output->writeln('Hay '.$total.' data points');

        while ( $offset < $total ) {

            $insertNow = array_slice($insert, $offset, $MAX);

            // hacer insert masivo
            $r = $this->conn->executeUpdate('INSERT INTO data (source_id, date, date_type, value, created_at, updated_at)
                VALUES '.join(',', $insertNow));

            $offset += $MAX;

            $this->output->writeln('  ...añadidos '.$offset);
        }

    }

}