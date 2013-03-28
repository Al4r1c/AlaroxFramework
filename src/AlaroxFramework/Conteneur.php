<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\RestInfos;
use AlaroxFramework\cfg\RouteMap;
use AlaroxFramework\cfg\Server;

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
     * @param string $cheminVersRestInfos
     * @param string $cheminVersRouteMap
     * @return Config
     */
    public function getConfig($cheminVersFichierConfig, $cheminVersRestInfos, $cheminVersRouteMap)
    {
        $config = new Config();
        $config->recupererConfigDepuisFichier($this->getFile($cheminVersFichierConfig));
        $config->setRestInfos($this->getRestInfos($cheminVersRestInfos));
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

    private function getRestInfos($cheminVersRestInfos)
    {
        $routeMap = new RestInfos();
        $routeMap->setRestInfosDepuisFichier($this->getFile($cheminVersRestInfos));

        return $routeMap;
    }
}