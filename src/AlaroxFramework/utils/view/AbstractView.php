<?php
namespace AlaroxFramework\utils\view;

use AlaroxFramework\utils\ObjetReponse;

abstract class AbstractView
{
    /**
     * @var string
     */
    protected $_viewData;

    /**
     * @var array
     */
    private $_variables = array();

    /**
     * @return string
     */
    public function getViewData()
    {
        return $this->_viewData;
    }

    /**
     * @param $donnees
     * @return $this
     */
    abstract public function renderView($donnees);

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->_variables;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return AbstractView
     */
    public function with($key, $value)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Variable key is empty.');
        }

        if (is_callable($value) || is_resource($value)) {
            throw new \InvalidArgumentException(sprintf('Invalid value type for key %s.', $key));
        }

        $this->_variables[$key] = $value;

        return $this;
    }

    /**
     * @param array $mapVar
     * @throws \InvalidArgumentException
     * @return AbstractView
     */
    public function withMap($mapVar)
    {
        if (!is_array($mapVar)) {
            throw new \InvalidArgumentException('Expected parameter 1 mapVar to be array.');
        }

        foreach ($mapVar as $uneClef => $uneVariable) {
            $this->with($uneClef, $uneVariable);
        }

        return $this;
    }

    /**
     * @param ObjetReponse $objetReponse
     * @param string $clef
     * @throws \InvalidArgumentException
     * @return AbstractView
     */
    public function withResponseObject($objetReponse, $clef = 'responseObject')
    {
        if (!$objetReponse instanceof ObjetReponse) {
            throw new \InvalidArgumentException('Expected parameter 1 objetReponse to be instance of ObjetReponse.');
        }

        $this->with($clef, $objetReponse->toArray());

        return $this;
    }
}