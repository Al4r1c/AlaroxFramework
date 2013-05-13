<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\configs\Server;
use AlaroxFramework\cfg\configs\TemplateConfig;
use AlaroxFramework\cfg\i18n\Internationalization;
use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\utils\restclient\RestClient;

class Config
{
    /**
     * @var string
     */
    private $_version;

    /**
     * @var RouteMAp
     */
    private $_routeMap;

    /**
     * @var ControllerFactory
     */
    private $_ctrlFactory;

    /**
     * @var RestClient
     */
    private $_restClient;

    /**
     * @var Internationalization
     */
    private $_i18nConfig;

    /**
     * @var Server
     */
    private $_server;

    /**
     * @var TemplateConfig
     */
    private $_templateConfig;

    /**
     * @var array
     */
    private static $valeursMinimales = array(
        'Website_version',
        'RestServer',
        'TemplateConfig',
        'InternationalizationConfig',
        'InternationalizationConfig.Enabled',
        'InternationalizationConfig.Default_language',
        'InternationalizationConfig.Available',
        'TemplateConfig.Cache',
        'TemplateConfig.Charset',
        'TemplateConfig.Variables',
        'TemplateConfig.Variables.Static',
        'TemplateConfig.Variables.Remote'
    );

    /**
     * @return Internationalization
     */
    public function getI18nConfig()
    {
        return $this->_i18nConfig;
    }

    /**
     * @return ControllerFactory
     */
    public function getCtrlFactory()
    {
        return $this->_ctrlFactory;
    }

    /**
     * @return RestClient
     */
    public function getRestClient()
    {
        return $this->_restClient;
    }

    /**
     * @return RouteMap
     */
    public function getRouteMap()
    {
        return $this->_routeMap;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * @return TemplateConfig
     */
    public function getTemplateConfig()
    {
        return $this->_templateConfig;
    }

    /**
     * @return boolean
     */
    public function isProdVersion()
    {
        return strcmp(strtolower($this->_version), 'prod') == 0;
    }

    /**
     * @param ControllerFactory $controllerFactory
     * @throws \InvalidArgumentException
     */
    public function setCtrlFactory($controllerFactory)
    {
        if (!$controllerFactory instanceof ControllerFactory) {
            throw new \InvalidArgumentException('Expected parameter 1 controllerFactory to be instance of ControllerFactory.');
        }

        $this->_ctrlFactory = $controllerFactory;
    }

    /**
     * @param Internationalization $i18n
     * @throws \InvalidArgumentException
     */
    public function setI18nConfig($i18n)
    {
        if (!$i18n instanceof Internationalization) {
            throw new \InvalidArgumentException('Expected parameter 1 i18n to be instance of Internationalization.');
        }

        $this->_i18nConfig = $i18n;
    }

    /**
     * @param RouteMap $routeMap
     * @throws \InvalidArgumentException
     */
    public function setRouteMap($routeMap)
    {
        if (!$routeMap instanceof RouteMap) {
            throw new \InvalidArgumentException('Expected parameter 1 routeMap to be instance of RouteMap.');
        }

        $this->_routeMap = $routeMap;
    }

    /**
     * @param RestClient $restClient
     * @throws \InvalidArgumentException
     */
    public function setRestClient($restClient)
    {
        if (!$restClient instanceof RestClient) {
            throw new \InvalidArgumentException('Expected parameter 1 restClient to be instance of RestClient.');
        }

        $this->_restClient = $restClient;
    }

    /**
     * @param Server $server
     * @throws \InvalidArgumentException
     */
    public function setServer($server)
    {
        if (!$server instanceof Server) {
            throw new \InvalidArgumentException('Expected parameter 1 server to be instance of Server.');
        }

        $this->_server = $server;
    }

    /**
     * @param TemplateConfig $templateConfig
     * @throws \InvalidArgumentException
     */
    public function setTemplateConfig($templateConfig)
    {
        if (!$templateConfig instanceof TemplateConfig) {
            throw new \InvalidArgumentException('Expected parameter 1 templateConfig to be instance of TemplateConfig.');
        }

        $this->_templateConfig = $templateConfig;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->_version = $version;
    }

    /**
     * @param File $fichier
     * @return array
     * @throws \Exception
     */
    public function validerEtChargerFichier($fichier)
    {
        if ($fichier->fileExist() === true) {
            $tabCfg = $fichier->loadFile();
        } else {
            throw new \Exception(sprintf('Config file %s does not exist.', $fichier->getPathToFile()));
        }

        foreach (self::$valeursMinimales as $uneValeurMinimale) {
            if (is_null(array_multisearch($uneValeurMinimale, $tabCfg, true))) {
                throw new \Exception(sprintf('Missing config key "%s".', $uneValeurMinimale));
            }
        }

        return $tabCfg;
    }
}