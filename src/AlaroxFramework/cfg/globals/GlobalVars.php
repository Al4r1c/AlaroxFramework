<?php
namespace AlaroxFramework\cfg\globals;

class GlobalVars
{
    /**
     * @var array
     */
    private $_staticVars = array();

    /**
     * @var RemoteVars
     */
    private $_remoteVars;

    /**
     * @return RemoteVars
     */
    public function getRemoteVarsExecutees()
    {
        return $this->_remoteVars->getRemoteVarsExecutees();
    }

    /**
     * @return array
     */
    public function getStaticVars()
    {
        return $this->_staticVars;
    }

    /**
     * @param string $clef
     * @param mixed $uneVarStatic
     */
    public function addStaticVar($clef, $uneVarStatic)
    {
        $this->_staticVars[$clef] = $uneVarStatic;
    }

    /**
     * @param RemoteVars $remoteVars
     */
    public function setRemoteVars($remoteVars)
    {
        $this->_remoteVars = $remoteVars;
    }
}