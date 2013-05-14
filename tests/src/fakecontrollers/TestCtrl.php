<?php
namespace Tests\fakecontrollers;

use AlaroxFramework\traitement\controller\GenericController;
use AlaroxFramework\utils\ObjetRequete;

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
        $view = $this->generateView('templatetest.twig');

        $resultRequete = $this->executeRequest(
            'myServer',
            new ObjetRequete('/produit', 'GET', array('_id' => $this->getUneVariableRequete('id')))
        );

        return $view->withResponseObject($resultRequete);
    }
}