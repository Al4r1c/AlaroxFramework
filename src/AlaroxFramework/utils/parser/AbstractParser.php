<?php
namespace AlaroxFramework\utils\parser;

abstract class AbstractParser
{
    /**
     * @param array $tabDonnees
     * @return string
     */
    abstract public function parse($tabDonnees);
}