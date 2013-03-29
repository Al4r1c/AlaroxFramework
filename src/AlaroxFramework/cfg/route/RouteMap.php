<?php
namespace AlaroxFramework\cfg\route;

use AlaroxFileManager\FileManager\File;

class RouteMap
{
    /**
     * @var string
     */
    private $_controlerParDefaut;

    /**
     * @var Route[]
     */
    private $_routes = array();

    /**
     * @var array
     */
    private $_staticAliases;

    /**
     * @var array
     */
    private static $valeursMinimales = array('Default_controller', 'RouteMap', 'Static');

    /**
     * @return string
     */
    public function getControlerParDefaut()
    {
        return $this->_controlerParDefaut;
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * @return array
     */
    public function getStaticAliases()
    {
        return $this->_staticAliases;
    }

    /**
     * @param string $controleurParDefaut
     */
    public function setControlerParDefaut($controleurParDefaut)
    {
        $this->_controlerParDefaut = $controleurParDefaut;
    }

    /**
     * @param Route $route
     * @throws \InvalidArgumentException
     */
    public function ajouterRoute($route)
    {
        if (!$route instanceof Route) {
            throw new \InvalidArgumentException('Expected parameter 1 route to be Route.');
        }

        $this->_routes[] = $route;
    }

    /**
     * @param array $staticAliases
     * @throws \InvalidArgumentException
     */
    public function setStaticAliases($staticAliases)
    {
        if (!is_array($staticAliases)) {
            throw new \InvalidArgumentException('Expected parameter 1 staticAliases to be array.');
        }

        $this->_staticAliases = $staticAliases;
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

        if ($fichierRouteMap->fileExist() === false) {
            throw new \Exception(sprintf('Config file %s does not exist.', $fichierRouteMap->getPathToFile()));
        }

        $routeMap = $fichierRouteMap->loadFile();

        foreach (self::$valeursMinimales as $uneValeurMinimale) {
            if (!array_key_exists($uneValeurMinimale, $routeMap)) {
                throw new \Exception(sprintf('Missing route map key "%s".', $uneValeurMinimale));
            }
        }

        foreach ($routeMap['RouteMap'] as $uri => $uneRoute) {
            $route = new Route();
            $route->setUri($uri);
            $route->setController($uneRoute['controller']);
            $route->setPattern($uneRoute['pattern']);
            $route->setDefaultAction($uneRoute['defaultAction']);
            $route->setMapping($uneRoute['mapping']);

            $this->ajouterRoute($route);
        }

        $this->setControlerParDefaut($routeMap['Default_controller']);
        $this->setStaticAliases($routeMap['Static']);
    }
}