<?php
namespace AlaroxFramework\utils\unparser;

class UnparserFactory
{
    /**
     * @param $nomClasse
     * @return AbstractUnparser
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
            default:
                throw new \Exception(sprintf('Response format "%s" not supported.', $nomClasse));
                break;
        }
    }
}