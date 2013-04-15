<?php
namespace AlaroxFramework\utils\unparser;

abstract class AbstractUnparser
{
    /**
     * @param string $donnees
     * @return array
     */
    abstract public function toArray($donnees);
}