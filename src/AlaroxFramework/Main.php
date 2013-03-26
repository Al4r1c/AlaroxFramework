<?php
namespace AlaroxFramework;

class Main
{
    /**
     * @var AlaroxFramework
     */
    private $_alaroxFramework;

    /**
     * @param string $cheminVersFichierConfig
     */
    public function __construct($cheminVersFichierConfig)
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
        $this->_alaroxFramework->genererConfigDepuisFichier($cheminVersFichierConfig);
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->_alaroxFramework->process();
    }
}