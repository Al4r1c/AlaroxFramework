<?php
namespace AlaroxFramework\utils\parser;

class Parser
{
    /**
     * @var ParserFactory
     */
    private $_parserFactory;

    /**
     * @param ParserFactory $parserFactory
     * @throws \InvalidArgumentException
     */
    public function setParserFactory($parserFactory)
    {
        if (!$parserFactory instanceof ParserFactory) {
            throw new \InvalidArgumentException('Expected parameter 1 to be ParserFactory.');
        }

        $this->_parserFactory = $parserFactory;
    }

    /**
     * @param array $tabDonnees
     * @param string $format
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return array
     */
    public function parse($tabDonnees, $format)
    {
        if (!is_array($tabDonnees)) {
            throw new \InvalidArgumentException('Parameter 1 $donnees must be a string.');
        }

        if (!$this->_parserFactory instanceof ParserFactory) {
            throw new \Exception('Parser factory is not set.');
        }

        $classParser = $this->_parserFactory->getClass($format);

        return $classParser->parse($tabDonnees);
    }
}