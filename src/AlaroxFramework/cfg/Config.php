<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;

class Config
{
    /**
     * @var array
     */
    private $_tabConfiguration = array();

    /**
     * @var array
     */
    private static $valeursMinimales = array('ControllerConfig',
        'TemplateConfig',
        'InternationalizationConfig',
        'ControllerConfig.Default_controller',
        'ControllerConfig.RestServer_url',
        'ControllerConfig.RouteMap',
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
            throw new \InvalidArgumentException('Expected RouteMap.');
        }

        $this->_tabConfiguration['ControllerConfig']['RouteMapFile'] = $routeMap;
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