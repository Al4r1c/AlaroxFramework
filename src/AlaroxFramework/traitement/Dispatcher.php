<?php
namespace AlaroxFramework\traitement;

use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\route\Route;
use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\utils\View;

class Dispatcher
{
    /**
     * @var string
     */
    private $_uriDemandee;

    /**
     * @var boolean
     */
    private $_i18nActif;

    /**
     * @var ControllerFactory
     */
    private $_controllerFactory;

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
     * @param boolean $isActivated
     * @throws \InvalidArgumentException
     */
    public function setI18nActif($isActivated)
    {
        if (is_null($varBool = getValidBoolean($isActivated))) {
            throw new \InvalidArgumentException('Expected parameter 1 isActivated to be boolean.');
        }

        $this->_i18nActif = $varBool;
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
     * @return string|View
     * @throws \Exception
     */
    public function executerActionRequise()
    {
        foreach (get_object_vars($this) as $clef => $unAttribut) {
            if (is_null($unAttribut)) {
                throw new \Exception('Can\'t execute request: ' . $clef . 'is not set.');
            }
        }

        if ($this->_i18nActif === true) {
            $temp = array_filter(explode('/', $this->_uriDemandee), 'strlen');
            unset($temp[1]);
            $this->_uriDemandee = '/' . implode('/', $temp);
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
            $controllerParDefaut = $this->_routeMap->getRouteParDefaut();

            $nomClasseController = $controllerParDefaut->getController();
            $actionAEffectuer = $controllerParDefaut->getDefaultAction();
            $tabVariablesRequete = array();
        } else {
            list($nomClasseController, $actionAEffectuer, $tabVariablesRequete) = $this->dispatchUri();
        }

        try {
            $controlleur =
                $this->_controllerFactory->{$nomClasseController}($tabVariablesRequete);

            if (method_exists($controlleur, 'beforeExecuteAction') === true) {
                $controlleur->beforeExecuteAction();
            }

        } catch (\Exception $uneException) {
            throw new \Exception(sprintf(
                'Can\'t load controller "%s" for uri "%s": %s.',
                $nomClasseController,
                $this->_uriDemandee,
                $uneException->getMessage()
            ));
        }


        if (method_exists($controlleur, $actionAEffectuer)) {
            if (is_callable(array($controlleur, $actionAEffectuer))) {
                return $controlleur->{$actionAEffectuer}();
            } else {
                throw new \Exception(sprintf(
                    'Action "%s" not reachable in controller "%s".',
                    $actionAEffectuer,
                    $nomClasseController
                ));
            }
        } else {
            throw new \Exception(sprintf(
                'Action "%s" not found in controller "%s".',
                $actionAEffectuer,
                $nomClasseController
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
                $uriSansBaseDuMapping = rtrim(substr($this->_uriDemandee, strlen($uri)), '/');

                if ($uriSansBaseDuMapping == '' || startsWith($uriSansBaseDuMapping, '/')) {
                    $route = $uneRoute;
                    break;
                }
            }
        }

        if (isset($route) && isset($uriSansBaseDuMapping)) {
            if (!is_null($actionAEffectuerEtVariable = $this->recupererAction($uriSansBaseDuMapping, $route))) {
                array_unshift($actionAEffectuerEtVariable, $route->getController());

                return $actionAEffectuerEtVariable;
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
                $actionAEffectuer = array($actionDefaut, array());
            }
        } elseif (($mappingRouteTrouvee = $route->getMapping()) > 0) {
            $tabUriSansBaseDuMapping = array_filter(explode('/', $uriSansBaseDuMapping), 'strlen');

            foreach ($mappingRouteTrouvee as $patternUri => $actionPourPatternUri) {
                $patternUriWithModifier = preg_replace('#\$[a-z0-9]+\?#', '*', $patternUri);

                $actionTrouvee = false;

                if (strcmp($patternUriWithModifier, $uriSansBaseDuMapping) == 0) {
                    $actionTrouvee = true;
                } elseif (strpos($patternUriWithModifier, '*') !== false &&
                    count($tabPatternUri = array_filter(explode('/', $patternUriWithModifier), 'strlen')) ==
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
                    $actionAEffectuer = array($actionPourPatternUri,
                        $this->recupererVariablesDepuisPattern(
                            $patternUri,
                            array_filter(explode('/', $uriSansBaseDuMapping), 'strlen')
                        ));
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
}