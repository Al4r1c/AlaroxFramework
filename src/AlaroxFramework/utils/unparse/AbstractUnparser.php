<?php
namespace AlaroxFramework\utils\unparse;

abstract class AbstractUnparser
{
    /**
     * @param string $donnees
     * @return string
     */
    abstract public function toArray($donnees);
}