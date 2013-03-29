<?php
namespace AlaroxFramework\cfg;

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
    private $_username;

    /**
     * @var string
     */
    private $_password;

    /**
     * @var string
     */
    private $_formatEnvoi;

    /**
     * @var array
     */
    private static $valeursMinimales = array('Url', 'Format', 'Username', 'PassKey');

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

    /**
     * @param array $tabRestInfos
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function parseRestInfos($tabRestInfos)
    {
        if (!is_array($tabRestInfos)) {
            throw new \InvalidArgumentException('Expected parameter 1 tabRestInfos to be array.');
        }

        foreach (self::$valeursMinimales as $uneValeurMinimale) {
            if (!array_key_exists($uneValeurMinimale, $tabRestInfos)) {
                throw new \Exception(sprintf('Missing config key "%s".', $uneValeurMinimale));
            }
        }

        $this->setUrl($tabRestInfos['Url']);
        $this->setFormatEnvoi($tabRestInfos['Format']);
        $this->setUsername($tabRestInfos['Username']);
        $this->setPassword($tabRestInfos['PassKey']);
    }
}