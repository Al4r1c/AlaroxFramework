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
     * @param string $cheminVersFichierRestInfos
     * @param string $cheminVersFichierRouteMap
     */
    public function __construct($cheminVersFichierConfig, $cheminVersFichierRestInfos, $cheminVersFichierRouteMap = '')
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
        $this->_alaroxFramework->genererConfigDepuisFichiers(
            $cheminVersFichierConfig, $cheminVersFichierRestInfos, $cheminVersFichierRouteMap
        );
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->_alaroxFramework->process();
    }
}