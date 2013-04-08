<?php
namespace Tests\fakecontrollers;

use AlaroxFramework\traitement\controller\GenericController;

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
}