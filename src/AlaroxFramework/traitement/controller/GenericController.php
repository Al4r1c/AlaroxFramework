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
     * @var array
     */
    private $_variablesRequete;

    /**
     * @return RestClient
     */
    protected function getRestClient()
    {
        return $this->_restClient;
    }

    /**
     * @return array
     */
    protected function getVariablesRequete()
    {
        return $this->_variablesRequete;
    }

    /**
     * @param RestClient $restClient
     * @throws \InvalidArgumentException
     */
    public function setRestClient($restClient)
    {
        if (!$restClient instanceof RestClient) {
            throw new \InvalidArgumentException('Expected parameter 1 restClient to be RestClient.');
        }

        $this->_restClient = $restClient;
    }

    /**
     * @param array $tabVariables
     * @throws \InvalidArgumentException
     */
    public function setVariablesRequete($tabVariables)
    {
        if (!is_array($tabVariables)) {
            throw new \InvalidArgumentException('Expected parameter 1 tabVariables to be array.');
        }

        $this->_variablesRequete = $tabVariables;
    }
}