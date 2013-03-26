<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\RouteMap;

class Conteneur
{
    /**
     * @param string $cheminVersFichier
     * @param string $cheminVersRouteMap
     * @return Config
     */
    public function getConfig($cheminVersFichier, $cheminVersRouteMap)
    {
        $alaroxFile = new AlaroxFile();

        $config = new Config();
        $config->recupererConfigDepuisFichier($alaroxFile->getFile($cheminVersFichier));

        if (!empty($cheminVersRouteMap)) {
            $routeMap = new RouteMap();
            $routeMap->setRouteMapDepuisFichier($cheminVersRouteMap);
            $config->setRouteMap($routeMap);
        }

        return $config;
    }
}