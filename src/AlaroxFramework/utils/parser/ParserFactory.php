<?php
namespace AlaroxFramework\utils\parser;

class ParserFactory
{
    /**
     * @param $nomClasse
     * @return AbstractParser
     * @throws \Exception
     */
    public function getClass($nomClasse)
    {
        switch ($nomClasse) {
            case 'json':
                return new Json();
                break;
            case 'xml':
                return new Xml();
                break;
            case 'txt':
            case 'text':
            case 'plain':
                return new Plain();
                break;
            default:
                throw new \Exception(sprintf('Format "%s" not supported for parsing.', $nomClasse));
                break;
        }
    }
}