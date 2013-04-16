<?php
namespace AlaroxFramework\cfg\i18n;

class LangueDispo
{
    /**
     * @var string
     */
    private $_identifiant;

    /**
     * @var string
     */
    private $_alias;

    /**
     * @var string
     */
    private $_nomFichier;

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * @return string
     */
    public function getIdentifiant()
    {
        return $this->_identifiant;
    }

    /**
     * @return string
     */
    public function getNomFichier()
    {
        return $this->_nomFichier;
    }

    /**
     * @param string $alias
     * @throws \InvalidArgumentException
     */
    public function setAlias($alias)
    {
        if (!is_string($alias)) {
            throw new \InvalidArgumentException('Expected parameter 1 alias to be string.');
        }

        $this->_alias = $alias;
    }

    /**
     * @param string $identifiant
     * @throws \InvalidArgumentException
     */
    public function setIdentifiant($identifiant)
    {
        if (!is_string($identifiant)) {
            throw new \InvalidArgumentException('Expected parameter 1 identifiant to be string.');
        }
        $this->_identifiant = $identifiant;
    }

    /**
     * @param string $nomFichier
     * @throws \InvalidArgumentException
     */
    public function setNomFichier($nomFichier)
    {
        if (!is_string($nomFichier)) {
            throw new \InvalidArgumentException('Expected parameter 1 nomFichier to be string.');
        }

        $this->_nomFichier = $nomFichier;
    }
}