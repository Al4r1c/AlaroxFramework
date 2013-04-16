<?php
namespace Tests\fakecontrollers;

use AlaroxFramework\traitement\controller\GenericController;
use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\View;

class TestCtrl extends GenericController
{
    public function indexAction()
    {
        return 'THIS IS INDEX ACTION';
    }

    public function myActionFirst()
    {
        return 'myFirst ACTION';
    }

    public function myActionSecond()
    {
        return 'mySecond ACTION';
    }

    private function privatemethod()
    {
        return false;
    }

    public function sendRequest()
    {
        $view = new View();

        $resultRequete = $this->getRestClient()->executerRequete(
            new ObjetRequete('/produit', 'GET', array('_id' => $this->getUneVariableRequete('id')))
        );

        return $view->renderView('templatetest.twig')->withResponseObject($resultRequete);
    }
}