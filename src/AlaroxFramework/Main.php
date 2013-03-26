<?php
namespace AlaroxFramework;

class Main
{
    /**
     * @var AlaroxFramework
     */
    private $_alaroxFramework;

    public function __construct($cheminVersFichierConfig)
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
        $this->_alaroxFramework->genererConfigDepuisFichier($cheminVersFichierConfig);
    }

    public function run()
    {
        return $this->_alaroxFramework->process();
    }
}