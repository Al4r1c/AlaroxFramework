<?php
namespace AlaroxFramework\utils\restclient;

use AlaroxFramework\cfg\rest\RestServer;
use AlaroxFramework\utils\ObjetReponse;
use AlaroxFramework\utils\ObjetRequete;

class RestClient
{
    /**
     * @var RestServer
     */
    private $_restServer;

    /**
     * @var CurlClient
     */
    private $_curlClient;

    /**
     * @param RestServer $restServer
     * @throws \InvalidArgumentException
     */
    public function setRestServer($restServer)
    {
        if (!$restServer instanceof RestServer) {
            throw new \InvalidArgumentException('Expected parameter 1 to be RestServer.');
        }

        $this->_restServer = $restServer;
    }

    /**
     * @param CurlClient $curlClient
     * @throws \InvalidArgumentException
     */
    public function setCurlClient($curlClient)
    {
        if (!$curlClient instanceof CurlClient) {
            throw new \InvalidArgumentException('Expected parameter 1 to be CurlClient.');
        }

        $this->_curlClient = $curlClient;
    }

    /**
     * @param ObjetRequete $objetRequete
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return ObjetReponse
     */
    public function executerRequete($objetRequete)
    {
        if (!$objetRequete instanceof ObjetRequete) {
            throw new \InvalidArgumentException('Expected parameter 1 to be ObjetRequete.');
        }

        if (!isset($this->_curlClient)) {
            throw new \Exception('CurlClient is not set.');
        }

        if (!isset($this->_restServer)) {
            throw new \Exception('ResInfos is not set.');
        }

        return $this->_curlClient->executer($this->_restServer, $objetRequete);
    }
}