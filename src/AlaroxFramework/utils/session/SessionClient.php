<?php
namespace AlaroxFramework\utils\session;

class SessionClient
{
    /**
     * @var array
     */
    private $_sessionRef;

    /**
     * @param array $sessionRef
     */
    public function setSessionRef(&$sessionRef)
    {
        $this->_sessionRef = & $sessionRef;
    }

    /**
     * @param string $clef
     * @return mixed
     */
    public function getSessionValue($clef)
    {
        return isset($this->_sessionRef[$clef]) ? $this->_sessionRef[$clef] : null;
    }

    /**
     * @param string $clef
     * @param mixed $valeur
     */
    public function setSessionValue($clef, $valeur)
    {
        $this->_sessionRef[$clef] = $valeur;
    }

    /**
     * @param string $clef
     */
    public function deleteValue($clef)
    {
        if (isset($this->_sessionRef[$clef])) {
            unset($this->_sessionRef[$clef]);
        }
    }
}