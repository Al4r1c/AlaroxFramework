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
     * @var array
     */
    private static $_clefMinimalesConfig = array('configFile', 'routeFile', 'controllersPath', 'templatesPath');

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
     * @param array $configurationArray
     * @throws \InvalidArgumentException
     */
    public function genererConfig($configurationArray)
    {
        foreach (self::$_clefMinimalesConfig as $uneClefObligatoire) {
            if (!array_key_exists($uneClefObligatoire, $configurationArray)) {
                throw new \InvalidArgumentException(sprintf('Missing configuration key %s', $uneClefObligatoire));
            }
        }

        $configurationArray = $configurationArray + array('localesPath' => '');

        $this->_conteneur->createConfiguration($configurationArray);
    }

    /**
     * @throws \Exception
     * @return HtmlReponse
     */
    public function process()
    {
        try {
            $reponse = $this->_conteneur->getDispatcher()->executerActionRequise();
            try {
                $htmlReponse = $this->_conteneur->getResponseManager()->getHtmlResponse($reponse);
            } catch (\Exception $exception) {
                if ($this->_conteneur->getConfig()->isProdVersion() === true) {
                    $htmlReponse = new HtmlReponse(500);
                } else {
                    throw $exception;
                }
            }
        } catch (\Exception $exception) {
            if ($this->_conteneur->getConfig()->isProdVersion() === true) {
                $htmlReponse = new HtmlReponse(404);
            } else {
                throw $exception;
            }
        }


        return $htmlReponse;
    }
}