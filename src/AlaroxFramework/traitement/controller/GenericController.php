<?php
namespace AlaroxFramework\traitement\controller;

use AlaroxFramework\traitement\restclient\RestClient;

abstract class GenericController
{
    /**
     * @var RestClient
     */
    private $_restClient;

    /**
     * @return RestClient
     */
    protected function getRestClient()
    {
        return $this->_restClient;
    }

    /**
     * @param RestClient $restClient
     */
    public function setRestClient($restClient)
    {
        $this->_restClient = $restClient;
    }
}