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
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', 'off');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'on');
        }

        $this->_config = $config;
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

        $this->setConfig($this->_conteneur->dispatchConfig($configurationArray));
    }

    /**
     * @throws \Exception
     * @return HtmlReponse
     */
    public function process()
    {
        try {
            $reponse = $this->_conteneur->getDispatcher($this->_config)->executerActionRequise();

            try {
                $htmlReponse = $this->_conteneur->getResponseManager($this->_config)->getHtmlResponse($reponse);
            } catch (\Exception $exception) {
                if ($this->_config->isProdVersion() === true) {
                    $htmlReponse = new HtmlReponse(500);
                } else {
                    throw $exception;
                }
            }

        } catch (\Exception $exception) {
            if ($this->_config->isProdVersion() === true) {
                $htmlReponse = new HtmlReponse(404);
            } else {
                throw $exception;
            }
        }


        return $htmlReponse;
    }
}