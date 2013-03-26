<?php
namespace AlaroxFramework;

class AlaroxFramework
{
    /**
     * @var Conteneur
     */
    private $_conteneur;

    public function setConteneur($conteneur)
    {
        $this->_conteneur = $conteneur;
    }

    public function process()
    {
        return null;
    }
}