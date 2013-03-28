<?php
namespace AlaroxFramework\Controller;

use AlaroxFramework\Utils\ObjetReponse;
use AlaroxFramework\Utils\Tools;

class Curl
{
    /**
     * @var resource
     */
    private $_curl;

    /**
     * @var resource
     */
    private $_file;

    public function __construct()
    {
        $this->_curl = curl_init();
        curl_setopt($this->_curl, CURLOPT_TIMEOUT, 6);
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param RestClient $restClient
     * @return mixed
     */
    private function curlExec($restClient)
    {
        if (!is_null($restClient->getUsername()) && !is_null($restClient->getPassword())) {
            curl_setopt($this->_curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($this->_curl, CURLOPT_USERPWD, $restClient->getUsername() . ':' . $restClient->getPassword());
        }

        if (!is_null($restClient->getFormatEnvoi())) {
            $formatMime = Tools::getMimePourFormat($restClient->getFormatEnvoi());
        } else {
            $formatMime = 'text/plain';
        }

        curl_setopt($this->_curl, CURLOPT_URL, $restClient->getUrl());
        curl_setopt(
            $this->_curl, CURLOPT_HTTPHEADER, array(
                'Accept: ' . $formatMime,
                'Content-type: ' . $formatMime
            )
        );

        return curl_exec($this->_curl);
    }

    public function __destruct()
    {
        if ($this->_file != null) {
            fclose($this->_file);
        }

        curl_close($this->_curl);
    }

    /**
     * @param RestClient $restClient
     * @return ObjetReponse
     * @throws \InvalidArgumentException
     */
    public function executer($restClient)
    {
        $this->verifierValide($restClient);

        switch ($methodeHttp = strtoupper($restClient->getMethodeHttp())) {
            case 'GET':
                if (count($restClient->getBody()) > 0) {
                    $donnees = $this->buildPostBody($restClient->getBody());

                    $restClient->setUrl($restClient->getUrl() . '?' . $donnees);
                }
                break;
            case 'POST':
                $donnees = $this->buildPostBody($restClient->getBody(), $restClient->getFormatEnvoi());

                curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $donnees);
                curl_setopt($this->_curl, CURLOPT_POST, true);
                break;
            case 'PUT':
                $this->_file = tmpFile();
                fwrite(
                    $this->_file, $donnees = $this->buildPostBody($restClient->getBody(), $restClient->getFormatEnvoi())
                );
                rewind($this->_file);

                curl_setopt($this->_curl, CURLOPT_INFILE, $this->_file);
                curl_setopt($this->_curl, CURLOPT_INFILESIZE, strlen($donnees));
                curl_setopt($this->_curl, CURLOPT_PUT, true);
                break;
            case 'DELETE':
                curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                throw new \InvalidArgumentException('Unsupported HTTP method "' . $methodeHttp . '".');
        }

        $responseCurl = $this->curlExec($restClient);
        $reponseInfo = curl_getinfo($this->_curl);

        if (($pos = strpos($contentType = $reponseInfo['content_type'], ';')) !== false) {
            $contentType = substr($reponseInfo['content_type'], 0, $pos);
        }

        return
            new ObjetReponse($reponseInfo['http_code'], $responseCurl, $contentType);
    }

    /**
     * @param RestClient $restClient
     * @throws \Exception
     */
    private function verifierValide($restClient)
    {
        if (is_null($restClient->getUrl())) {
            throw new \Exception('Can\'t execute curl: missing server url.');
        }

        if (is_null($restClient->getMethodeHttp())) {
            throw new \Exception('Can\'t execute curl: missing HTTP verb..');
        }
    }

    /**
     * @param \XMLElement[] $tabXmlElements
     * @return array
     */
    private function dataToAssocArray($tabXmlElements)
    {
        $result = array();

        foreach ($tabXmlElements as $unElement) {
            if ($unElement->hasChildren()) {
                $result[$unElement->getUnAttribut('attr')] = $this->dataToAssocArray($unElement->getChildren());
            } else {
                $result[$unElement->getUnAttribut('attr')] = $unElement->getValue();
            }
        }

        return $result;
    }

    /**
     * @param array $tableauDonnees
     * @param string $format
     * @return string
     * @throws \InvalidArgumentException
     */
    public function buildPostBody($tableauDonnees, $format = 'text')
    {
        if (!is_array($tableauDonnees)) {
            throw new \InvalidArgumentException('Invalid data input for postBody. Array expected.');
        }

        if (is_null($format)) {
            $format = 'text';
        }

        switch ($format) {
            case 'xml':
                $simpleXmlObject = new \SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
                $this->arrayToXml($tableauDonnees, $simpleXmlObject);

                $donneesLinearisees = $simpleXmlObject->asXML();
                break;
            case 'json':
                $donneesLinearisees = json_encode($tableauDonnees, true);
                break;
            case 'txt':
            case 'text':
                $donneesLinearisees = http_build_query($tableauDonnees);
                break;
            default:
                throw new \InvalidArgumentException('Unsupported format "' . $format . '".');
        }

        return $donneesLinearisees;
    }

    /**
     * @param array $contenu
     * @param \SimpleXMLElement $simpleXmlObject
     */
    private function arrayToXml($contenu, &$simpleXmlObject)
    {
        foreach ($contenu as $clef => $value) {
            if (is_array($value)) {
                $subnode = $simpleXmlObject->addChild('element');
                $subnode->addAttribute('attr', $clef);
                $this->arrayToXml($value, $subnode);
            } else {
                $simpleXmlObject->addChild('element', $value)->addAttribute('attr', $clef);
            }
        }
    }
}