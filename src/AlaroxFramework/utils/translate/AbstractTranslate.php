<?php
namespace AlaroxFramework\utils\translate;

abstract class AbstractTranslate
{
    /**
     * @param string $donnees
     * @return string
     */
    abstract public function toArray($donnees);
}