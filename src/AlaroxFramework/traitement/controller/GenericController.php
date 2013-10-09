<?php
namespace AlaroxFramework\traitement\controller;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\traitement\NotFoundException;
use AlaroxFramework\utils\ObjetReponse;
use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\restclient\RestClient;
use AlaroxFramework\utils\session\SessionClient;
use AlaroxFramework\utils\view\AbstractView;
use AlaroxFramework\utils\view\PlainView;
use AlaroxFramework\utils\view\TemplateView;
use AlaroxFramework\utils\view\ViewFactory;

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
     * @var ViewFactory
     */
    private $_viewFactory;

    /**
     * @var array
     */
    private $_variablesUri;

    /**
     * @var array
     */
    private $_variablesRequete;

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
    protected function getVariablesUri()
    {
        return $this->_variablesUri;
    }

    /**
     * @param string $clef
     * @return string|null
     */
    protected function getUneVariableUri($clef)
    {
        if (array_key_exists($clef, $this->_variablesUri)) {
            return $this->_variablesUri[$clef];
        } else {
            return null;
        }
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
     * @return SessionClient
     */
    protected function getSession()
    {
        return $this->_sessionClient;
    }

    /**
     * @return AlaroxFile
     */
    protected function getAlaroxFile()
    {
        return $this->_alaroxFile;
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
     * @param ViewFactory $viewFactory
     */
    public function setViewFactory($viewFactory)
    {
        $this->_viewFactory = $viewFactory;
    }

    /**
     * @param array $tabVariables
     * @throws \InvalidArgumentException
     */
    public function setVariablesUri($tabVariables)
    {
        if (!is_array($tabVariables)) {
            throw new \InvalidArgumentException('Expected parameter 1 tabVariables to be array.');
        }

        $this->_variablesUri = $tabVariables;
    }

    /**
     * @param array $tabQueryVars
     */
    public function setVariablesRequete($tabQueryVars)
    {
        $this->_variablesRequete = $tabQueryVars;
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
     * @return PlainView
     */
    protected function generatePlainView($templateName)
    {
        return $this->generateTemplate($templateName, 'plain');
    }

    /**
     * @param string $templateName
     * @return TemplateView
     */
    protected function generateTemplateView($templateName)
    {
        return $this->generateTemplate($templateName, 'template');
    }

    /**
     * @param string $renderContent
     * @param string $viewType
     * @return AbstractView
     */
    private function generateTemplate($renderContent, $viewType)
    {
        $view = $this->_viewFactory->getView($viewType);

        $view->renderView($renderContent);

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

    /**
     * @throws NotFoundException
     */
    protected function send404()
    {
        throw new NotFoundException();
    }
}