<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\RestInfos;
use AlaroxFramework\cfg\Server;
use AlaroxFramework\cfg\route\RouteMap;

class Conteneur
{
    /**
     * @param $cheminVersFichier
     * @return File
     */
    public function getFile($cheminVersFichier)
    {
        $alaroxFile = new AlaroxFile();

        return $alaroxFile->getFile($cheminVersFichier);
    }

    /**
     * @param string $cheminVersFichierConfig
     * @param string $cheminVersRouteMap
     * @return Config
     */
    public function getConfig($cheminVersFichierConfig, $cheminVersRouteMap)
    {
        $config = new Config();
        $config->recupererConfigDepuisFichier($this->getFile($cheminVersFichierConfig));
        $config->parseServer($this->getServer());

        if (!empty($cheminVersRouteMap)) {
            $config->setRouteMap($this->getRoute($cheminVersRouteMap));
        }

        return $config;
    }

    /**
     * @param $cheminVersRouteMap
     * @return RouteMap
     */
    public function getRoute($cheminVersRouteMap)
    {
        $routeMap = new RouteMap();
        $routeMap->setRouteMapDepuisFichier($this->getFile($cheminVersRouteMap));

        return $routeMap;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        $server = new Server();
        $server->setServeurVariables($_SERVER);

        return $server;
    }
}