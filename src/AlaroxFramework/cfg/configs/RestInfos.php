<?php
namespace AlaroxFramework\cfg\configs;

use AlaroxFramework\utils\Tools;

class RestInfos
{
    /**
     * @var string
     */
    private $_url;

    /**
     * @var boolean
     */
    private $_authentifEnabled;

    /**
     * @var string
     */
    private $_authentifMethode;

    /**
     * @var string
     */
    private $_username;

    /**
     * @var string
     */
    private $_privateKey;

    /**
     * @var string
     */
    private $_formatEnvoi;

    /**
     * @return string
     */
    public function getFormatEnvoi()
    {
        return $this->_formatEnvoi;
    }

    /**
     * @return bool
     */
    public function isAuthEnabled()
    {
        return $this->_authentifEnabled;
    }

    /**
     * @return string
     */
    public function getAuthentifMethode()
    {
        return $this->_authentifMethode;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->_privateKey;
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
     * @param boolean $authentifEnabled
     * @throws \InvalidArgumentException
     */
    public function setAuthentifEnabled($authentifEnabled)
    {
        if (is_null($varBool = getValidBoolean($authentifEnabled))) {
            throw new \InvalidArgumentException(sprintf('Expected boolean as Authentification Enabled.'));
        }

        $this->_authentifEnabled = $varBool;
    }

    /**
     * @param string $authentifMethode
     * @throws \InvalidArgumentException
     */
    public function setAuthentifMethode($authentifMethode)
    {
        if (!is_string($authentifMethode)) {
            throw new \InvalidArgumentException('Expected string for authentification method.');
        }

        $this->_authentifMethode = $authentifMethode;
    }

    /**
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function setPrivateKey($password)
    {
        if (!is_string($password)) {
            throw new \InvalidArgumentException('Expected string for password.');
        }

        $this->_privateKey = $password;
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