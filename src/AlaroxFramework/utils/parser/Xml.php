<?php
namespace AlaroxFramework\utils\parser;

class Xml extends AbstractParser
{
    /**
     * @param array $tabDonnees
     * @return string
     */
    public function parse($tabDonnees)
    {
        $simpleXmlObject = new \SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
        $this->arrayToXml($tabDonnees, $simpleXmlObject);

        return $simpleXmlObject->asXML();
    }

    /**
     * @param array $contenu
     * @param \SimpleXMLElement $simpleXmlObject
     */
    private function arrayToXml($contenu, &$simpleXmlObject)
    {
        foreach ($contenu as $clef => $value) {
            if (is_array($value)) {
                $subnode = $simpleXmlObject->addChild($clef);
                $this->arrayToXml($value, $subnode);
            } else {
                $simpleXmlObject->addChild($clef, $value);
            }
        }
    }
}