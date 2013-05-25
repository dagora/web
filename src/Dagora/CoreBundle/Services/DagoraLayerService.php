<?php

namespace Dagora\CoreBundle\Services;

use Doctrine\ORM\Mapping\ClassMetadata,
    Doctrine\Common\Util\Inflector,
    Doctrine\ORM\EntityManager,
    Oryzone\MediaStorage\MediaStorage,
    Symfony\Component\EventDispatcher,
    Symfony\Component\Templating\EngineInterface,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\Validator\Validator,
    TE\SearchifyBundle\Service\SearchifyService,
    AppKernel;

class DagoraLayerService
{
    /* Memcache service */
    protected $memcache;

    /* Searchify service */
    protected $searchify;

    /* Conexion to the database */
    protected $em;

    /* Validator */
    protected $validator;

    /* Entity Serializer */
    protected $entitySerializer;

    /**
     * @var Kernel
     */
    private $kernel;

    /* EventDispatcher */
    private $eventDispatcher;

    private $resultIds;

    /**
     * Construct
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, \Memcached $memcache, SearchifyService $searchify,
        Validator $validator, EntitySerializer $entitySerializer, AppKernel $kernel, $eventDispatcher )
    {
        $this->memcache         = $memcache;
        $this->searchify        = $searchify;
        $this->em               = $em;
        $this->validator        = $validator;
        $this->entitySerializer = $entitySerializer;
        $this->kernel           = $kernel;
        $this->eventDispatcher  = $eventDispatcher;
    }

    /**
     * Find an object
     *
     * @param string $entity
     * @param array $params
     *
     * @return
     */
    public function find($entity, $params) {

        return $this->em->getRepository('DagoraCoreBundle:'.$entity)->findOneBy($params);
    }

    /**
     * Find objects
     *
     * @param string $entity
     * @param array $params
     *
     * @return array
     */
    public function findAll($entity, $params) {

        // if we are searching, search in Searchify
        if ( isset($params['s']) && $params['s'] ) {

            // we have already searched on Searchify this same query
            if ( !$this->resultIds ) {
                $params['entity'] = $entity;
                $result = $this->search($params);

                $this->resultIds = array();
                foreach ( $result->results as $r ) {
                    $this->resultIds[] = (int) substr($r->docid, 1);
                }
            }

            // no results
            if ( count($this->resultIds) == 0 ) {
                return array();
            }

            $params = array(
                'id'    => $this->resultIds,
                'order' => 'ids'
            );
            $this->resultIds = array();
        }

        return $this->em->getRepository('DagoraCoreBundle:'.$entity)->findAllBy($params);
    }

    /**
     * Count number of objects
     *
     * @param string $entity
     * @param array $params
     *
     * @return array
     */
    public function count($entity, $params) {

        // if we are searching, search in Searchify
        if ( isset($params['s']) && $params['s'] ) {
            $params['entity'] = $entity;
            $result = $this->search($params);

            $this->resultIds = array();
            foreach ( $result->results as $r ) {
                $this->resultIds[] = (int) substr($r->docid, 1);
            }

            return (int) $result->matches;
        }
        // search in our db
        else
        {
            return $this->em->getRepository('DagoraCoreBundle:'.$entity)->count($params);
        }
    }

    /**
     * Search in Searchify
     *
     * @param  array $params
     * @return mixed
     */
    private function search($params) {

        $num         = isset($params['num']) ? $params['num'] : NULL;
        $s           = isset($params['s']) ? trim($params['s']) : NULL;
        $start       = isset($params['start']) ? $params['start'] : NULL;

        $s .= '*';

        $search = $this->searchify->setTerm($s);

        if ( $start ) {
            $search->setFirstResult($start);
        }

        if ( $num ) {
            $search->setMaxResults($num);
        }

        // order by relevance
        $search->setScoringFunction('relevance');

        return $search->getResults();
    }

    /**
     * Get values from a set of results
     *
     * @param  array   $objects
     * @param  string  $entity
     * @param  boolean $getId       Get the id of the object if true
     *
     * @return array
     */
    public function findValuesFrom($objects, $field, $getId=false) {

        $values = array();
        $method = 'get'.ucfirst($field);

        foreach ($objects as $o) {
            if ( $value = $o->$method() ) {
                $values[ $o->getId() ] = $getId ? $value->getId() : $value;
            }
        }

        return $values;
    }

    /**
     * Create an object
     *
     * @param  string   $entity
     * @param  array    $params
     * @param  boolean  $canChangePrivateFields
     *
     * @return object
     */
    public function create($entity, $params, $canChangePrivateFields=false) {

        // new object
        $class = 'Dagora\CoreBundle\Entity\\'.$entity;
        $object = new $class();

        // fill with related objects
        $params = $this->fillParams($entity, $params);

        // bind data
        $object->bind($params, $canChangePrivateFields);

        // validate params
        $errors = $this->validator->validate($object);

        // there has been validation errors
        if (count($errors) > 0) {
            throw $this->createBadRequestException("Error with object");
        }

        // // set object creator if given
        // if ( isset($params['created_by']) ) {
        //     $object->setCreatedBy($params['created_by']);
        //     $object->setUpdatedBy($params['created_by']);
        // }

        // create object
        $this->em->persist($object);
        $this->em->flush();

        // add object to searchify
        if ( 'Source' == $entity ) {
            $event = new \TE\SearchifyBundle\Event\ObjectEvent($object);
            $this->eventDispatcher->dispatch(\TE\SearchifyBundle\Event\SearchifyEvents::OBJECT_CREATE, $event);
        }

        return $object;
    }

    /**
     * Update object
     *
     * @param Object $object
     * @param array $params
     * @param  boolean $canChangePrivateFields
     *
     * @return object
     */
    public function update($object, $params, $canChangePrivateFields=false) {

        // no object
        if ( !$object ) {
            throw $this->createNotFoundException('Object not found');
        }

        // // check if the user has permission to edit it
        // if ( !$canChangePrivateFields ) {
        //     throw $this->createAccessDeniedException('You do not have permissions to edit it');
        // }

        $classNamespace = explode('\\', get_class($object));
        $entity         = array_pop($classNamespace);

        // fill with related objects
        $params = $this->fillParams($entity, $params, $object);

        // bind data
        $object->bind($params, $canChangePrivateFields);

        // validate params
        $errors = $this->validator->validate($object);

        // there has been validation errors
        if (count($errors) > 0) {
            throw $this->createBadRequestException("Error with object");
        }

        // update object
        $this->em->persist($object);
        $this->em->flush();

        // add object to searchify
        if ( 'Source' == $entity ) {
            $event = new \TE\SearchifyBundle\Event\ObjectEvent($object);
            $this->eventDispatcher->dispatch(\TE\SearchifyBundle\Event\SearchifyEvents::OBJECT_UPDATE, $event);
        }

        return $object;
    }

    /**
     * Delete object
     *
     * @param string $object
     */
    public function delete($object) {

        // no object, so it has been already deleted :)
        if ( !$object ) {
            return;
        }

        // // the user has no permission to delete it
        // if ( !$object->canBeDeletedBy($this->getUser()) ) {
        //     throw $this->createAccessDeniedException('You do not have permissions to delete it');
        // }

        $classNamespace = explode('\\', get_class($object));
        $entity         = array_pop($classNamespace);

        // remove from Searchify - we need the object to be alived
        if ( in_array($entity, array('Source') ) ) {
            $event = new \TE\SearchifyBundle\Event\ObjectEvent($object);
            $this->eventDispatcher->dispatch(\TE\SearchifyBundle\Event\SearchifyEvents::OBJECT_REMOVE, $event);
        }

        // remove object
        $this->em->remove($object);
        $this->em->flush();

        // // launch background task to remove the related objects
        // if ( in_array($entity, array('Place', 'Wishlist', 'Trip', 'User', 'Tip')) ) {

        //     $this->createTask('te:'.strtolower($entity).':remove', array($object->getId()));
        // }
    }

    /**
     * Create task to launch in background
     * @param  string $name
     * @param  array  $params
     */
    public function createTask($name, $params) {
        $job = new \JMS\JobQueueBundle\Entity\Job($name, $params);
        $this->em->persist($job);
        $this->em->flush();
    }

    /**
     * Fill the params array with related objects
     *
     * @param  string $entity
     * @param  array  $params
     * @param  object $object
     * @return array
     */
    private function fillParams($entity, $params, $object=null)
    {
        $function = 'fillParamsOf'.$entity;
        return $this->$function($params, $object);
    }

    /**
     * Fill the params array of a Source with related objects
     *
     * @param  array  $params
     * @param  object $object
     * @return array
     */
    private function fillParamsOfSource($params, $object)
    {
        // get title
        if ( !isset($params['title']) || !$params['title'] ) {
            throw $this->createBadRequestException('No title given');
        }

        // get unit
        if ( !isset($params['unit']) || !$params['unit'] ) {
            throw $this->createBadRequestException('No unit given');
        }

        return $params;
    }

    /**
     * Fill the params array of a Data with related objects
     *
     * @param  array  $params
     * @param  object $object
     * @return array
     */
    private function fillParamsOfData($params, $object)
    {
        if ( !$object && !isset($params['source']) ) {

            // get source
            if ( !isset($params['source_id']) ) {
                throw $this->createBadRequestException('No source_id given');
            }

            $source = $this->find('Source', array('id' => $params['source_id']));

            // no object
            if ( !$source ) {
                throw $this->createNotFoundException('No source found');
            }

            $params['source'] = $source;
            unset($params['source_id']);
        }

        // get data
        if ( !isset($params['value']) || !$params['value'] ) {
            throw $this->createBadRequestException('No value given');
        }

        // get unit
        if ( !isset($params['date']) || !$params['date'] ) {
            throw $this->createBadRequestException('No date given');
        }

        return $params;
    }

    /**
     * Returns a BadRequestHttpException.
     *
     * This will result in a 400 response code. Usage example:
     *
     *     throw $this->createBadRequestException('A param is needed!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return BadRequestHttpException
     */
    public function createBadRequestException($message = 'Bad Request', \Exception $previous = null)
    {
        return new \Dagora\CoreBundle\Utilities\Exception\BadRequestHttpException($message, $previous);
    }

    /**
     * Returns a UnauthorizedHttpException.
     *
     * This will result in a 401 response code. Usage example:
     *
     *     throw $this->createUnauthorizedException('The auth token is incorrect!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return UnauthorizedHttpException
     */
    public function createUnauthorizedException($message = 'The auth token is incorrect', \Exception $previous = null)
    {
        return new \Dagora\CoreBundle\Utilities\Exception\UnauthorizedHttpException($message, $previous);
    }

    /**
     * Returns a AccessDeniedHttpException.
     *
     * This will result in a 403 response code. Usage example:
     *
     *     throw $this->createAccessDeniedException('You do not have permissions');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return AccessDeniedHttpException
     */
    public function createAccessDeniedException($message = 'You do not have permissions', \Exception $previous = null)
    {
        return new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($message, $previous);
    }

    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return NotFoundHttpException
     */
    public function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException($message, $previous);
    }

    /**
     * Returns a UnauthorizedHttpException.
     *
     * This will result in a 401 response code. Usage example:
     *
     *     throw $this->createUnauthorizedException('The auth token is incorrect!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return UnauthorizedHttpException
     */
    public function createDuplicatedException($message = 'The object already exists', \Exception $previous = null)
    {
        return new \Dagora\CoreBundle\Utilities\Exception\DuplicatedHttpException($message, $previous);
    }

    /**
     * Returns a HttpException.
     *
     * This will result in a 500 response code. Usage example:
     *
     *     throw $this->createException('There has been an error');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return HttpException
     */
    public function createException($message = 'There has been an error', \Exception $previous = null)
    {
        return new \Symfony\Component\HttpKernel\Exception\HttpException(500, $message, $previous);
    }

}