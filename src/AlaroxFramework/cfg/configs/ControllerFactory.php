<?php
namespace AlaroxFramework\cfg\configs;

use AlaroxFramework\traitement\controller\GenericController;

class ControllerFactory
{
    /**
     * @var \Closure[]
     */
    private $_listControllers = array();

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
     * @param array $plainListControllers
     * @throws \InvalidArgumentException
     */
    public function setListControllers($plainListControllers)
    {
        if (!is_array($plainListControllers)) {
            throw new \InvalidArgumentException('Expected array for parameter 1 listControllers.');
        }

        foreach ($plainListControllers as $unControllerTrouve) {
            $tempNamespacesSepares = explode('\\', $unControllerTrouve);
            $this->_listControllers[strtolower(end($tempNamespacesSepares))] =
                function ($restClient, $tabVariables) use ($unControllerTrouve) {
                    /** @var GenericController $controller */
                    $controller = new $unControllerTrouve();
                    $controller->setRestClient($restClient);
                    $controller->setVariablesRequete($tabVariables);

                    return $controller;
                };
        }
    }
}