<?php
namespace AlaroxFramework\cfg\globals;

use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\restclient\RestClient;

class RemoteVars
{
    /**
     * @var ObjetRequete[]
     */
    private $_listeRemoteVars = array();

    /**
     * @var RestClient
     */
    private $_restClient;

    /**
     * @return array
     */
    public function getRemoteVarsExecutees()
    {
        $tabResult = array();

        foreach ($this->_listeRemoteVars as $serverKey => $temp) {
            foreach ($temp as $clef => $uneAction) {
                $tabResult[$clef] = $this->_restClient->executerRequete($serverKey, $uneAction)->toArray();
            }
        }

        return $tabResult;
    }

    /**
     * @param RestClient $restClient
     */
    public function setRestClient($restClient)
    {
        $this->_restClient = $restClient;
    }

    /**
     * @param string $serverKey
     * @param string $clef
     * @param ObjetRequete $uneRequete
     */
    public function addRemoteVar($serverKey, $clef, $uneRequete)
    {
        $this->_listeRemoteVars[$serverKey][$clef] = $uneRequete;
    }
}