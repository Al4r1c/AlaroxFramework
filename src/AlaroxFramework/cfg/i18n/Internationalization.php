<?php
namespace AlaroxFramework\cfg\i18n;

class Internationalization
{
    /**
     * @var boolean
     */
    private $_actif = false;

    /**
     * @var Langue
     */
    private $_langueDefaut;

    /**
     * @var Langue[]
     */
    private $_languesDispo = array();

    /**
     * @var string
     */
    private $_dossierLocales;

    /**
     * @return boolean
     */
    public function isActivated()
    {
        return $this->_actif;
    }

    /**
     * @return string
     */
    public function getDossierLocales()
    {
        return $this->_dossierLocales;
    }

    /**
     * @return Langue
     */
    public function getLangueDefaut()
    {
        return $this->_langueDefaut;
    }

    /**
     * @return Langue[]
     */
    public function getLanguesDispo()
    {
        return $this->_languesDispo;
    }

    /**
     * @param string $id
     * @return Langue
     */
    public function getLanguesDispoById($id)
    {
        foreach ($this->_languesDispo as $uneLangueDispo) {
            if (strcmp($id, $uneLangueDispo->getIdentifiant()) == 0) {
                return $uneLangueDispo;
            }
        }

        return false;
    }

    /**
     * @param boolean $actif
     * @throws \InvalidArgumentException
     */
    public function setActif($actif)
    {
        if (is_null($varBool = filter_var($actif, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
            throw new \InvalidArgumentException('Expected parameter 1 actif to be boolean.');
        }

        $this->_actif = $varBool;
    }

    /**
     * @param string $dossierLocales
     */
    public function setDossierLocales($dossierLocales)
    {
        $this->_dossierLocales = $dossierLocales;
    }

    /**
     * @param Langue $langueDefaut
     * @throws \InvalidArgumentException
     */
    public function setLangueDefaut($langueDefaut)
    {
        if (!$langueDefaut instanceof Langue) {
            throw new \InvalidArgumentException('Expected parameter 1 langueDefaut to be instance of Langue.');
        }

        $this->_langueDefaut = $langueDefaut;
    }

    /**
     * @param Langue $languesDispo
     * @throws \InvalidArgumentException
     */
    public function addLanguesDispo($languesDispo)
    {
        if (!$languesDispo instanceof Langue) {
            throw new \InvalidArgumentException('Expected parameter 1 languesDispo to be instance of Langue.');
        }

        $this->_languesDispo[] = $languesDispo;
    }
}