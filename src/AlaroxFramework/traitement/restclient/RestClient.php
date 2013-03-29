<?php
namespace AlaroxFramework\traitement\restclient;

use AlaroxFramework\Utils\ObjetReponse;
use AlaroxFramework\Utils\ObjetRequete;
use AlaroxFramework\cfg\RestInfos;

class RestClient
{
    /**
     * @var RestInfos
     */
    private $_restInfos;

    /**
     * @var CurlClient
     */
    private $_curlClient;

    /**
     * @param RestInfos $restInfos
     * @throws \InvalidArgumentException
     */
    public function setRestInfos($restInfos)
    {
        if (!$restInfos instanceof RestInfos) {
            throw new \InvalidArgumentException('Expected parameter 1 to be RestInfos.');
        }

        $this->_restInfos = $restInfos;
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

        if (!isset($this->_restInfos)) {
            throw new \Exception('ResInfos is not set.');
        }

        return $this->_curlClient->executer($this->_restInfos, $objetRequete);
    }
}