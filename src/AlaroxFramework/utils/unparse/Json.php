<?php
namespace AlaroxFramework\utils\unparse;

class Json extends AbstractUnparser
{
    /**
     * @param string $donnees
     * @return string
     */
    public function toArray($donnees)
    {
        return json_decode($donnees, true);
    }
}