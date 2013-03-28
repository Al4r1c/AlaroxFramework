<?php
namespace AlaroxFramework\Controller;

use AlaroxFramework\Utils\Tools;

class RestInfos
{
    /**
     * @var string
     */
    private $_url;

    /**
     * @var string
     */
    private $_methodeHttp;

    /**
     * @var array
     */
    private $_body;

    /**
     * @var string
     */
    private $_username;

    /**
     * @var string
     */
    private $_password;

    /**
     * @var string
     */
    private $_formatEnvoi;

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
    public function getFormatEnvoi()
    {
        return $this->_formatEnvoi;
    }

    /**
     * @return string
     */
    public function getMethodeHttp()
    {
        return $this->_methodeHttp;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
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
     * @param string $formatEnvoi
     * @throws \InvalidArgumentException
     */
    public function setFormatEnvoi($formatEnvoi)
    {
        if (!Tools::isValideFormat($formatEnvoi)) {
            throw new \InvalidArgumentException(sprintf('Invalid format "%s".', $formatEnvoi));
        }

        $this->_formatEnvoi = $formatEnvoi;
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

    /**
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function setPassword($password)
    {
        if (!is_string($password)) {
            throw new \InvalidArgumentException('Expected string for password.');
        }

        $this->_password = $password;
    }

    /**
     * @param string $url
     * @throws \InvalidArgumentException
     */
    public function setUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException(sprintf('Invalid rest server url "%s".', $url));
        }

        $this->_url = $url;
    }

    /**
     * @param string $username
     * @throws \InvalidArgumentException
     */
    public function setUsername($username)
    {
        if (!is_string($username)) {
            throw new \InvalidArgumentException('Expected string for username.');
        }

        $this->_username = $username;
    }
}