<?php
namespace AlaroxFramework\traitement\controller;

use AlaroxFramework\utils\ObjetReponse;
use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\View;
use AlaroxFramework\utils\restclient\RestClient;

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
     * @var array
     */
    private $_variablesPost;

    /**
     * @return array
     */
    protected function getVariablesRequete()
    {
        return $this->_variablesRequete;
    }

    /**
     * @param string $clef
     * @return string|null
     */
    protected function getUneVariableRequete($clef)
    {
        if (array_key_exists($clef, $this->_variablesRequete)) {
            return $this->_variablesRequete[$clef];
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    protected function getVariablesPost()
    {
        return $this->_variablesPost;
    }

    /**
     * @param string $clef
     * @return string|null
     */
    protected function getUneVariablePost($clef)
    {
        if (array_key_exists($clef, $this->_variablesPost)) {
            return $this->_variablesPost[$clef];
        } else {
            return null;
        }
    }

    /**
     * @param RestClient $restClient
     */
    public function setRestClient($restClient)
    {
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

    /**
     * @param array $tabPostVars
     */
    public function setVariablesPost($tabPostVars)
    {
        $this->_variablesPost = $tabPostVars;
    }

    /**
     * @param string $templateName
     * @return View
     */
    protected function generateView($templateName)
    {
        $view = new View();

        return $view->renderView($templateName);
    }

    /**
     * @param string $serverName
     * @param ObjetRequete $requestObject
     * @return ObjetReponse
     */
    protected function executeRequest($serverName, $requestObject)
    {
        return $this->_restClient->executerRequete($serverName, $requestObject);
    }
}