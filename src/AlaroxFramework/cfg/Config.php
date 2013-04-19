<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\configs\RestInfos;
use AlaroxFramework\cfg\configs\Server;
use AlaroxFramework\cfg\i18n\Internationalization;
use AlaroxFramework\cfg\i18n\Langue;
use AlaroxFramework\cfg\route\RouteMap;

class Config
{
    /**
     * @var string
     */
    private $_version;

    /**
     * @var RouteMAp
     */
    private $_routeMap;

    /**
     * @var ControllerFactory
     */
    private $_ctrlFactory;

    /**
     * @var RestInfos
     */
    private $_restInfos;

    /**
     * @var Internationalization
     */
    private $_i18nConfig;

    /**
     * @var Server
     */
    private $_server;

    /**
     * @var array
     */
    private $_globals;

    /**
     * @var string
     */
    private $_templateDirectory;

    /**
     * @var array
     */
    private static $valeursMinimales = array(
        'Website_version',
        'RestServer',
        'TemplateVars',
        'InternationalizationConfig',
        'RestServer.Url',
        'RestServer.Format',
        'RestServer.Authentification',
        'RestServer.Authentification.Enabled',
        'RestServer.Authentification.Username',
        'RestServer.Authentification.PassKey',
        'InternationalizationConfig.Enabled',
        'InternationalizationConfig.Default_language',
        'InternationalizationConfig.Available');

    /**
     * @return Internationalization
     */
    public function getI18nConfig()
    {
        return $this->_i18nConfig;
    }

    /**
     * @return ControllerFactory
     */
    public function getCtrlFactory()
    {
        return $this->_ctrlFactory;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return $this->_globals;
    }

    /**
     * @return RestInfos
     */
    public function getRestInfos()
    {
        return $this->_restInfos;
    }

    /**
     * @return RouteMap
     */
    public function getRouteMap()
    {
        return $this->_routeMap;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * @return string
     */
    public function getTemplateDirectory()
    {
        return $this->_templateDirectory;
    }

    /**
     * @return boolean
     */
    public function isProdVersion()
    {
        return strcmp(strtolower($this->_version), 'prod') == 0;
    }

    /**
     * @param ControllerFactory $controllerFactory
     * @throws \InvalidArgumentException
     */
    public function setCtrlFactory($controllerFactory)
    {
        if (!$controllerFactory instanceof ControllerFactory) {
            throw new \InvalidArgumentException('Expected parameter 1 controllerFactory to be instance of ControllerFactory.');
        }

        $this->_ctrlFactory = $controllerFactory;
    }

    /**
     * @param array $globals
     * @throws \InvalidArgumentException
     */
    public function setGlobals($globals)
    {
        if (!is_array($globals)) {
            throw new \InvalidArgumentException('Expected parameter 1 globals to be array.');
        }

        $this->_globals = $globals;
    }

    /**
     * @param Internationalization $i18n
     * @throws \InvalidArgumentException
     */
    public function setI18nConfig($i18n)
    {
        if (!$i18n instanceof Internationalization) {
            throw new \InvalidArgumentException('Expected parameter 1 i18n to be instance of Internationalization.');
        }

        $this->_i18nConfig = $i18n;
    }

    /**
     * @param RouteMap $routeMap
     * @throws \InvalidArgumentException
     */
    public function setRouteMap($routeMap)
    {
        if (!$routeMap instanceof RouteMap) {
            throw new \InvalidArgumentException('Expected parameter 1 routeMap to be instance of RouteMap.');
        }

        $this->_routeMap = $routeMap;
    }

    /**
     * @param RestInfos $restInfos
     * @throws \InvalidArgumentException
     */
    public function setRestInfos($restInfos)
    {
        if (!$restInfos instanceof RestInfos) {
            throw new \InvalidArgumentException('Expected parameter 1 restInfos to be instance of RestInfos.');
        }

        $this->_restInfos = $restInfos;
    }

    /**
     * @param Server $server
     * @throws \InvalidArgumentException
     */
    public function setServer($server)
    {
        if (!$server instanceof Server) {
            throw new \InvalidArgumentException('Expected parameter 1 server to be instance of Server.');
        }

        $this->_server = $server;
    }

    /**
     * @param string $repertoireTemplates
     */
    public function setTemplateDirectory($repertoireTemplates)
    {
        $this->_templateDirectory = $repertoireTemplates;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->_version = $version;
    }

    /**
     * @param File $fichier
     * @param string $repertoireLocales
     * @throws \Exception
     */
    public function recupererConfigDepuisFichier($fichier, $repertoireLocales)
    {
        if ($fichier->fileExist() === true) {
            $tabCfg = $fichier->loadFile();
        } else {
            throw new \Exception(sprintf('Config file %s does not exist.', $fichier->getPathToFile()));
        }

        foreach (self::$valeursMinimales as $uneValeurMinimale) {
            if (is_null(array_multisearch($uneValeurMinimale, $tabCfg, true))) {
                throw new \Exception(sprintf('Missing config key "%s".', $uneValeurMinimale));
            }
        }

        $this->setVersion(array_multisearch('Website_version', $tabCfg, true));
        $this->setGlobals(array_multisearch('TemplateVars', $tabCfg, true));

        $i18n = new Internationalization();
        if ($langueActif = array_multisearch('InternationalizationConfig.Enabled', $tabCfg, true) === true) {
            $defaultLanguageId = array_multisearch('InternationalizationConfig.Default_language', $tabCfg, true);

            $i18n->setActif(true);
            $i18n->setDossierLocales($repertoireLocales);
            foreach (array_multisearch('InternationalizationConfig.Available', $tabCfg, true) as $clef => $langueDispo)
            {
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
                    'Default language "%s" not found in available language list.', $defaultLanguageId
                ));
            }

            $i18n->setLangueDefaut($langueDefaut);
        }
        $this->setI18nConfig($i18n);


        $restInfos = new RestInfos();
        $restInfos->parseRestInfos($tabCfg['RestServer']);
        $this->setRestInfos($restInfos);
    }
}