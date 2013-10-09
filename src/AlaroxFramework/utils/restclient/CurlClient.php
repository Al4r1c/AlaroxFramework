<?php
namespace AlaroxFramework\utils\restclient;

use AlaroxFramework\cfg\rest\RestServer;
use AlaroxFramework\utils\compressor\Compressor;
use AlaroxFramework\utils\ObjetReponse;
use AlaroxFramework\utils\ObjetRequete;
use AlaroxFramework\utils\parser\Parser;
use AlaroxFramework\utils\tools\Tools;

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
     * @var Compressor
     */
    private $_compressor;

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
     * @param Compressor $compressor
     * @throws \InvalidArgumentException
     */
    public function setCompressor($compressor)
    {
        if (!$compressor instanceof Compressor) {
            throw new \InvalidArgumentException('Expected parameter 1 compressor to be instance of Compressor.');
        }

        $this->_compressor = $compressor;
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
     * @param RestServer $restServer
     * @param ObjetRequete $objetRequete
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return ObjetReponse
     */
    public function executer($restServer, $objetRequete)
    {
        if (!$restServer instanceof RestServer) {
            throw new \InvalidArgumentException('Expected parameter 1 restServer to be RestServer.');
        }

        if (!$objetRequete instanceof ObjetRequete) {
            throw new \InvalidArgumentException('Expected parameter 2 objetRequete to be ObjetRequete.');
        }

        foreach (get_object_vars($this) as $clef => $unAttribut) {
            if (empty($unAttribut)) {
                throw new \Exception('Can\'t execute request: ' . $clef . ' is not set.');
            }
        }


        $this->_curl->initialize();

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
                $donneesAEnvoyer = $this->buildPostBody($objetRequete->getBody(), $restServer->getFormatEnvoi());

                $this->_curl->ajouterOption(
                    CURLOPT_POSTFIELDS,
                    $this->compressData($donneesAEnvoyer, $restServer->getCompressor())
                );
                $this->_curl->ajouterOption(CURLOPT_POST, true);
                break;
            case 'PUT':
                $this->_curl->ajouterOption(CURLOPT_RETURNTRANSFER, true);
                $this->_curl->ajouterOption(CURLOPT_CUSTOMREQUEST, 'PUT');
                $this->_curl->ajouterOption(
                    CURLOPT_POSTFIELDS,
                    $this->compressData($donneesAEnvoyer, $restServer->getCompressor())
                );
                break;
            case 'DELETE':
                $this->_curl->ajouterOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                throw new \Exception('Unsupported HTTP verb "' . $methodeHttp . '".');
        }

        if ($restServer->isAuthEnabled() === true) {
            $this->_curl->ajouterUnHeader(
                'Authorization',
                $this->getSignature($restServer, $methodeHttp, $donneesAEnvoyer, strtotime($dateRequeteGmt))
            );
        }

        list($responseCurl, $reponseInfo) =
            $this->curlExec(
                $restServer->getUrl(),
                $restServer->getParametresUri(),
                $uri,
                $restServer->getFormatEnvoi(),
                $dateRequeteGmt
            );

        if (($pos = strpos($contentType = $reponseInfo['content_type'], ';')) !== false) {
            $contentType = substr($reponseInfo['content_type'], 0, $pos);
        }

        return new ObjetReponse($reponseInfo['http_code'], $responseCurl, $contentType);
    }

    private function compressData($data, $compressorType)
    {
        if (!is_null($compressorType)) {
            $this->_curl->ajouterUnHeader('Content-Encoding', $compressorType);

            return $this->_compressor->compress($data, $compressorType);
        } else {
            return $data;
        }
    }

    /**
     * @param string $url
     * @param array $paramUri
     * @param string $uri
     * @param string $format
     * @param string $date
     * @return array
     */
    private function curlExec($url, $paramUri, $uri, $format, $date)
    {
        $formatMime = Tools::getMimePourFormat($format);

        if (count($paramUri) > 0) {
            $donneesUri = $this->buildPostBody($paramUri, 'txt');

            if (strpos('?', $uri) === false) {
                $uri .= '?';
            }

            $uri .= $donneesUri;
        }

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
     * @param RestServer $restServer
     * @param string $methode
     * @param string $donnees
     * @param int $timestamp
     * @return string
     */
    private function getSignature($restServer, $methode, $donnees, $timestamp)
    {
        $auth = $restServer->getAuth();

        $encode =
            base64_encode(
                hash_hmac(
                    'sha256',
                    $donnees,
                    $auth->getPrivateKey() . $methode . Tools::getMimePourFormat($restServer->getFormatEnvoi()) .
                    $timestamp,
                    true
                )
            );

        return $auth->getAuthentifMethode() . ' ' . $auth->getUsername() . ':' . $encode;
    }
}