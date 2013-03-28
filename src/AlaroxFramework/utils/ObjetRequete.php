<?php
namespace AlaroxFramework\Utils;

use AlaroxFramework\cfg\RestInfos;

class ObjetRequete
{
    /**
     * @var string
     */
    private $_uri;

    /**
     * @var string
     */
    private $_methodeHttp;

    /**
     * @var array
     */
    private $_body;

    /**
     * @var array
     */
    private static $_methodesHttpAutorisees = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * @return string
     */
    public function getMethodeHttp()
    {
        return $this->_methodeHttp;
    }

    /**
     * @param array $body
     * @throws \InvalidArgumentException
     */
    public function setBody($body)
    {
        if (!is_array($body)) {
            throw new \InvalidArgumentException('Expected array for body data.');
        }

        $this->_body = $body;
    }

    /**
     * @param string $uri
     * @throws \InvalidArgumentException
     */
    public function setUri($uri)
    {
        if (!is_string($uri)) {
            throw new \InvalidArgumentException('Expected string for uri.');
        }

        $this->_uri = $uri;
    }

    /**
     * @param string $methodeHttp
     * @throws \InvalidArgumentException
     */
    public function setMethodeHttp($methodeHttp)
    {
        if (!in_array($methodeHttp, self::$_methodesHttpAutorisees)) {
            throw new \InvalidArgumentException(sprintf('Invalid HTTP method %s', $methodeHttp));
        }

        $this->_methodeHttp = $methodeHttp;
    }
}