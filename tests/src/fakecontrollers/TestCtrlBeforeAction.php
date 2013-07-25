<?php
namespace Tests\fakecontrollers;

use AlaroxFramework\traitement\controller\GenericController;
use AlaroxFramework\utils\ObjetRequete;

class TestCtrlBeforeAction extends GenericController
{
    public function indexAction()
    {
        return 'THIS IS INDEX ACTION';
    }

    public function beforeExecuteAction()
    {
        return true;
    }
}