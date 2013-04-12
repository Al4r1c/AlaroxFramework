<?php
namespace AlaroxFramework\traitement;

use AlaroxFramework\cfg\ControllerFactory;
use AlaroxFramework\cfg\RestInfos;
use AlaroxFramework\cfg\route\Route;
use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\traitement\restclient\CurlClient;
use AlaroxFramework\traitement\restclient\RestClient;
use AlaroxFramework\utils\View;

class Dispatcher
{
    /**
     * @var string
     */
    private $_uriDemandee;

    /**
     * @var ControllerFactory
     */
    private $_controllerFactory;

    /**
     * @var RestInfos
     */
    private $_restInfos;

    /**
     * @var RouteMap
     */
    private $_routeMap;

    /**
     * @param string $uriDemandee
     * @throws \Exception
     */
    public function setUriDemandee($uriDemandee)
    {
        if (empty($uriDemandee)) {
            throw new \Exception(sprintf('Empty URI.'));
        }

        $this->_uriDemandee = $uriDemandee;
    }

    /**
     * @param ControllerFactory $controllerFactory
     * @throws \InvalidArgumentException
     */
    public function setControllerFactory($controllerFactory)
    {
        if (!$controllerFactory instanceof ControllerFactory) {
            throw new \InvalidArgumentException('Expected parameter 1 controllerFactory to be ControllerFactory.');
        }

        $this->_controllerFactory = $controllerFactory;
    }

    /**
     * @param RestInfos $restInfos
     * @throws \InvalidArgumentException
     */
    public function setRestInfos($restInfos)
    {
        if (!$restInfos instanceof RestInfos) {
            throw new \InvalidArgumentException('Expected parameter 1 restInfos to be RestInfos.');
        }

        $this->_restInfos = $restInfos;
    }

    /**
     * @param RouteMap $routeMap
     * @throws \InvalidArgumentException
     */
    public function setRouteMap($routeMap)
    {
        if (!$routeMap instanceof RouteMap) {
            throw new \InvalidArgumentException('Expected parameter 1 routeMap to be RouteMap.');
        }

        $this->_routeMap = $routeMap;
    }

    /**
     * @param array $tabConfig
     */
    public function parseConfig($tabConfig)
    {
        $this->setUriDemandee($tabConfig['Uri']);
        $this->setRestInfos($tabConfig['RestServer']);
        $this->setRouteMap($tabConfig['RouteMap']);
        $this->setControllerFactory($tabConfig['CtrlFactory']);
    }

    /**
     * @return RestClient
     */
    private function getRestClient()
    {
        $restClient = new RestClient();
        $restClient->setRestInfos($this->_restInfos);
        $restClient->setCurlClient(new CurlClient());

        return $restClient;
    }

    /**
     * @return string|View
     * @throws \Exception
     */
    public function executerActionRequise()
    {
        foreach (get_object_vars($this) as $clef => $unAttribut) {
            if (empty($unAttribut)) {
                throw new \Exception('Can\'t execute request: ' . $clef . 'is not set.');
            }
        }

        $staticAliasFound = false;
        foreach ($this->_routeMap->getStaticAliases() as $unAliasStatic) {
            if (startsWith($this->_uriDemandee, $unAliasStatic)) {
                $staticAliasFound = $unAliasStatic;
                break;
            }
        }

        if ($staticAliasFound !== false) {
            $uriSansBaseDuMapping = trim(substr($this->_uriDemandee, strlen($staticAliasFound)), '/');

            if (!empty($uriSansBaseDuMapping)) {
                $view = new View();
                $view->renderView($uriSansBaseDuMapping . '.twig');

                return $view;
            } else {
                throw new \Exception('No static page defined in uri.');
            }
        } else {
            return $this->dispatchAvecControlleur();
        }
    }

    /**
     * @return string|View
     * @throws \Exception
     */
    private function dispatchAvecControlleur()
    {
        if (strcmp($this->_uriDemandee, '/') == 0) {
            list($nomClasseController, $actionAEffectuer) = $this->dispatchDefaultUri();
            $tabVariablesRequete = array();
        } else {
            list($nomClasseController, $actionAEffectuer, $tabVariablesRequete) = $this->dispatchUri();
        }

        try {
            $controlleur =
                $this->_controllerFactory->{$nomClasseController}($this->getRestClient(), $tabVariablesRequete);
        } catch (\Exception $uneException) {
            throw new \Exception(sprintf(
                'Can\'t load controller "%s" for uri "%s": %s.', $nomClasseController, $this->_uriDemandee,
                $uneException->getMessage()
            ));
        }


        if (method_exists($controlleur, $actionAEffectuer)) {
            if (is_callable(array($controlleur, $actionAEffectuer))) {
                return $controlleur->{$actionAEffectuer}();
            } else {
                throw new \Exception(sprintf(
                    'Action "%s" not reachable in controller "%s".', $actionAEffectuer, $nomClasseController
                ));
            }
        } else {
            throw new \Exception(sprintf(
                'Action "%s" not found in controller "%s".', $actionAEffectuer, $nomClasseController
            ));
        }
    }

    /**
     * @throws \Exception
     * @return array
     */
    private function dispatchUri()
    {
        foreach ($this->_routeMap->getRoutes() as $uneRoute) {
            if (startsWith($this->_uriDemandee, ($uri = $uneRoute->getUri()))) {
                $route = $uneRoute;
                break;
            }
        }

        if (isset($route)) {
            $nomClasseController = $route->getController();

            $uriSansBaseDuMapping = rtrim(substr($this->_uriDemandee, strlen($uri)), '/');
            $actionAEffectuer = $this->recupererAction($uriSansBaseDuMapping, $route);

            if (!is_null($actionAEffectuer)) {
                if (!is_null($pattern = $route->getPattern())) {
                    $tabVariablesRequete = $this->recupererVariablesDepuisPattern(
                        $pattern, array_filter(explode('/', $uriSansBaseDuMapping), 'strlen')
                    );

                    foreach ($tabVariablesRequete as $pattern => $variable) {
                        $actionAEffectuer = str_replace('$' . $pattern . '?', $variable, $actionAEffectuer);
                    }

                    return array($nomClasseController, $actionAEffectuer, $tabVariablesRequete);
                } else {
                    return array($nomClasseController, $actionAEffectuer, array());
                }
            } else {
                throw new \Exception(sprintf('No action found for uri "%s".', $this->_uriDemandee));
            }
        } else {
            throw new \Exception(sprintf('No route mapped for uri "%s".', $this->_uriDemandee));
        }
    }

    /**
     * @param string $uriSansBaseDuMapping
     * @param Route $route
     * @return mixed
     */
    private function recupererAction($uriSansBaseDuMapping, $route)
    {
        $actionAEffectuer = null;

        if (empty($uriSansBaseDuMapping)) {
            if (!is_null($actionDefaut = $route->getDefaultAction())) {
                $actionAEffectuer = $actionDefaut;
            }
        } elseif (($mappingRouteTrouvee = $route->getMapping()) > 0) {
            $tabUriSansBaseDuMapping = array_filter(explode('/', $uriSansBaseDuMapping), 'strlen');

            foreach ($mappingRouteTrouvee as $patternUri => $actionPourPatternUri) {
                $actionTrouvee = false;

                if (strcmp($patternUri, $uriSansBaseDuMapping) == 0) {
                    $actionTrouvee = true;
                } elseif (strpos($patternUri, '*') !== false &&
                    count($tabPatternUri = array_filter(explode('/', $patternUri), 'strlen')) ==
                        count($tabUriSansBaseDuMapping)
                ) {
                    $actionTrouvee = true;

                    foreach ($tabPatternUri as $clef => $unePartiePatternUri) {
                        if (array_key_exists($clef, $tabUriSansBaseDuMapping)) {
                            if (
                                (strcmp($unePartiePatternUri, $tabUriSansBaseDuMapping[$clef]) == 0) ||
                                (strpos($unePartiePatternUri, '*') !== false &&
                                preg_match(
                                    '#^' . str_replace('*', '[a-zA-Z0-9]+', $unePartiePatternUri) . '$#',
                                    $tabUriSansBaseDuMapping[$clef]
                                ) == 1)
                            ) {
                                continue;
                            }
                        }

                        $actionTrouvee = false;
                    }
                }

                if ($actionTrouvee === true) {
                    $actionAEffectuer = $actionPourPatternUri;
                    break;
                }
            }
        }

        return $actionAEffectuer;
    }

    /**
     * @param string $pattern
     * @param array $uriFractionneTableau
     * @return array
     */
    private function recupererVariablesDepuisPattern($pattern, $uriFractionneTableau)
    {
        $tabVariables = array();
        $tabPattern = explode('/', $pattern);

        foreach ($uriFractionneTableau as $clef => $unBoutUri) {
            if (!array_key_exists($clef, $tabPattern)) {
                break;
            }

            if (preg_match_all('#\$([a-zA-Z0-9]+)\?#', $tabPattern[$clef], $tabAllVariablesPattern) > 0) {
                $patternGeneriqueCorrespondant =
                    '#^' . preg_replace('#\$([a-zA-Z0-9]+)\?#', '([a-zA-Z0-9]+)', $tabPattern[$clef]) . '$#';
                if (preg_match($patternGeneriqueCorrespondant, $unBoutUri, $tabPregCorrespondances) == 1) {
                    foreach ($tabAllVariablesPattern[1] as $uneClef => $uneVar) {
                        $tabVariables[$uneVar] = $tabPregCorrespondances[$uneClef + 1];
                    }
                }
            }
        }

        return $tabVariables;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function dispatchDefaultUri()
    {
        $nomClasseController = $this->_routeMap->getControlerParDefaut();

        if (!is_null($route = $this->_routeMap->getUneRouteByController($nomClasseController))) {
            if (!is_null($actionDefaut = $route->getDefaultAction())) {
                $actionAEffectuer = $actionDefaut;

                return array($nomClasseController, $actionAEffectuer);
            } else {
                throw new \Exception(sprintf(
                    'No default action found for default uri "%s".', $route->getUri()
                ));
            }
        } else {
            throw new \Exception(sprintf('No route with controller "%s" found.', $nomClasseController));
        }
    }
}