<?php

namespace Dagora\CoreBundle\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpFoundation\Response;

/**
 * Base controller to manage common functions
 */
class Controller extends BaseController
{
    // Contains the query parameters
    private $params = null;

    /**
     * Return a JSON response
     *
     * @param   array     $data
     * @param   string    $dataType
     * @param   string    $cacheKeyPrefix
     * @return  Response
     */
    public function returnJSON($data=array(), $dataType='', $cacheKeyPrefix=null)
    {
        $responseData =  array(
            'status'   => 200,
            'message'  => '',
            'dataType' => $dataType,
            'data'     => $data
        );

        // build response
        $response = new JsonResponse($responseData);

        // save response in cache
        // if ( $cacheKeyPrefix && 'GET' == $this->getRequest()->getMethod() ) {

        //     $lastModified = time();

        //     // save in cache
        //     $this->saveInCache($cacheKeyPrefix, $lastModified, $responseData);

        //     // set response attributes
        //     $response->setPrivate();
        //     $response->setCache(array(
        //         'last_modified' => new \DateTime('@'.$lastModified),
        //         'max_age'       => 3600,
        //         's_maxage'      => 3600
        //     ));
        // }

        return $response;
    }

    /**
     * Function to cache some data
     * @param  string $cacheKeyPrefix
     * @param  int $lastModified
     * @param  string $responseData
     */
    public function saveInCache($cacheKeyPrefix, $lastModified, $responseData)
    {
        // save if cache enabled
        if ( $this->container->getParameter('cache_enabled') ) {

            // save the last time the response was modified
            $this->get('memcached')->set($cacheKeyPrefix.'_date', $lastModified, 3600);

            // save the response for 1 hour
            $cacheKey = $this->getCacheKey($cacheKeyPrefix, $this->getQueryParameters(), $lastModified);
            $this->get('memcached')->set($cacheKey, $responseData, 3600);
        }
    }

    /**
     * Return a HTML response
     *
     * @param   string    $template
     * @param   array     $data
     * @param   string    $cacheKeyPrefix
     * @param  array $extraParams More params to take into account on cache key
     * @return  Response
     */
    public function returnHTML($template, $data, $cacheKeyPrefix=null, $extraParams=array())
    {
        // build response
        $response = $this->render($template, $data);

        // save response in cache
        if ( $cacheKeyPrefix && 'GET' == $this->getRequest()->getMethod() ) {

            $lastModified = time();

            // save if cache enabled
            if ( $this->container->getParameter('cache_enabled') ) {

                // save the last time the response was modified
                $this->get('memcached')->set($cacheKeyPrefix.'_date', $lastModified, 3600);

                // save the response for 1 hour
                $params = $this->getQueryParameters();
                $params = array_merge($params, $extraParams);
                $cacheKey = $this->getCacheKey($cacheKeyPrefix, $params, $lastModified);
                $this->get('memcached')->set($cacheKey, $response->getContent(), 3600);
            }

            // everything get the same HTML
            $response->setPublic();
            $response->setCache(array(
                'last_modified' => new \DateTime('@'.$lastModified),
                'max_age'       => 3600,
                's_maxage'      => 3600
            ));
        }

        return $response;
    }

    /**
     * Get the cached response if is valid
     *
     * @param  string $keyPrefix
     * @param  array $extraParams More params to take into account on cache key
     * @return Response
     */
    public function getCachedResponse($keyPrefix, $extraParams=array()) {

        // check if cache enabled
        if ( !$this->container->getParameter('cache_enabled') ) return null;

        // get last modified date
        if ( $lastModified = $this->get('memcached')->get($keyPrefix.'_date') ) {

            $response = 'json' == $this->getFormat() ? new JsonResponse() : new Response();
            $response->setLastModified( new \DateTime('@'.$lastModified) );

            // if the response hasn't changed return the 304 Response immediately
            if ( $response->isNotModified($this->getRequest())) {
                return $response;
            }

            // return the cached response data
            $params = $this->getQueryParameters();
            $params = array_merge($params, $extraParams);
            if ( $data = $this->get('memcached')->get($this->getCacheKey($keyPrefix, $params, $lastModified) ) ) {

                if ( 'json' == $this->getFormat() ) {
                    $response->setData($data);
                }
                else {
                    $response->setContent($data);
                }

                $response->setPrivate();

                $response->setCache(array(
                    'max_age'       => 3600,
                    's_maxage'      => 3600,
                ));

                return $response;
            }
        }

        return null;
    }

    /**
     * Get the cached data if is valid
     *
     * @param  string $keyPrefix
     * @return Response
     */
    public function getCachedData($keyPrefix) {

        // check if cache enabled
        if ( !$this->container->getParameter('cache_enabled') ) return null;

        // get last modified date
        if ( $lastModified = $this->get('memcached')->get($keyPrefix.'_date') ) {

            // return the cached response data
            $params = $this->getQueryParameters();
            if ( $data = $this->get('memcached')->get($this->getCacheKey($keyPrefix, $params, $lastModified) ) ) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Return the cache key to be used
     *
     * @param  string   $keyPrefix
     * @param  array    $params
     * @param  int      $lastModified
     * @return string
     */
    public function getCacheKey($keyPrefix, $params, $lastModified) {

        return $keyPrefix . '_' . substr(md5(join('_', $params)), 0, 8) . '_' . $lastModified.'_'.$this->getFormat();
    }

    /**
     * Return the response format
     * @return string html or json
     */
    public function getFormat() {

        return strpos($this->getRequest()->attributes->get('_controller'), 'WebBundle')
            && !$this->expectsJsonResponse()
            ? 'html'
            : 'json';
    }

    /**
     * Return only the parameters needed for the request
     *
     * @return array
     */
    public function getQueryParameters() {

        if ( count($this->params) > 0 ) return $this->params;

        $params = $this->getRequest()->query->all();

        if ( !isset($params['start']) ) $params['start'] = 0;

        ksort($params);
        $this->params = $params;

        return $params;
    }

}
