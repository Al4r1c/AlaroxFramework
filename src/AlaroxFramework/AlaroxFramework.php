<?php
namespace AlaroxFramework;

use AlaroxFramework\cfg\Config;

class AlaroxFramework
{
    /**
     * @var Conteneur
     */
    private $_conteneur;

    /**
     * @var Config
     */
    private $_config;

    /**
     * @param Conteneur $conteneur
     */
    public function setConteneur($conteneur)
    {
        $this->_conteneur = $conteneur;
    }

    /**
     * @param string $cheminVersFichierConfig
     */
    public function genererConfigDepuisFichier($cheminVersFichierConfig)
    {
        $this->_config = $this->_conteneur->getConfig($cheminVersFichierConfig);
    }

    public function process()
    {
        return null;
    }
}