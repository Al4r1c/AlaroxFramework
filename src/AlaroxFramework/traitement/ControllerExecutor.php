<?php
namespace AlaroxFramework\traitement;

use AlaroxFramework\cfg\configs\ControllerFactory;

class ControllerExecutor
{
    /**
     * @var ControllerFactory
     */
    private $_controllerFactory;

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
     * @param string $nomClasseController
     * @param string $actionAEffectuer
     * @param array $tabVariablesRequete
     * @return mixed
     * @throws NotFoundException
     */
    public function executerControleur($nomClasseController, $actionAEffectuer, $tabVariablesRequete = array())
    {
        try {
            $controlleur =
                $this->_controllerFactory->{$nomClasseController}($tabVariablesRequete);

            if (method_exists($controlleur, 'beforeExecuteAction') === true) {
                $controlleur->beforeExecuteAction();
            }
        } catch (\Exception $uneException) {
            throw new NotFoundException(sprintf(
                'Can\'t load controller "%s": %s.',
                $nomClasseController,
                $uneException->getMessage()
            ));
        }


        if (method_exists($controlleur, $actionAEffectuer)) {
            if (is_callable(array($controlleur, $actionAEffectuer))) {
                return $controlleur->{$actionAEffectuer}();
            } else {
                throw new NotFoundException(sprintf(
                    'Action "%s" not reachable in controller "%s".',
                    $actionAEffectuer,
                    $nomClasseController
                ));
            }
        } else {
            throw new NotFoundException(sprintf(
                'Action "%s" not found in controller "%s".',
                $actionAEffectuer,
                $nomClasseController
            ));
        }
    }
}