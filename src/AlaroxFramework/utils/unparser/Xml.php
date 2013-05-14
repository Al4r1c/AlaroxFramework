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
                if (count($data = $this->xmlDataToArray($unXmlElement->getChildren())) > 0) {
                    $result[$unXmlElement->getName()][] = $data;
                } else {
                    $result[$unXmlElement->getName()] = array();
                }
            } else {
                if (isset($result[$unXmlElement->getName()])) {
                    if (!is_array($result[$unXmlElement->getName()])) {
                        $result[$unXmlElement->getName()] = array($result[$unXmlElement->getName()]);
                    }

                    $result[$unXmlElement->getName()][] = $unXmlElement->getValue();
                } else {
                    $result[$unXmlElement->getName()] = $unXmlElement->getValue();
                }
            }
        }

        return $result;
    }
}