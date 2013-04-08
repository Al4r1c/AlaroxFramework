<?php
namespace AlaroxFramework\utils\translate;

class Json extends AbstractTranslate
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