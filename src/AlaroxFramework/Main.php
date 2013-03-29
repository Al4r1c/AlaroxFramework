<?php
namespace AlaroxFramework;

include_once(__DIR__ . '../../functions/functions.php');

class Main
{
    /**
     * @var AlaroxFramework
     */
    private $_alaroxFramework;

    /**
     * @param string $cheminVersFichierConfig
     * @param string $cheminVersFichierRouteMap
     */
    public function __construct($cheminVersFichierConfig, $cheminVersFichierRouteMap)
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
        $this->_alaroxFramework->genererConfigDepuisFichiers($cheminVersFichierConfig, $cheminVersFichierRouteMap);
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->_alaroxFramework->process();
    }
}