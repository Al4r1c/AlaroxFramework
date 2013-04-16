<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\configs\RestInfos;
use AlaroxFramework\cfg\configs\Server;
use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\exceptions\ErreurHandler;
use AlaroxFramework\traitement\Dispatcher;
use AlaroxFramework\traitement\restclient\CurlClient;
use AlaroxFramework\traitement\restclient\RestClient;
use AlaroxFramework\utils\Curl;
use AlaroxFramework\utils\parser\Parser;
use AlaroxFramework\utils\parser\ParserFactory;

class Conteneur
{
    /**
     * @param $cheminVersFichier
     * @return File
     */
    public function getFile($cheminVersFichier)
    {
        $alaroxFile = new AlaroxFile();

        return $alaroxFile->getFile($cheminVersFichier);
    }

    /**
     * @return ErreurHandler
     */
    public function getErreurHandler()
    {
        return new ErreurHandler();
    }

    /**
     * @param string $cheminVersFichierConfig
     * @param string $cheminVersRouteMap
     * @param string $repertoireControlleurs
     * @return Config
     */
    public function getConfig($cheminVersFichierConfig, $cheminVersRouteMap, $repertoireControlleurs)
    {
        $config = new Config();
        $config->recupererConfigDepuisFichier($this->getFile($cheminVersFichierConfig));
        $config->setServer($this->getServer());
        $config->setCtrlFactory($this->getControllerFactory($repertoireControlleurs));

        if (!empty($cheminVersRouteMap)) {
            $config->setRouteMap($this->getRoute($cheminVersRouteMap));
        }

        return $config;
    }

    /**
     * @param $cheminVersRouteMap
     * @return RouteMap
     */
    public function getRoute($cheminVersRouteMap)
    {
        $routeMap = new RouteMap();
        $routeMap->setRouteMapDepuisFichier($this->getFile($cheminVersRouteMap));

        return $routeMap;
    }

    /**
     * @param string $repertoireControlleurs
     * @return ControllerFactory
     */
    public function getControllerFactory($repertoireControlleurs)
    {
        $ctrlFactory = new ControllerFactory();

        $controllers = array();
        $scanDir = scandir($repertoireControlleurs);
        foreach ($scanDir as $uneEntite) {
            if (is_file($file = $repertoireControlleurs . DIRECTORY_SEPARATOR . $uneEntite)) {
                $php_code = file_get_contents($file);
                $tokens = token_get_all($php_code);

                $classToken = false;
                $namespaceToken = false;
                $currentNamespace = '';

                foreach ($tokens as $token) {
                    if (is_array($token)) {
                        if ($token[0] == T_NAMESPACE) {
                            $namespaceToken = true;
                        } elseif ($namespaceToken && ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR)) {
                            $currentNamespace .= $token[1];
                        } elseif ($namespaceToken && ($token[0] == T_CLASS || $token[0] == T_USE)) {
                            $namespaceToken = false;
                        }

                        if ($token[0] == T_CLASS) {
                            $classToken = true;
                        } elseif ($classToken && $token[0] == T_STRING) {
                            $controllers[] = $currentNamespace . '\\' . $token[1];
                            break;
                        }
                    }
                }
            }
        }

        $ctrlFactory->setListControllers($controllers);

        return $ctrlFactory;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        $server = new Server();
        $server->setServeurVariables($_SERVER);

        return $server;
    }

    /**
     * @param Config $config
     * @throws \InvalidArgumentException
     * @return Dispatcher
     */
    public function getDispatcher($config)
    {
        if (!$config instanceof Config) {
            throw new \InvalidArgumentException('Expected parameter 1 config to be instance of Config.');
        }

        $dispatcher = new Dispatcher();
        $dispatcher->setUriDemandee($config->getServer()->getUneVariableServeur('REQUEST_URI_NODIR'));
        $dispatcher->setRestInfos($config->getRestInfos());
        $dispatcher->setRouteMap($config->getRouteMap());
        $dispatcher->setControllerFactory($config->getCtrlFactory());
        $dispatcher->setRestClient($this->getRestClient($dispatcher->getRestInfos()));

        return $dispatcher;
    }

    /**
     * @param RestInfos $restInfos
     * @return RestClient
     */
    private function getRestClient($restInfos)
    {
        $restClient = new RestClient();
        $restClient->setRestInfos($restInfos);
        $restClient->setCurlClient($this->getCurlClient());

        return $restClient;
    }

    /**
     * @return CurlClient
     */
    private function getCurlClient()
    {
        $curlClient = new CurlClient();
        $curlClient->setCurl(new Curl());
        $curlClient->setParser($this->getParser());
        $curlClient->setTime(time());

        return $curlClient;
    }

    /**
     * @return Parser
     */
    private function getParser()
    {
        $parser = new Parser();
        $parser->setParserFactory(new ParserFactory());

        return $parser;
    }
}