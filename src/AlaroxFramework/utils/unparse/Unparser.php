<?php
namespace AlaroxFramework\utils\unparse;

class Unparser
{
    /**
     * @var UnparserFactory
     */
    private $_unparserFactory;

    /**
     * @param UnparserFactory $unparserFactory
     * @throws \InvalidArgumentException
     */
    public function setUnparserFactory($unparserFactory)
    {
        if (!$unparserFactory instanceof UnparserFactory) {
            throw new \InvalidArgumentException('Expected parameter 1 to be UnparserFactory.');
        }

        $this->_unparserFactory = $unparserFactory;
    }

    /**
     * @param string $donnees
     * @param string $format
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return array
     */
    public function toArray($donnees, $format)
    {
        if (!is_string($donnees)) {
            throw new \InvalidArgumentException('Parameter 1 $donnees must be a string.');
        }

        if (!$this->_unparserFactory instanceof UnparserFactory) {
            throw new \Exception('Unparser factory is not set.');
        }

        $classUnparser = $this->_unparserFactory->getClass($format);

        return $classUnparser->toArray($donnees);
    }
}