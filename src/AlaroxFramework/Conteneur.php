<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\ControllerFactory;
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
     * @param string $repertoireControlleurs
     * @return Config
     */
    public function getConfig($cheminVersFichierConfig, $cheminVersRouteMap, $repertoireControlleurs)
    {
        $config = new Config();
        $config->recupererConfigDepuisFichier($this->getFile($cheminVersFichierConfig));
        $config->recupererUriDepuisServer($this->getServer());
        $config->setControllerFactory($this->getControllerFactory($repertoireControlleurs));

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
     * @param string $repertoireControlleurs
     * @return ControllerFactory
     */
    public function getControllerFactory($repertoireControlleurs)
    {
        $ctrlFactory = new ControllerFactory();

        $controllers = array();
        $scanDir = scandir($repertoireControlleurs);
        foreach ($scanDir as $uneEntite) {
            if (is_file($file = $repertoireControlleurs . DIRECTORY_SEPARATOR . $uneEntite)) {
                $php_code = file_get_contents($file);
                $tokens = token_get_all($php_code);

                $classToken = false;
                $namespaceToken = false;
                $currentNamespace = '';

                foreach ($tokens as $token) {
                    if (is_array($token)) {
                        if ($token[0] == T_NAMESPACE) {
                            $namespaceToken = true;
                        } elseif ($namespaceToken && ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR)) {
                            $currentNamespace .= $token[1];
                        } elseif ($namespaceToken && ($token[0] == T_CLASS || $token[0] == T_USE)) {
                            $namespaceToken = false;
                        }

                        if ($token[0] == T_CLASS) {
                            $classToken = true;
                        } elseif ($classToken && $token[0] == T_STRING) {
                            $controllers[] = $currentNamespace . '\\' . $token[1];
                            break;
                        }
                    }
                }
            }
        }

        $ctrlFactory->setListControllers($controllers);

        return $ctrlFactory;
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