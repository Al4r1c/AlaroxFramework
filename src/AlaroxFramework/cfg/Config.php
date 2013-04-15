<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\configs\RestInfos;
use AlaroxFramework\cfg\configs\Server;
use AlaroxFramework\cfg\route\RouteMap;

class Config
{
    /**
     * @var array
     */
    private $_tabConfiguration = array();

    /**
     * @var array
     */
    private static $valeursMinimales = array(
        'Website_version',
        'RestServer',
        'TemplateVars',
        'InternationalizationConfig',
        'RestServer.Url',
        'RestServer.Format',
        'RestServer.Authentification',
        'RestServer.Authentification.Enabled',
        'RestServer.Authentification.Username',
        'RestServer.Authentification.PassKey',
        'InternationalizationConfig.Enabled',
        'InternationalizationConfig.Default_language',
        'InternationalizationConfig.Available');

    /**
     * @param RouteMap $routeMap
     * @throws \InvalidArgumentException
     */
    public function setRouteMap($routeMap)
    {
        if (!$routeMap instanceof RouteMap) {
            throw new \InvalidArgumentException('Expected parameter 1 routeMap to be RouteMap.');
        }

        $this->_tabConfiguration['ControllerConfig']['RouteMap'] = $routeMap;
    }

    /**
     * @param RestInfos $restInfos
     * @throws \InvalidArgumentException
     */
    public function setRestInfos($restInfos)
    {
        if (!$restInfos instanceof RestInfos) {
            throw new \InvalidArgumentException('Expected paramete 1 restInfos to be RestInfos.');
        }

        $this->_tabConfiguration['ControllerConfig']['RestServer'] = $restInfos;
        unset($this->_tabConfiguration['RestServer']);
    }

    /**
     * @param Server $server
     * @throws \InvalidArgumentException
     */
    public function recupererUriDepuisServer($server)
    {
        if (!$server instanceof Server) {
            throw new \InvalidArgumentException('Expected parameter 1 server to be Server.');
        }

        $this->_tabConfiguration['ControllerConfig']['Uri'] = $server->getUneVariableServeur('REQUEST_URI_NODIR');
    }

    /**
     * @param File $fichier
     * @throws \Exception
     */
    public function recupererConfigDepuisFichier($fichier)
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

        if (!array_key_exists(
            $langue = array_multisearch('InternationalizationConfig.Default_language', $tabCfg, true),
            array_multisearch('InternationalizationConfig.Available', $tabCfg, true)
        )
        ) {
            throw new \Exception(sprintf('Default language "%s" not found in available language list.', $langue));
        }

        $this->_tabConfiguration = array_merge($this->_tabConfiguration, $tabCfg);

        $restInfos = new RestInfos();
        $restInfos->parseRestInfos($this->_tabConfiguration['RestServer']);
        $this->setRestInfos($restInfos);
    }

    /**
     * @param ControllerFactory $controllerFactory
     * @throws \InvalidArgumentException
     */
    public function setControllerFactory($controllerFactory)
    {
        if (!$controllerFactory instanceof ControllerFactory) {
            throw new \InvalidArgumentException('Expected parameter 1 server to be Server.');
        }

        $this->_tabConfiguration['ControllerConfig']['CtrlFactory'] = $controllerFactory;
    }

    /**
     * @param string $clefConfig
     * @return mixed|null
     */
    public function getConfigValeur($clefConfig)
    {
        return array_multisearch($clefConfig, $this->_tabConfiguration, true);
    }
}