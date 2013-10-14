<?php
namespace AlaroxFramework\utils\restclient;

class Curl
{
    /**
     * @var resource
     */
    private $_curl;

    /**
     * @var array
     */
    private $_headersOptions = array();

    /**
     * @return resource
     */
    public function getCurl()
    {
        return $this->_curl;
    }

    public function initialize()
    {
        $this->_curl = curl_init();

        curl_setopt($this->_curl, CURLOPT_TIMEOUT, 6);
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param int $option
     * @param mixed $value
     */
    public function ajouterOption($option, $value)
    {
        curl_setopt($this->_curl, $option, $value);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function ajouterUnHeader($name, $value)
    {
        $this->_headersOptions[$name] = $name . ': ' . $value;
    }

    /**
     * @param array $tabHeaders
     */
    public function ajouterHeaders($tabHeaders)
    {
        foreach ($tabHeaders as $unHeader => $uneValeur) {
            $this->ajouterUnHeader($unHeader, $uneValeur);
        }
    }

    public function prepare()
    {
        $this->ajouterUnHeader('Expect', ' ');

        $this->ajouterOption(CURLOPT_HTTPHEADER, $this->_headersOptions);
    }

    public function __destruct()
    {
        if (is_resource($this->_curl)) {
            curl_close($this->_curl);
        }
    }
}