<?php
namespace AlaroxFramework\cfg;

use AlaroxFramework\traitement\controller\GenericController;

class ControllerFactory
{
    /**
     * @var \Closure[]
     */
    private $_listControllers = array();

    /**
     * @param string $nomMethode
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function __call($nomMethode, $arguments)
    {
        if (array_key_exists($nomMethode = strtolower($nomMethode), $this->_listControllers)) {
            return call_user_func_array($this->_listControllers[$nomMethode], $arguments);
        }

        throw new \Exception(sprintf('Controller %s not found.', $nomMethode));
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

        foreach (array_map('strtolower', $plainListControllers) as $unControllerTrouve) {
            $tempNamespacesSepares = explode('\\', $unControllerTrouve);
            $this->_listControllers[end($tempNamespacesSepares)] =
                function ($restClient) use ($unControllerTrouve) {
                    /** @var GenericController $controller */
                    $controller = new $unControllerTrouve();
                    $controller->setRestClient($restClient);

                    return $controller;
                };
        }
    }
}