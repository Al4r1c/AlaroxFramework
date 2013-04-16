<?php
namespace AlaroxFramework\cfg\i18n;

class Internationalization
{
    /**
     * @var boolean
     */
    private $_actif = false;

    /**
     * @var string
     */
    private $_langueDefaut;

    /**
     * @var Langue[]
     */
    private $_languesDispo = array();

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
    public function getLangueDefaut()
    {
        return $this->_langueDefaut;
    }

    /**
     * @param string $alias
     * @return Langue
     */
    public function getLanguesDispoByAlias($alias)
    {
        foreach ($this->_languesDispo as $uneLangueDispo) {
            if (strcmp(strtolower($alias), $uneLangueDispo->getAlias()) == 0) {
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
     * @param string $langueDefaut
     * @throws \InvalidArgumentException
     */
    public function setLangueDefaut($langueDefaut)
    {
        if (!is_string($langueDefaut)) {
            throw new \InvalidArgumentException('Expected parameter 1 langueDefaut to be string.');
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
            throw new \InvalidArgumentException('Expected parameter 1 languesDispo to be instance of LanguesDispo.');
        }

        $this->_languesDispo[] = $languesDispo;
    }
}