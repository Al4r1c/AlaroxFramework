<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;

class RouteMap
{
    /**
     * @var array
     */
    private $_routeMap;

    /**
     * @return array
     */
    public function getRouteMap()
    {
        return $this->_routeMap;
    }

    /**
     * @param File $fichierRouteMap
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function setRouteMapDepuisFichier($fichierRouteMap)
    {
        if (!$fichierRouteMap instanceof File) {
            throw new \InvalidArgumentException('Expected File.');
        }

        if ($fichierRouteMap->fileExist() === true) {
            $this->_routeMap = $fichierRouteMap->loadFile();
        } else {
            throw new \Exception(sprintf('Config file %s does not exist.', $fichierRouteMap->getPathToFile()));
        }
    }
}