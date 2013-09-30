<?php
namespace AlaroxFramework;

use AlaroxFileManager\AlaroxFile;
use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\Config;
use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\configs\Server;
use AlaroxFramework\cfg\configs\TemplateConfig;
use AlaroxFramework\cfg\globals\GlobalVars;
use AlaroxFramework\cfg\globals\RemoteVars;
use AlaroxFramework\cfg\i18n\Internationalization;
use AlaroxFramework\cfg\i18n\Langue;
use AlaroxFramework\cfg\rest\Auth;
use AlaroxFramework\cfg\rest\RestServer;
use AlaroxFramework\cfg\rest\RestServerManager;
use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\exceptions\ErreurHandler;
use AlaroxFramework\reponse\ReponseManager;
use AlaroxFramework\reponse\TemplateManager;
use AlaroxFramework\traitement\Dispatcher;
use AlaroxFramework\utils\compressor\Compressor;
use AlaroxFramework\utils\compressor\CompressorFactory;
use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\parser\Parser;
use AlaroxFramework\utils\parser\ParserFactory;
use AlaroxFramework\utils\restclient\Curl;
use AlaroxFramework\utils\restclient\CurlClient;
use AlaroxFramework\utils\restclient\RestClient;
use AlaroxFramework\utils\session\Session;
use AlaroxFramework\utils\session\SessionClient;
use AlaroxFramework\utils\tools\Tools;
use AlaroxFramework\utils\twig\TwigEnvFactory;
use AlaroxFramework\utils\view\ViewFactory;

class Conteneur
{
    /**
     * @var Config
     */
    private $_config;

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

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
     */
    public function createConfiguration($arrayConfiguration)
    {
        $this->_config = new Config();

        $tabCfg =
            array_change_key_case(
                $this->_config->validerEtChargerFichier($this->getFile($arrayConfiguration['configFile'])),
                CASE_LOWER
            );

        $this->_config->setVersion($tabCfg['website_version']);

        if ($this->_config->isProdVersion() === true) {
            $this->getErreurHandler()->setHandler();
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', 'off');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 'on');
        }

        $this->_config->setRestClient($this->getRestClient($this->getRestServerManager($tabCfg['restserver'])));

        $this->_config->setTemplateConfig(
            $this->getTemplateConfig(
                $tabCfg['templateconfig'],
                $arrayConfiguration['templatesPath']
            )
        );

        $this->_config->setI18nConfig(
            $this->getI18nConfig(
                $tabCfg['internationalizationconfig'],
                $arrayConfiguration['localesPath']
            )
        );

        $this->_config->setServer($this->getServer());

        $this->_config->setCtrlFactory(
            $this->getControllerFactory($arrayConfiguration['controllersPath'], $_POST, $_FILES)
        );

        $this->_config->setRouteMap($this->getRoute($arrayConfiguration['routeFile']));
    }

    /**
     * @param array $tabTemplateConfig
     * @param string $repertoireTemplates
     * @return TemplateConfig
     */
    private function getTemplateConfig($tabTemplateConfig, $repertoireTemplates)
    {
        $tabTemplateConfig = array_change_key_case($tabTemplateConfig, CASE_LOWER);

        $templateConfig = new TemplateConfig();
        $templateConfig->setCache($tabTemplateConfig['cache']);
        $templateConfig->setCharset($tabTemplateConfig['charset']);
        $templateConfig->setGlobalVariables(
            $this->getGlobalVars($tabTemplateConfig['variables'])
        );
        $templateConfig->setTemplateDirectory($repertoireTemplates);


        $viewFactory = $this->getViewFactory();

        if (isset($tabTemplateConfig['notfoundpage']) && is_array($tabTemplateConfig['notfoundpage'])) {
            $notFoundView = $viewFactory->getView($tabTemplateConfig['notfoundpage']['type']);
            $notFoundView->renderView($tabTemplateConfig['notfoundpage']['value']);
        } else {
            $notFoundView = $viewFactory->getView('plain');
            $notFoundView->renderView(
                '<h1>HTTP 404 ' . Tools::getMessageHttpCode(404)[0] . '</h1><h2>' .
                Tools::getMessageHttpCode(404)[1] .
                '</h2><h3>{{ errorMessage }}</h3>'
            );
        }

        $templateConfig->setNotFoundTemplate($notFoundView);

        return $templateConfig;
    }

    /**
     * @return ViewFactory
     */
    private function getViewFactory()
    {
        return new ViewFactory();
    }

    /**
     * @param array $tabI18nConfig
     * @param string $repertoireLocales
     * @return Internationalization
     * @throws \Exception
     */
    private function getI18nConfig($tabI18nConfig, $repertoireLocales)
    {
        $tabI18nConfig = array_change_key_case($tabI18nConfig, CASE_LOWER);

        $i18n = new Internationalization();
        if (($langueActif = $tabI18nConfig['enabled']) === true) {
            $defaultLanguageId = $tabI18nConfig['default_language'];

            $i18n->setActif(true);
            $i18n->setDossierLocales($repertoireLocales);
            foreach ($tabI18nConfig['available'] as $clef => $langueDispo) {
                $langueDispoObj = new Langue();
                $langueDispoObj->setIdentifiant($clef);
                $langueDispoObj->setAlias($langueDispo['alias']);
                $langueDispoObj->setNomFichier($langueDispo['filename']);
                $i18n->addLanguesDispo($langueDispoObj);

                if (strcmp($defaultLanguageId, $langueDispoObj->getIdentifiant()) == 0) {
                    $langueDefaut = $langueDispoObj;
                }
            }

            if (!isset($langueDefaut)) {
                throw new \Exception(sprintf(
                    'Default language "%s" not found in available language list.',
                    $defaultLanguageId
                ));
            }

            $i18n->setLangueDefaut($langueDefaut);
        }

        return $i18n;
    }

    /**
     * @param array $tabRestServer
     * @return RestServerManager
     */
    private function getRestServerManager($tabRestServer)
    {
        $restServerManager = new RestServerManager();

        foreach ($tabRestServer as $clefServer => $unRestServer) {
            $restServerManager->addRestServer($clefServer, $this->getRestServer($unRestServer));
        }

        return $restServerManager;
    }

    /**
     * @param array $unRestServer
     * @throws \Exception
     * @return RestServer
     */
    private function getRestServer($unRestServer)
    {
        $valeursMinimales = array('Url', 'Format');

        foreach ($valeursMinimales as $uneValeurMinimale) {
            if (is_null(array_multisearch($uneValeurMinimale, $unRestServer))) {
                throw new \Exception(sprintf('Missing config key "%s".', $uneValeurMinimale));
            }
        }

        $restServer = new RestServer();

        $unRestServer = array_change_key_case_recursive($unRestServer, CASE_LOWER);

        $restServer->setUrl($unRestServer['url']);
        $restServer->setFormatEnvoi($unRestServer['format']);
        if (
            isset($unRestServer['authentification'])
            && is_array($unRestServer['authentification'])
            && $unRestServer['authentification']['enabled'] === true
        ) {
            $restServer->setAuth($this->getAuth($unRestServer['authentification']));
        }

        if (isset($unRestServer['parameters']) && is_array($unRestServer['parameters'])) {
            foreach ($unRestServer['parameters'] as $clefParam => $unParametre) {
                $restServer->addParametreUri($clefParam, $unParametre);
            }
        }

        if (isset($unRestServer['compression'])) {
            $restServer->setCompressor($unRestServer['compression']);
        }

        return $restServer;
    }

    private function getAuth($tabAuth)
    {
        $valeursMinimales = array('method', 'username', 'passkey');

        foreach ($valeursMinimales as $uneValeurMinimale) {
            if (!array_key_exists($uneValeurMinimale, $tabAuth)) {
                throw new \Exception(sprintf('Missing Authentification key "%s".', $uneValeurMinimale));
            }
        }

        $auth = new Auth();

        $auth->setAuthentifMethode($tabAuth['method']);
        $auth->setUsername($tabAuth['username']);
        $auth->setPrivateKey($tabAuth['passkey']);

        return $auth;
    }

    /**
     * @param $arrayVariables
     * @return GlobalVars
     */
    private function getGlobalVars($arrayVariables)
    {
        $globalVars = new GlobalVars();

        $arrayVariables = array_change_key_case($arrayVariables, CASE_LOWER);

        if (!empty($arrayVariables['static'])) {
            foreach ($arrayVariables['static'] as $clef => $uneVarStatic) {
                $globalVars->addStaticVar($clef, $uneVarStatic);
            }
        }

        $globalVars->setRemoteVars($this->getRemoteVars($arrayVariables['remote']));

        return $globalVars;
    }

    /**
     * @param $arrayRemote
     * @throws \InvalidArgumentException
     * @return RemoteVars
     */
    private function getRemoteVars($arrayRemote)
    {
        $remoteVars = new RemoteVars();

        $remoteVars->setRestClient($this->getConfig()->getRestClient());

        if (!empty($arrayRemote)) {
            if (!is_array($arrayRemote)) {
                throw new \InvalidArgumentException('Malformed configuration TemplateConfig.Remote.');
            }

            foreach ($arrayRemote as $clef => $uneVarRemote) {
                foreach (array('server', 'uri', 'method') as $uneClefObligatoire) {
                    if (!array_key_exists($uneClefObligatoire, $uneVarRemote)) {
                        throw new \InvalidArgumentException(sprintf(
                            'Missing key "%s" in Remote section for "%s".',
                            $uneClefObligatoire,
                            $clef
                        ));
                    }
                }

                $remoteVars->addRemoteVar(
                    $uneVarRemote['server'],
                    $clef,
                    new ObjetRequete($uneVarRemote['uri'], $uneVarRemote['method'])
                );
            }
        }

        return $remoteVars;
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
     * @param array $postVars
     * @param array $filesVars
     * @return ControllerFactory
     */
    private function getControllerFactory($repertoireControlleurs, $postVars, $filesVars)
    {
        $ctrlFactory = new ControllerFactory();
        $ctrlFactory->setRestClient($this->getConfig()->getRestClient());

        $controllers = $this->scanControllerDir($repertoireControlleurs);

        if (!empty($filesVars)) {
            $postVars['uploadedFile'] = $filesVars;
        }

        $ctrlFactory->setListControllers($controllers, $this->getSessionClient(), $postVars);

        return $ctrlFactory;
    }

    /**
     * @param string $repertoireActuel
     * @return array
     */
    private function scanControllerDir($repertoireActuel)
    {
        $arrayControllers = array();

        foreach (scandir($repertoireActuel) as $uneEntite) {
            if (is_file($entitePath = $repertoireActuel . DIRECTORY_SEPARATOR . $uneEntite) &&
                endsWith($entitePath, '.php')
            ) {
                $php_code = file_get_contents($entitePath);
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
                            $arrayControllers[] = $currentNamespace . '\\' . $token[1];
                            break;
                        }
                    }
                }
            } elseif (is_dir($entitePath) && !startsWith($uneEntite, '.')) {
                $arrayControllers = array_merge($arrayControllers, $this->scanControllerDir($entitePath));
            }
        }

        return $arrayControllers;
    }

    /**
     * @return mixed
     */
    private function &getAndStartSession()
    {
        $session = new Session();
        $session->startSession();

        return $session->getSession();
    }

    /**
     * @return SessionClient
     */
    private function getSessionClient()
    {
        $sessionRef = & $this->getAndStartSession();

        $sessionClient = new SessionClient();
        $sessionClient->setSessionRef($sessionRef);

        return $sessionClient;
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
     * @throws \InvalidArgumentException
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        $dispatcher = new Dispatcher();

        $dispatcher->setUriDemandee($this->getConfig()->getServer()->getUneVariableServeur('REQUEST_URI_NODIR'));
        $dispatcher->setI18nActif($this->getConfig()->getI18nConfig()->isActivated());
        $dispatcher->setRouteMap($this->getConfig()->getRouteMap());
        $dispatcher->setControllerFactory($this->getConfig()->getCtrlFactory());
        $dispatcher->setViewFactory($this->getViewFactory());

        return $dispatcher;
    }

    /**
     * @param RestServerManager $restServerManager
     * @return RestClient
     */
    private function getRestClient($restServerManager)
    {
        $restClient = new RestClient();

        $restClient->setRestServerManager($restServerManager);
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
        $curlClient->setCompressor($this->getCompressor());
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
     * @return Compressor
     */
    private function getCompressor()
    {
        $compressor = new Compressor();

        $compressor->setCompressorFactory(new CompressorFactory());

        return $compressor;
    }

    /**
     * @return ReponseManager
     */
    public function getResponseManager()
    {
        $responseManager = new ReponseManager();

        $responseManager->setTemplateManager($this->getTemplateManager());
        $responseManager->setNotFoundTemplate($this->getConfig()->getTemplateConfig()->getNotFoundTemplate());

        return $responseManager;
    }

    /**
     * @return TemplateManager
     */
    private function getTemplateManager()
    {
        $templateManager = new TemplateManager();

        $templateManager->setGlobalVar($this->getConfig()->getTemplateConfig()->getGlobalVariables());
        $templateManager->setTwigEnvFactory($this->getTwigFactory($this->getConfig()->getTemplateConfig()));

        if ($this->getConfig()->getI18nConfig()->isActivated() === true) {
            $arrayLanguages = array();

            foreach ($this->getConfig()->getI18nConfig()->getLanguesDispo() as $uneLangueDispo) {
                $arrayLanguages[$uneLangueDispo->getIdentifiant()] = $uneLangueDispo->getNomFichier();
            }

            $templateManager->addExtension(
                new \Twig_I18nExtension_Extension_I18n(
                    $this->getConfig()->getI18nConfig()->getLangueDefaut()->getAlias(),
                    $this->getConfig()->getI18nConfig()->getDossierLocales(),
                    $arrayLanguages
                )
            );
        }

        return $templateManager;
    }

    /**
     * @param TemplateConfig $templateConfig
     * @return TwigEnvFactory
     */
    private function getTwigFactory($templateConfig)
    {
        $twigEnvFactory = new TwigEnvFactory();
        $twigEnvFactory->setTemplateConfig($templateConfig);

        return $twigEnvFactory;
    }
}