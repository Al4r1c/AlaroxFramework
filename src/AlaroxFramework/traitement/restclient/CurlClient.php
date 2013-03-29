<?php
namespace AlaroxFramework\traitement\restclient;

use AlaroxFramework\Utils\ObjetReponse;
use AlaroxFramework\Utils\ObjetRequete;
use AlaroxFramework\Utils\Tools;
use AlaroxFramework\cfg\RestInfos;

class CurlClient
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
     * @param RestInfos $restInfos
     * @return mixed
     */
    private function curlExec($restInfos)
    {
        if (!is_null($restInfos->getUsername()) && !is_null($restInfos->getPassword())) {
            curl_setopt($this->_curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt(
                $this->_curl, CURLOPT_USERPWD, $restInfos->getUsername() . ':' . $restInfos->getPassword()
            );
        }

        if (!is_null($restInfos->getFormatEnvoi())) {
            $formatMime = Tools::getMimePourFormat($restInfos->getFormatEnvoi());
        } else {
            $formatMime = 'text/plain';
        }

        curl_setopt($this->_curl, CURLOPT_URL, $restInfos->getUrl());
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
     * @param RestInfos $restInfos
     * @param ObjetRequete $objetRequete
     * @throws \InvalidArgumentException
     * @return ObjetReponse
     */
    public function executer($restInfos, $objetRequete)
    {
        switch ($methodeHttp = strtoupper($objetRequete->getMethodeHttp())) {
            case 'GET':
                if (count($objetRequete->getBody()) > 0) {
                    $donnees = $this->buildPostBody($objetRequete->getBody());

                    $restInfos->setUrl($restInfos->getUrl() . '?' . $donnees);
                }
                break;
            case 'POST':
                $donnees = $this->buildPostBody($objetRequete->getBody(), $restInfos->getFormatEnvoi());

                curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $donnees);
                curl_setopt($this->_curl, CURLOPT_POST, true);
                break;
            case 'PUT':
                $this->_file = tmpFile();
                fwrite(
                    $this->_file,
                    $donnees = $this->buildPostBody($objetRequete->getBody(), $restInfos->getFormatEnvoi())
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

        $responseCurl = $this->curlExec($restInfos);
        $reponseInfo = curl_getinfo($this->_curl);

        if (($pos = strpos($contentType = $reponseInfo['content_type'], ';')) !== false) {
            $contentType = substr($reponseInfo['content_type'], 0, $pos);
        }

        return
            new ObjetReponse($reponseInfo['http_code'], $responseCurl, $contentType);
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
    private function buildPostBody($tableauDonnees, $format = 'text')
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