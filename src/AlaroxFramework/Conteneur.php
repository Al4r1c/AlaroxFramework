<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFramework\cfg\Config;

class Conteneur
{
    /**
     * @param string $cheminVersFichier
     * @return Config
     */
    public function getConfig($cheminVersFichier)
    {
        /** @var Config $config */
        $config = new Config();

        /** @var AlaroxFile $alaroxFile */
        $alaroxFile = new AlaroxFile();

        $config->recupererConfigDepuisFichier($alaroxFile->getFile($cheminVersFichier));

        return $config;
    }
}