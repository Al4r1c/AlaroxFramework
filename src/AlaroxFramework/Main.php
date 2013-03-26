<?php
namespace AlaroxFramework;

class Main
{
    /**
     * @var AlaroxFramework
     */
    private $_alaroxFramework;

    public function __construct()
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
    }

    public function run()
    {
        return $this->_alaroxFramework->process();
    }
}