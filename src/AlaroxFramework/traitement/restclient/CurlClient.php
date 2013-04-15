<?php
namespace AlaroxFramework\traitement\restclient;

use AlaroxFramework\cfg\RestInfos;
use AlaroxFramework\utils\Curl;
use AlaroxFramework\utils\ObjetReponse;
use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\Tools;
use AlaroxFramework\utils\parser\Parser;

class CurlClient
{
    /**
     * @var Curl
     */
    private $_curl;

    /**
     * @var Parser
     */
    private $_parser;

    /**
     * @var int
     */
    private $_time;

    /**
     * @param Curl $curl
     * @throws \InvalidArgumentException
     */
    public function setCurl($curl)
    {
        if (!$curl instanceof Curl) {
            throw new \InvalidArgumentException('Expected parameter 1 curl to be instance of Curl.');
        }

        $this->_curl = $curl;
        $this->_curl->ajouterOption(CURLOPT_TIMEOUT, 6);
        $this->_curl->ajouterOption(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param Parser $parser
     * @throws \InvalidArgumentException
     */
    public function setParser($parser)
    {
        if (!$parser instanceof Parser) {
            throw new \InvalidArgumentException('Expected parameter 1 parser to be instance of Parser.');
        }

        $this->_parser = $parser;
    }

    /**
     * @param $timestamp
     * @throws \InvalidArgumentException
     * @internal param int $time
     */
    public function setTime($timestamp)
    {
        if (!is_int($timestamp)) {
            throw new \InvalidArgumentException('Expected parameter 1 timestamp to be integer.');
        }

        $this->_time = $timestamp;
    }

    /**
     * @param RestInfos $restInfos
     * @param ObjetRequete $objetRequete
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return ObjetReponse
     */
    public function executer($restInfos, $objetRequete)
    {
        if (!$restInfos instanceof RestInfos) {
            throw new \InvalidArgumentException('Expected parameter 1 restInfos to be RestInfos.');
        }

        if (!$objetRequete instanceof ObjetRequete) {
            throw new \InvalidArgumentException('Expected parameter 2 objetRequete to be ObjetRequete.');
        }

        foreach (get_object_vars($this) as $clef => $unAttribut) {
            if (empty($unAttribut)) {
                throw new \Exception('Can\'t execute request: ' . $clef . 'is not set.');
            }
        }

        $donneesAEnvoyer = '';
        $uri = $objetRequete->getUri();
        $dateRequeteGmt = gmdate("D, d M Y H:i:s T", $this->_time);

        switch ($methodeHttp = strtoupper($objetRequete->getMethodeHttp())) {
            case 'GET':
                if (count($objetsAEnvoyer = $objetRequete->getBody()) > 0) {
                    $donneesAEnvoyer = $this->buildPostBody($objetsAEnvoyer, 'txt');

                    $uri .= '?' . $donneesAEnvoyer;
                }
                break;
            case 'POST':
                $donneesAEnvoyer = $this->buildPostBody($objetRequete->getBody(), $restInfos->getFormatEnvoi());

                $this->_curl->ajouterOption(CURLOPT_POSTFIELDS, $donneesAEnvoyer);
                $this->_curl->ajouterOption(CURLOPT_POST, true);
                break;
            case 'PUT':
                $fichierTempEcriture = tmpFile();
                fwrite(
                    $fichierTempEcriture,
                    $donneesAEnvoyer = $this->buildPostBody($objetRequete->getBody(), $restInfos->getFormatEnvoi())
                );
                rewind($fichierTempEcriture);

                $this->_curl->ajouterOption(CURLOPT_INFILE, $fichierTempEcriture);
                $this->_curl->ajouterOption(CURLOPT_INFILESIZE, strlen($donneesAEnvoyer));
                $this->_curl->ajouterOption(CURLOPT_PUT, true);
                break;
            case 'DELETE':
                $this->_curl->ajouterOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                throw new \Exception('Unsupported HTTP verb "' . $methodeHttp . '".');
        }

        if ($restInfos->isAuthEnabled() === true) {
            $this->_curl->ajouterUnHeader(
                'Authorization',
                $this->getSignature($restInfos, $methodeHttp, $donneesAEnvoyer, strtotime($dateRequeteGmt))
            );
        }

        list($responseCurl, $reponseInfo) =
            $this->curlExec($restInfos->getUrl(), $uri, $restInfos->getFormatEnvoi(), $dateRequeteGmt);

        if (($pos = strpos($contentType = $reponseInfo['content_type'], ';')) !== false) {
            $contentType = substr($reponseInfo['content_type'], 0, $pos);
        }

        if (isset($fichierTempEcriture)) {
            fclose($fichierTempEcriture);
        }

        return new ObjetReponse($reponseInfo['http_code'], $responseCurl, $contentType);
    }

    /**
     * @param string $url
     * @param string $uri
     * @param string $format
     * @param string $date
     * @return array
     */
    private function curlExec($url, $uri, $format, $date)
    {
        $formatMime = Tools::getMimePourFormat($format);

        $this->_curl->ajouterOption(CURLOPT_URL, $url . $uri);
        $this->_curl->ajouterHeaders(
            array(
                'Accept' => $formatMime,
                'Content-type' => $formatMime,
                'Date' => $date)
        );

        return $this->_curl->executer();
    }

    /**
     * @param array $tableauDonnees
     * @param string $format
     * @return string
     * @throws \Exception
     */
    private function buildPostBody($tableauDonnees, $format)
    {
        return $this->_parser->parse($tableauDonnees, $format);
    }

    /**
     * @param RestInfos $restInfos
     * @param string $methode
     * @param string $donnees
     * @param int $timestamp
     * @return string
     */
    private function getSignature($restInfos, $methode, $donnees, $timestamp)
    {
        $encode =
            base64_encode(
                hash_hmac(
                    'sha256', $donnees,
                    $restInfos->getPrivateKey() . $methode . Tools::getMimePourFormat($restInfos->getFormatEnvoi()) .
                        $timestamp, true
                )
            );

        return $restInfos->getAuthentifMethode() . ' ' . $restInfos->getUsername() . ':' . $encode;
    }
}