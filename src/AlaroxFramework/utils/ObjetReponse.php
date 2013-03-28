<?php
namespace AlaroxFramework\Utils;

use AlaroxFramework\Utils\Tools;

class ObjetReponse
{
    /**
     * @var int
     */
    private $_statusHttp;

    /**
     * @var array
     */
    private $_donneesReponse;

    /**
     * @var string
     */
    private $_format;

    public function __construct($statusHttp = 200, $donneesReponse = array())
    {
        $this->setStatusHttp($statusHttp);
        $this->setDonneesReponse($donneesReponse);
    }

    /**
     * @return int
     */
    public function getStatusHttp()
    {
        return $this->_statusHttp;
    }

    /**
     * @return array
     */
    public function getDonneesReponse()
    {
        return $this->_donneesReponse;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * @param int $statusHttp
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function setStatusHttp($statusHttp)
    {
        if (!is_numeric($statusHttp)) {
            throw new \InvalidArgumentException('Expected numeric value for status.');
        }

        if (!Tools::isValideHttpCode($statusHttp)) {
            throw new \Exception(sprintf('Invalid HTTP code %s.', $statusHttp));
        }

        $this->_statusHttp = $statusHttp;
    }

    /**
     * @param array $donneesReponse
     * @throws \InvalidArgumentException
     */
    public function setDonneesReponse($donneesReponse)
    {
        if (!is_array($donneesReponse)) {
            throw new \InvalidArgumentException('Expected array as data.');
        }

        $this->_donneesReponse = $donneesReponse;
    }

    /**
     * @param string $format
     * @throws \Exception
     */
    public function setFormat($format)
    {
        if (!Tools::isValideFormat($format)) {
            throw new \Exception(sprintf('Invalid format "%s".', $format));
        }

        $this->_format = $format;
    }
}