<?php
namespace AlaroxFramework;

use AlaroxFramework\cfg\Config;
use AlaroxFramework\traitement\RouteNotFoundException;
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
     * @return HtmlReponse
     */
    public function process()
    {
        try {
            $reponse = $this->_conteneur->getDispatcher()->executerActionRequise();

            try {
                $htmlReponse = $this->_conteneur->getResponseManager()->getHtmlResponse($reponse);
            } catch (\Exception $exception) {
                $htmlReponse = $this->htmlResponseError($exception);
            }
        } catch (RouteNotFoundException $exception) {
            try {
                $htmlReponse = $this->_conteneur->getResponseManager()->getNotFoundTemplate($exception->getMessage());
            } catch (\Exception $exception) {
                $htmlReponse = $this->htmlResponseError($exception);
            }
        }

        return $htmlReponse;
    }

    /**
     * @param \Exception $exception
     * @return HtmlReponse
     */
    private function htmlResponseError($exception)
    {
        return new HtmlReponse(500, $exception, true);
    }
}