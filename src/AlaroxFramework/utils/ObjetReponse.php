<?php
namespace AlaroxFramework\utils;

use AlaroxFramework\utils\Tools;
use AlaroxFramework\utils\translate\AbstractTranslate;

class ObjetReponse
{
    /**
     * @var int
     */
    private $_statusHttp;

    /**
     * @var string
     */
    private $_donneesReponse;

    /**
     * @var string
     */
    private $_formatMime;

    public function __construct($statusHttp = 200, $donneesReponse = '', $format = 'text/plain')
    {
        $this->setStatusHttp($statusHttp);
        $this->setDonneesReponse($donneesReponse);
        $this->setFormatMime($format);
    }

    /**
     * @return int
     */
    public function getStatusHttp()
    {
        return $this->_statusHttp;
    }

    /**
     * @return string
     */
    public function getDonneesReponse()
    {
        return $this->_donneesReponse;
    }

    /**
     * @return string
     */
    public function getFormatMime()
    {
        return $this->_formatMime;
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
     * @param string $donneesReponse
     * @throws \InvalidArgumentException
     */
    public function setDonneesReponse($donneesReponse)
    {
        if (!is_string($donneesReponse)) {
            throw new \InvalidArgumentException('Expected string as data.');
        }

        $this->_donneesReponse = $donneesReponse;
    }

    /**
     * @param string $format
     * @throws \Exception
     */
    public function setFormatMime($format)
    {
        if (!Tools::isValidMime($format)) {
            throw new \Exception(sprintf('Invalid format "%s".', $format));
        }

        $this->_formatMime = $format;
    }

    public function toArray()
    {
        if (class_exists(
            $nomClasseConversion =
                'AlaroxFramework\\utils\\translate\\' . ucfirst(strtolower(Tools::getFormat($this->_formatMime)))
        )
        ) {
            /** @var AbstractTranslate $classeConversion */
            $classeConversion = new $nomClasseConversion();

            return $classeConversion->toArray($this->_donneesReponse);
        } else {
            throw new \Exception(sprintf('Format "%s" not supported.', $this->_formatMime));
        }
    }
}