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
     * @param array $arrayConfiguration
     */
    public function __construct($arrayConfiguration)
    {
        $this->_alaroxFramework = new AlaroxFramework();
        $this->_alaroxFramework->setConteneur(new Conteneur());
        $this->_alaroxFramework->genererConfig($arrayConfiguration);
    }

    /**
     * @throws \Exception
     * @return string
     */
    public function run()
    {
        $reponse = $this->_alaroxFramework->process();

        http_response_code($reponse->getStatusHttp());

        if ($reponse->isException() === true) {
            return $reponse->getReponse();
        } else {
            throw $reponse->getReponse();
        }
    }
}