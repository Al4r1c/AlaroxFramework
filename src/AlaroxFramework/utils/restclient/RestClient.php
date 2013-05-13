<?php
namespace AlaroxFramework\utils\restclient;

use AlaroxFramework\cfg\rest\RestServerManager;
use AlaroxFramework\utils\ObjetReponse;
use AlaroxFramework\utils\ObjetRequete;

class RestClient
{
    /**
     * @var RestServerManager
     */
    private $_restServerManager;

    /**
     * @var CurlClient
     */
    private $_curlClient;

    /**
     * @param RestServerManager $restServerManager
     * @throws \InvalidArgumentException
     */
    public function setRestServerManager($restServerManager)
    {
        if (!$restServerManager instanceof RestServerManager) {
            throw new \InvalidArgumentException('Expected parameter 1 to be RestServerManager.');
        }

        $this->_restServerManager = $restServerManager;
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
     * @param string $restServerKey
     * @param ObjetRequete $objetRequete
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return ObjetReponse
     */
    public function executerRequete($restServerKey, $objetRequete)
    {
        if (!$objetRequete instanceof ObjetRequete) {
            throw new \InvalidArgumentException('Expected parameter 1 to be ObjetRequete.');
        }

        if (!isset($this->_curlClient)) {
            throw new \Exception('CurlClient is not set.');
        }

        if (!isset($this->_restServerManager)) {
            throw new \Exception('RestServerManager is not set.');
        }

        if (is_null($restServer = $this->_restServerManager->getRestServer($restServerKey))) {
            throw new \Exception(sprintf('RestServer with key "%s" not found.', $restServerKey));
        }

        return $this->_curlClient->executer($restServer, $objetRequete);
    }
}