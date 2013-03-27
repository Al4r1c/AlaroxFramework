<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\RouteMap;
use AlaroxFramework\cfg\Server;

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
        $config->parseServer($this->getServer());

        if (!empty($cheminVersRouteMap)) {
            $routeMap = new RouteMap();
            $routeMap->setRouteMapDepuisFichier($cheminVersRouteMap);
            $config->setRouteMap($routeMap);
        }

        return $config;
    }

    public function getServer()
    {
        $server = new Server();
        $server->setServeurVariables($_SERVER);

        return $server;
    }
}