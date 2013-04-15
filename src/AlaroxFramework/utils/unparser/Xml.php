<?php
namespace AlaroxFramework\utils\unparser;

class Xml extends AbstractUnparser
{
    /**
     * @param string $donnees
     * @return array
     */
    public function toArray($donnees)
    {
        $xmlParser = new \XMLParser();
        $xmlParser->setAndParseContent($donnees);

        return $this->xmlDataToArray($xmlParser->getParsedData()->getChildren());
    }

    /**
     * @param \XMLElement[] $tabXmlElements
     * @return array
     */
    private function xmlDataToArray($tabXmlElements)
    {
        $result = array();

        foreach ($tabXmlElements as $unXmlElement) {
            if ($unXmlElement->hasChildren()) {
                $result[$unXmlElement->getUnAttribut('attr')] = $this->xmlDataToArray($unXmlElement->getChildren());
            } else {
                $result[$unXmlElement->getUnAttribut('attr')] = $unXmlElement->getValue();
            }
        }

        return $result;
    }
}