<?php
namespace AlaroxFramework\utils\unparser;

class Json extends AbstractUnparser
{
    /**
     * @param string $donnees
     * @return array
     */
    public function toArray($donnees)
    {
        return json_decode($donnees, true);
    }
}