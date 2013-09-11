<?php
namespace AlaroxFramework\utils;

class HtmlReponse
{
    /**
     * @var int
     */
    private $_statusHttp;

    /**
     * @var string|\Exception
     */
    private $_reponse;

    /**
     * @var bool
     */
    private $_isException;

    /**
     * @param int $codeHttp
     * @param string|\Exception $corps
     * @param bool $isException
     */
    public function __construct($codeHttp = 200, $corps = '', $isException = false)
    {
        $this->setStatusHttp($codeHttp);
        $this->setReponse($corps);
        $this->setIsException($isException);
    }

    /**
     * @return string|\Exception
     */
    public function getReponse()
    {
        return $this->_reponse;
    }

    /**
     * @return int
     */
    public function getStatusHttp()
    {
        return $this->_statusHttp;
    }

    /**
     * @return boolean
     */
    public function isException()
    {
        return $this->_isException;
    }

    /**
     * @param string|\Exception $reponse
     */
    public function setReponse($reponse)
    {
        $this->_reponse = $reponse;
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

    /**
     * @param boolean $isException
     */
    public function setIsException($isException)
    {
        $this->_isException = $isException;
    }
}