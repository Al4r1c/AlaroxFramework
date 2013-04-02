<?php
namespace Tests\fakecontrollers;

use AlaroxFramework\traitement\controller\GenericController;

class Index extends GenericController
{
    public function indexAction()
    {
        return true;
    }
}