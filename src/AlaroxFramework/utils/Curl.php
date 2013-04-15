<?php
namespace AlaroxFramework\utils;

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

    public function __construct()
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

    public function executer()
    {
        $this->ajouterOption(CURLOPT_HTTPHEADER, $this->_headersOptions);

        $resultat = curl_exec($this->_curl);

        return array($resultat, curl_getinfo($this->_curl));
    }

    public function __destruct()
    {
        curl_close($this->_curl);
    }
}