<?php
namespace AlaroxFramework\utils;

class View
{
    /**
     * @var string
     */
    private $_viewName;

    /**
     * @var array
     */
    private $_variables = array();

    /**
     * @return string
     */
    public function getViewName()
    {
        return $this->_viewName;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->_variables;
    }

    /**
     * @param string $viewName
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function renderView($viewName)
    {
        $pathInfo = pathinfo($viewName);

        if (!isset($pathInfo['extension']) || strcmp($pathInfo['extension'], 'twig') != 0) {
            throw new \InvalidArgumentException('Expected twig template.');
        }

        $this->_viewName = $viewName;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @return $this
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
     * @return $this
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
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function withResponseObject($objetReponse)
    {
        if (!$objetReponse instanceof ObjetReponse) {
            throw new \InvalidArgumentException('Expected parameter 1 objetReponse to be instance of ObjetReponse.');
        }

        $this->with('responseObject', $objetReponse->toArray());

        return $this;
    }
}