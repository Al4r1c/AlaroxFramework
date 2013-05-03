<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\configs\RestInfos;
use AlaroxFramework\cfg\configs\Server;
use AlaroxFramework\cfg\configs\TemplateConfig;
use AlaroxFramework\cfg\i18n\Internationalization;
use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\exceptions\ErreurHandler;
use AlaroxFramework\reponse\ReponseManager;
use AlaroxFramework\reponse\TemplateManager;
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
     * @param array $arrayConfiguration
     * @return Config
     */
    public function dispatchConfig($arrayConfiguration)
    {
        $config = new Config();
        $config->recupererConfigDepuisFichier(
            $this->getFile($arrayConfiguration['configFile']),
            $arrayConfiguration['templatesPath'],
            $arrayConfiguration['localesPath']
        );
        $config->setServer($this->getServer());
        $config->setCtrlFactory($this->getControllerFactory($arrayConfiguration['controllersPath']));
        $config->setRouteMap($this->getRoute($arrayConfiguration['routeFile']));

        return $config;
    }

    /**
     * @param $cheminVersRouteMap
     * @return RouteMap
     */
    private function getRoute($cheminVersRouteMap)
    {
        $routeMap = new RouteMap();
        $routeMap->setRouteMapDepuisFichier($this->getFile($cheminVersRouteMap));

        return $routeMap;
    }

    /**
     * @param string $repertoireControlleurs
     * @return ControllerFactory
     */
    private function getControllerFactory($repertoireControlleurs)
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
    private function getServer()
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
        $dispatcher->setI18nActif($config->getI18nConfig()->isActivated());
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

    /**
     * @param Config $config
     * @return ReponseManager
     */
    public function getResponseManager($config)
    {
        $responseManager = new ReponseManager();
        $responseManager->setTemplateManager(
            $this->getTemplateManager($config->getTemplateConfig(), $config->getI18nConfig())
        );

        return $responseManager;
    }

    /**
     * @param TemplateConfig $templateConfig
     * @param Internationalization $i18nConfig
     * @param string $templateDirectory
     * @return TemplateManager
     */
    private function getTemplateManager($templateConfig, $i18nConfig)
    {
        $templateManager = new TemplateManager();
        $templateManager->setGlobalVar($templateConfig->getGlobalVariables());
        $templateManager->setTwigEnv($this->initTwig($templateConfig));

        if ($i18nConfig->isActivated() === true) {
            $arrayLanguages = array();

            foreach ($i18nConfig->getLanguesDispo() as $uneLangueDispo) {
                $arrayLanguages[$uneLangueDispo->getIdentifiant()] = $uneLangueDispo->getNomFichier();
            }

            $templateManager->addExtension(
                new \Twig_I18nExtension_Extension_I18n(
                    $i18nConfig->getLangueDefaut()->getAlias(),
                    $i18nConfig->getDossierLocales(),
                    $arrayLanguages
                )
            );
        }

        return $templateManager;
    }

    /**
     * @param TemplateConfig $templateConfig
     * @return \Twig_Environment
     */
    private function initTwig($templateConfig)
    {
        $loader = new \Twig_Loader_Filesystem(array($templateConfig->getTemplateDirectory()));

        $options = array(
            'cache' => false,
            'charset' => $templateConfig->getCharset(),
            'autoescape' => 'html',
            'strict_variables' => false,
            'optimizations' => -1
        );

        if ($templateConfig->isCacheEnabled() === true) {
            $options = array(
                'cache' => $templateConfig->getTemplateDirectory() . '/cache/',
                'auto_reload' => true
            ) + $options;
        }

        $twigEnv = new \Twig_Environment($loader, $options);

        return $twigEnv;
    }
}