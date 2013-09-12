<?php
namespace AlaroxFramework\traitement\controller;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\utils\ObjetReponse;
use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\restclient\RestClient;
use AlaroxFramework\utils\session\SessionClient;
use AlaroxFramework\utils\View;

abstract class GenericController
{
    /**
     * @var RestClient
     */
    private $_restClient;

    /**
     * @var SessionClient
     */
    private $_sessionClient;

    /**
     * @var array
     */
    private $_variablesRequete;

    /**
     * @var array
     */
    private $_variablesPost;

    /**
     * @var array
     */
    private $_beforeGenerateView = array();

    /**
     * @var AlaroxFile
     */
    private $_alaroxFile;

    public function __construct()
    {
        $this->_alaroxFile = new AlaroxFile();
    }

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
     * @return SessionClient
     */
    protected function getSession()
    {
        return $this->_sessionClient;
    }

    /**
     * @param $filePath
     * @return File
     */
    protected function getFile($filePath)
    {
        return $this->_alaroxFile->getFile($filePath);
    }

    /**
     * @param RestClient $restClient
     */
    public function setRestClient($restClient)
    {
        $this->_restClient = $restClient;
    }

    /**
     * @param SessionClient $sessionClient
     */
    public function setSessionClient($sessionClient)
    {
        $this->_sessionClient = $sessionClient;
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
     * @param string $clef
     * @param mixed $value
     */
    protected function addBeforeGenerateViewVariables($clef, $value)
    {
        $this->_beforeGenerateView[$clef] = $value;
    }

    /**
     * @param string $templateName
     * @return View
     */
    protected function generateView($templateName)
    {
        $view = new View();

        $view->renderView($templateName);

        foreach ($this->_beforeGenerateView as $key => $uneVariable) {
            $view->with($key, $uneVariable);
        }

        return $view;
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

    /**
     * @param string $url
     * @param int $codeHttp
     * @param bool $exit
     */
    protected function redirectToUrl($url, $codeHttp = 302, $exit = true)
    {
        header('Location: ' . $url);
        http_response_code($codeHttp);

        if ($exit === true) {
            // @codeCoverageIgnoreStart
            exit();
            // @codeCoverageIgnoreEnd
        }
    }
}