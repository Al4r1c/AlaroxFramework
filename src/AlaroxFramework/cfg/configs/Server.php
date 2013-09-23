<?php
namespace AlaroxFramework\cfg\configs;

class Server
{
    /**
     * @var array
     */
    private $_serveurVariables;

    private static $minimalVar = array('REQUEST_URI');

    /**
     * @return array
     */
    public function getServeurVariables()
    {
        return $this->_serveurVariables;
    }

    /**
     * @param string $clef
     * @return string|null
     */
    public function getUneVariableServeur($clef)
    {
        if (array_key_exists($clef, $this->_serveurVariables)) {
            return $this->_serveurVariables[$clef];
        } else {
            return null;
        }
    }

    /**
     * @param array $serverVar
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function setServeurVariables($serverVar)
    {
        if (!is_array($serverVar)) {
            throw new \InvalidArgumentException('Expected array.');
        }

        foreach (self::$minimalVar as $uneVarObligatoire) {
            if (!array_key_exists($uneVarObligatoire, $serverVar)) {
                throw new \Exception(sprintf('Missing server variable "%s".', $uneVarObligatoire));
            }
        }

        $this->_serveurVariables = $serverVar;
        $this->_serveurVariables['REQUEST_URI_NODIR'] =
            substr($serverVar['REQUEST_URI'], strlen(pathinfo($serverVar['PHP_SELF'])['dirname']) - 1);
    }
}