<?php
namespace AlaroxFramework\cfg\configs;

use AlaroxFramework\traitement\controller\GenericController;
use AlaroxFramework\utils\restclient\RestClient;
use AlaroxFramework\utils\session\SessionClient;
use AlaroxFramework\utils\view\ViewFactory;

class ControllerFactory
{
    /**
     * @var \Closure[]
     */
    private $_listControllers = array();

    /**
     * @var RestClient
     */
    private $_restClient;

    /**
     * @param string $nomControleur
     * @param array $arguments
     * @return GenericController
     * @throws \Exception
     */
    public function __call($nomControleur, $arguments)
    {
        if (array_key_exists($nomControleur = strtolower($nomControleur), $this->_listControllers)) {
            return call_user_func_array($this->_listControllers[$nomControleur], $arguments);
        }

        throw new \Exception('Controller not found in controller directory.');
    }

    /**
     * @param RestClient $restClient
     * @throws \InvalidArgumentException
     */
    public function setRestClient($restClient)
    {
        if (!$restClient instanceof RestClient) {
            throw new \InvalidArgumentException('Expected parameter 1 restClient to be RestClient.');
        }

        $this->_restClient = $restClient;
    }

    /**
     * @param array $plainListControllers
     * @param SessionClient $sessionClient
     * @param array $queryVars
     * @param ViewFactory $viewFactory
     * @throws \InvalidArgumentException
     */
    public function setListControllers($plainListControllers, $sessionClient, $queryVars, $viewFactory)
    {
        if (!is_array($plainListControllers)) {
            throw new \InvalidArgumentException('Expected parameter 1 plainListControllers to be array.');
        }

        foreach ($plainListControllers as $unControllerTrouve) {
            $tempNamespacesSepares = explode('\\', $unControllerTrouve);
            $this->_listControllers[strtolower(end($tempNamespacesSepares))] =
                function ($tabVariables) use ($unControllerTrouve, $sessionClient, $queryVars, $viewFactory) {
                    /** @var GenericController $controller */
                    $controller = new $unControllerTrouve();
                    $controller->setRestClient($this->_restClient);
                    $controller->setSessionClient($sessionClient);
                    $controller->setVariablesUri($tabVariables);
                    $controller->setViewFactory($viewFactory);
                    $controller->setVariablesRequete($queryVars);

                    return $controller;
                };
        }
    }
}