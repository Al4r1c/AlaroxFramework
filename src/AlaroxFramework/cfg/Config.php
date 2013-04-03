<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;
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
        'RestServer',
        'RestServer.Url',
        'RestServer.Format',
        'RestServer.Username',
        'RestServer.PassKey',
        'TemplateConfig',
        'InternationalizationConfig',
        'TemplateConfig.Name',
        'TemplateConfig.Media_url',
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
            if (is_null($this->rechercheValeurTableauMultidim($uneValeurMinimale, $tabCfg))) {
                throw new \Exception(sprintf('Missing config key "%s".', $uneValeurMinimale));
            }
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
        return $this->rechercheValeurTableauMultidim($clefConfig, $this->_tabConfiguration);
    }

    /**
     * @param string $clefRecherchee
     * @param array $tableauConcerne
     * @return mixed|null
     */
    private function rechercheValeurTableauMultidim($clefRecherchee, $tableauConcerne)
    {
        foreach (array_map('strtolower', explode('.', $clefRecherchee)) as $uneClef) {
            if (array_key_exists($uneClef, $tableauConcerne = array_change_key_case($tableauConcerne, CASE_LOWER))) {
                $tableauConcerne = $tableauConcerne[$uneClef];
            } else {
                return null;
            }
        }

        return $tableauConcerne;
    }
}