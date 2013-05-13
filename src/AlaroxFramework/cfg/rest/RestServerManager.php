<?php
namespace AlaroxFramework\cfg\rest;

class RestServerManager
{
    /**
     * @var RestServer[]
     */
    private $_restServers = array();

    /**
     * @param string $clef
     * @param RestServer $unRestServer
     * @throws \InvalidArgumentException
     */
    public function addRestServer($clef, $unRestServer)
    {
        if (!$unRestServer instanceof RestServer) {
            throw new \InvalidArgumentException('Expected parameter 1 unRestServer to be instance of RestServer.');
        }

        $this->_restServers[$clef] = $unRestServer;
    }

    /**
     * @param string $clef
     * @return RestServer
     */
    public function getRestServer($clef)
    {
        if (array_key_exists($clef, $this->_restServers)) {
            return $this->_restServers[$clef];
        } else {
            return null;
        }
    }
}