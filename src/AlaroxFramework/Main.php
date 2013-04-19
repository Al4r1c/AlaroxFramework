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
     * @param string $pathToConfigFile
     * @param string $pathToRouteMapFile
     * @param string $controllersRepertory
     * @param string $templatesRepertory
     * @param string $localesRepertory
     */
    public function __construct(
        $pathToConfigFile,
        $pathToRouteMapFile,
        $controllersRepertory,
        $templatesRepertory,
        $localesRepertory = '')
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
        $this->_alaroxFramework->genererConfigDepuisFichiers(
            $pathToConfigFile, $pathToRouteMapFile, $controllersRepertory, $templatesRepertory, $localesRepertory
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