<?php
namespace AlaroxFramework;

class Main
{
    private $_config;

    public function getConfig()
    {
        return $this->_config;
    }

    public function setCheminFichierConfig($cheminVersFichierConfig)
    {
        /*$this->_config = new Config();
        $this->_config->chargerConfigDepuisFichier($cheminVersFichierConfig);*/
    }

    public function run() {
        return null;
    }
}