<?php
namespace AlaroxFramework;

use AlaroxFramework\cfg\Config;
use AlaroxFramework\utils\HtmlReponse;

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
     * @param Config $config
     * @throws \InvalidArgumentException
     */
    public function setConfig($config)
    {
        if (!$config instanceof Config) {
            throw new \InvalidArgumentException('Expected Config.');
        }

        if ($config->isProdVersion() === true) {
            $this->_conteneur->getErreurHandler()->setHandler();
        }

        $this->_config = $config;
    }

    /**
     * @param string $cheminVersFichierConfig
     * @param string $cheminVersFichierRouteMap
     * @param string $repertoireControlleurs
     * @param string $repertoireTemplates
     * @param string $repertoireLocales
     */
    public function genererConfigDepuisFichiers(
        $cheminVersFichierConfig,
        $cheminVersFichierRouteMap,
        $repertoireControlleurs,
        $repertoireTemplates,
        $repertoireLocales)
    {
        $this->setConfig(
            $this->_conteneur->getConfig(
                $cheminVersFichierConfig, $cheminVersFichierRouteMap, $repertoireControlleurs, $repertoireTemplates,
                $repertoireLocales
            )
        );
    }

    /**
     * @throws \Exception
     * @return HtmlReponse
     */
    public function process()
    {
        try {
            $reponse = $this->_conteneur->getDispatcher($this->_config)->executerActionRequise();

            $htmlReponse = $this->_conteneur->getResponseManager($this->_config)->getHtmlResponse($reponse);
        } catch (\Exception $exception) {
            if ($this->_config->isProdVersion() === true) {
                $htmlReponse = new HtmlReponse(500);
            } else {
                throw $exception;
            }
        }

        return $htmlReponse;
    }
}