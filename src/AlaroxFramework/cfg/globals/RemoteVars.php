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

        foreach ($this->_listeRemoteVars as $clef => $uneAction) {
            $tabResult[$clef] = $this->_restClient->executerRequete($uneAction)->toArray();
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
     * @param string $clef
     * @param ObjetRequete $uneRequete
     */
    public function addRemoteVar($clef, $uneRequete)
    {
        $this->_listeRemoteVars[$clef] = $uneRequete;
    }
}