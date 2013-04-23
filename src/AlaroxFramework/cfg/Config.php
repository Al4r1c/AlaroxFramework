<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;
use AlaroxFramework\cfg\configs\ControllerFactory;
use AlaroxFramework\cfg\configs\RestInfos;
use AlaroxFramework\cfg\configs\Server;
use AlaroxFramework\cfg\configs\TemplateConfig;
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
     * @var TemplateConfig
     */
    private $_templateConfig;

    /**
     * @var array
     */
    private static $valeursMinimales = array(
        'Website_version',
        'RestServer',
        'TemplateConfig',
        'InternationalizationConfig',
        'RestServer.Url',
        'RestServer.Format',
        'RestServer.Authentification',
        'RestServer.Authentification.Enabled',
        'RestServer.Authentification.Username',
        'RestServer.Authentification.PassKey',
        'InternationalizationConfig.Enabled',
        'InternationalizationConfig.Default_language',
        'InternationalizationConfig.Available',
        'TemplateConfig.Cache',
        'TemplateConfig.Charset',
        'TemplateConfig.Variables'
    );

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
     * @return TemplateConfig
     */
    public function getTemplateConfig()
    {
        return $this->_templateConfig;
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
     * @param TemplateConfig $templateConfig
     * @throws \InvalidArgumentException
     */
    public function setTemplateConfig($templateConfig)
    {
        if (!$templateConfig instanceof TemplateConfig) {
            throw new \InvalidArgumentException('Expected parameter 1 templateConfig to be instance of TemplateConfig.');
        }

        $this->_templateConfig = $templateConfig;
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
     * @param string $repertoireTemplates
     * @param string $repertoireLocales
     * @throws \Exception
     */
    public function recupererConfigDepuisFichier($fichier, $repertoireTemplates, $repertoireLocales)
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
        $this->createTemplateConfig(array_multisearch('TemplateConfig', $tabCfg, true), $repertoireTemplates);
        $this->createI18nConfig(array_multisearch('InternationalizationConfig', $tabCfg, true), $repertoireLocales);
        $this->createRestInfos(array_multisearch('RestServer', $tabCfg, true));
    }

    /**
     * @param array $tabTemplateConfig
     * @param string $repertoireTemplates
     */
    private function createTemplateConfig($tabTemplateConfig, $repertoireTemplates)
    {
        $tabTemplateConfig = array_change_key_case($tabTemplateConfig, CASE_LOWER);

        $templateConfig = new TemplateConfig();
        $templateConfig->setCache($tabTemplateConfig['cache']);
        $templateConfig->setCharset($tabTemplateConfig['charset']);
        $templateConfig->setGlobalVariables($tabTemplateConfig['variables']);
        $templateConfig->setTemplateDirectory($repertoireTemplates);

        $this->setTemplateConfig($templateConfig);
    }

    /**
     * @param array $tabI18nConfig
     * @param string $repertoireLocales
     * @throws \Exception
     */
    private function createI18nConfig($tabI18nConfig, $repertoireLocales)
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
                    'Default language "%s" not found in available language list.', $defaultLanguageId
                ));
            }

            $i18n->setLangueDefaut($langueDefaut);
        }

        $this->setI18nConfig($i18n);
    }

    /**
     * @param array $tabRestServerInfos
     */
    private function createRestInfos($tabRestServerInfos)
    {
        $restInfos = new RestInfos();
        $restInfos->parseRestInfos($tabRestServerInfos);

        $this->setRestInfos($restInfos);
    }
}