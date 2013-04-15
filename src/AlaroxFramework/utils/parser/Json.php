<?php
namespace AlaroxFramework\utils\parser;

class Json extends AbstractParser
{
    /**
     * @param array $tabDonnees
     * @return string
     */
    public function parse($tabDonnees)
    {
        return json_encode($tabDonnees);
    }
}