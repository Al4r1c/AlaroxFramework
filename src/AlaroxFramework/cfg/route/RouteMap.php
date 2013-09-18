<?php
namespace AlaroxFramework\cfg\route;

use AlaroxFileManager\FileManager\File;

class RouteMap
{
    /**
     * @var Route
     */
    private $_routeParDefaut;

    /**
     * @var Route[]
     */
    private $_routes = array();

    /**
     * @var array
     */
    private $_staticAliases = array();

    /**
     * @var array
     */
    private static $valeursMinimales = array('Default.controller','Default.action', 'RouteMap', 'Static');

    /**
     * @return Route
     */
    public function getRouteParDefaut()
    {
        return $this->_routeParDefaut;
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
     * @param Route $routeControleurParDefaut
     * @throws \InvalidArgumentException
     */
    public function setRouteParDefaut($routeControleurParDefaut)
    {
        if (!$routeControleurParDefaut instanceof Route) {
            throw new \InvalidArgumentException('Expected parameter 1 routeControleurParDefaut to be Route.');
        }

        $this->_routeParDefaut = $routeControleurParDefaut;
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
        if (is_array($staticAliases)) {
            $i = 0;
            while ($i < count($staticAliases)) {
                if (!startsWith($uri = rtrim(preg_replace('#(\/)\1+#', '$1', $staticAliases[$i]), '/'), '/')) {
                    $uri = '/' . $uri;
                }

                $staticAliases[$i] = $uri;
                $i++;
            }

            $this->_staticAliases = $staticAliases;
        }
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
            if (is_null(array_multisearch($uneValeurMinimale, $routeMap, true))) {
                throw new \Exception(sprintf('Missing route map key "%s".', $uneValeurMinimale));
            }
        }


        $routeMap = array_change_key_case_recursive($routeMap, CASE_LOWER);

        if (is_array($routeMap['routemap'])) {
            foreach ($routeMap['routemap'] as $uri => $uneRoute) {
                if (!is_string($uri)) {
                    throw new \Exception('RouteMap parse error: no uri set or invalid uri.');
                }

                if (!isset($uneRoute['controller'])) {
                    throw new \Exception(sprintf('RouteMap parse error: key controller is missing for uri %s.', $uri));
                }

                if (!isset($uneRoute['defaultaction']) && !isset($uneRoute['mapping'])) {
                    throw new \Exception(sprintf(
                        'RouteMap parse error: no action attached for uri %s: key defaultAction OR mapping must be set.',
                        $uri
                    ));
                }

                $route = new Route();
                $route->setUri($uri);
                $route->setController($uneRoute['controller']);

                if (isset($uneRoute['defaultaction'])) {
                    $route->setDefaultAction($uneRoute['defaultaction']);
                }

                if (isset($uneRoute['mapping'])) {
                    $route->setMapping($uneRoute['mapping']);
                }

                $this->ajouterRoute($route);
            }
        }

        $routeControllerDefaut = new Route();
        $routeControllerDefaut->setController($routeMap['default']['controller']);
        $routeControllerDefaut->setDefaultAction($routeMap['default']['action']);
        $this->setRouteParDefaut($routeControllerDefaut);


        $this->setStaticAliases($routeMap['static']);
    }
}