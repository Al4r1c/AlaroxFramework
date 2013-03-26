<?php
namespace AlaroxFramework\cfg;

use AlaroxFileManager\FileManager\File;

class Config
{
    private $_tabConfiguration;

    private static $valeursMinimales = array('ControllerConfig',
        'TemplateConfig',
        'InternationalizationConfig',
        'ControllerConfig.Default_controller',
        'ControllerConfig.RestServer_url',
        'TemplateConfig.Name',
        'TemplateConfig.Media_url',
        'InternationalizationConfig.Enabled',
        'InternationalizationConfig.Default_language',
        'InternationalizationConfig.Available');

    /**
     * @param File $fichier
     * @throws \Exception
     */
    public function recupererConfigDepuisFichier($fichier)
    {
        $tabCfg = $fichier->loadFile();

        foreach (self::$valeursMinimales as $uneValeurMinimale) {
            if (is_null($this->rechercheValeurTableauMultidim($uneValeurMinimale, $tabCfg))) {
                throw new \Exception();
            }
        }

        $this->_tabConfiguration = $tabCfg;
    }

    /**
     * @param string $clefConfig
     * @return mixed|null
     */
    public function getConfigValeur($clefConfig)
    {
        return $this->rechercheValeurTableauMultidim($clefConfig, $this->_tabConfiguration);
    }

    /**
     * @param string $clefRecherchee
     * @param array $tableauConcerne
     * @return mixed|null
     */
    private function rechercheValeurTableauMultidim($clefRecherchee, $tableauConcerne)
    {
        foreach (array_map('strtolower', explode('.', $clefRecherchee)) as $uneClef) {
            if (array_key_exists($uneClef, $tableauConcerne = array_change_key_case($tableauConcerne, CASE_LOWER))) {
                $tableauConcerne = $tableauConcerne[$uneClef];
            } else {
                return null;
            }
        }

        return $tableauConcerne;
    }
}