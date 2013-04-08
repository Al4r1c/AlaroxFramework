<?php
namespace AlaroxFramework;

include_once(__DIR__ . '/../../functions/functions.php');

class Main
{
    /**
     * @var AlaroxFramework
     */
    private $_alaroxFramework;

    /**
     * @param string $cheminVersFichierConfig
     * @param string $cheminVersFichierRouteMap
     * @param string $repertoireControlleurs
     */
    public function __construct($cheminVersFichierConfig, $cheminVersFichierRouteMap, $repertoireControlleurs)
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
        $this->_alaroxFramework->genererConfigDepuisFichiers(
            $cheminVersFichierConfig, $cheminVersFichierRouteMap, $repertoireControlleurs
        );
    }

    /**
     * @return string
     */
    public function run()
    {
        $reponse = $this->_alaroxFramework->process();

        http_response_code($reponse->getStatusHttp());

        return $reponse->getCorpsReponse();
    }
}