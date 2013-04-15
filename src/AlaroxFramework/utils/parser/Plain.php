<?php
namespace AlaroxFramework\utils\parser;

class Plain extends AbstractParser
{
    /**
     * @param array $tabDonnees
     * @return string
     */
    public function parse($tabDonnees)
    {
        return http_build_query($tabDonnees);
    }
}