<?php
namespace Tests\fakecontrollers;

use AlaroxFramework\traitement\controller\GenericController;

class TestCtrl extends GenericController
{
    public function indexAction()
    {
        return true;
    }

    public function myActionFirst()
    {
        return 'myFirst';
    }

    public function myActionSecond()
    {
        return 'mySecond';
    }

    private function privatemethod()
    {
        return false;
    }
}