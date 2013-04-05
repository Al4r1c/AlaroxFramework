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
     * @param string $ctrlRecherche
     * @return Route|null
     */
    public function getUneRouteByController($ctrlRecherche)
    {
        foreach ($this->_routes as $uneRoute) {
            if (strcmp(strtolower($ctrlRecherche), $uneRoute->getController()) == 0) {
                return $uneRoute;
            }
        }

        return null;
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
            if (!is_string($uri)) {
                throw new \Exception('RouteMap parse error: no uri set or invalid uri.');
            }

            if (!isset($uneRoute['controller'])) {
                throw new \Exception(sprintf('RouteMap parse error: key controller is missing for uri %s.', $uri));
            }

            if (!isset($uneRoute['defaultAction']) && !isset($uneRoute['mapping'])) {
                throw new \Exception(sprintf(
                    'RouteMap parse error: no action attached for uri %s: key defaultAction OR mapping must be set.',
                    $uri
                ));
            }

            $route = new Route();
            $route->setUri($uri);
            $route->setController($uneRoute['controller']);

            if (isset($uneRoute['pattern'])) {
                $route->setPattern($uneRoute['pattern']);
            }

            if (isset($uneRoute['defaultAction'])) {
                $route->setDefaultAction($uneRoute['defaultAction']);
            }

            if (isset($uneRoute['mapping'])) {
                $route->setMapping($uneRoute['mapping']);
            }

            $this->ajouterRoute($route);
        }

        $this->setControlerParDefaut($routeMap['Default_controller']);
        $this->setStaticAliases($routeMap['Static']);
    }
}