<?php
namespace AlaroxFramework\cfg\rest;

use AlaroxFramework\utils\compressor\AbstractCompressor;
use AlaroxFramework\utils\Tools;

class RestServer
{
    /**
     * @var string
     */
    private $_url;

    /**
     * @var Auth
     */
    private $_auth;

    /**
     * @var string
     */
    private $_formatEnvoi;

    /**
     * @var array
     */
    private $_parametresUri = array();

    /**
     * @var string
     */
    private $_compressor;

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
        return !is_null($this->_auth);
    }

    /**
     * @return Auth
     */
    public function getAuth()
    {
        return $this->_auth;
    }

    /**
     * @return array
     */
    public function getParametresUri()
    {
        return $this->_parametresUri;
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
    public function getCompressor()
    {
        return $this->_compressor;
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
     * @param Auth $auth
     * @throws \InvalidArgumentException
     */
    public function setAuth($auth)
    {
        if (!$auth instanceof Auth) {
            throw new \InvalidArgumentException('Expected parameter 1 auth to be instance of Auth.');
        }

        $this->_auth = $auth;
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
     * @param string $compressor
     */
    public function setCompressor($compressor)
    {
        $this->_compressor = $compressor;
    }

    /**
     * @param string $clef
     * @param string $parametre
     */
    public function addParametreUri($clef, $parametre)
    {
        $this->_parametresUri[$clef] = $parametre;
    }
}