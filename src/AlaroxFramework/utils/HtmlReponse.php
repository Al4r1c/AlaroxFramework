<?php
namespace AlaroxFramework\utils;

class HtmlReponse
{
    /**
     * @var int
     */
    private $_statusHttp;

    /**
     * @var string
     */
    private $_corpsReponse;

    /**
     * @return string
     */
    public function getCorpsReponse()
    {
        return $this->_corpsReponse;
    }

    /**
     * @return int
     */
    public function getStatusHttp()
    {
        return $this->_statusHttp;
    }

    /**
     * @param string $corpsReponse
     */
    public function setCorpsReponse($corpsReponse)
    {
        $this->_corpsReponse = $corpsReponse;
    }

    /**
     * @param int $statusHttp
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function setStatusHttp($statusHttp)
    {
        if (!is_int($statusHttp)) {
            throw new \InvalidArgumentException('Expected parameter 1 statusHttp to be integer.');
        }

        if (!Tools::isValideHttpCode($statusHttp)) {
            throw new \Exception(sprintf('Invalid HTTP code %s.', $statusHttp));
        }

        $this->_statusHttp = $statusHttp;
    }

}