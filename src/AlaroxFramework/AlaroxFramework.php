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
     * @throws \InvalidArgumentException
     */
    public function setConteneur($conteneur)
    {
        if (!$conteneur instanceof Conteneur) {
            throw new \InvalidArgumentException('Expected Conteneur.');
        }

        $this->_conteneur = $conteneur;
    }

    /**
     * @param string $cheminVersFichierConfig
     * @param string $cheminVersFichierRouteMap
     * @param string $repertoireControlleurs
     * @throws \InvalidArgumentException
     */
    public function genererConfigDepuisFichiers(
        $cheminVersFichierConfig,
        $cheminVersFichierRouteMap,
        $repertoireControlleurs)
    {
        if (!($config =
            $this->_conteneur->getConfig($cheminVersFichierConfig, $cheminVersFichierRouteMap, $repertoireControlleurs))
            instanceof
            Config
        ) {
            throw new \InvalidArgumentException('Expected Config.');
        }

        $this->_config = $config;
    }

    public function process()
    {
        return null;
    }
}