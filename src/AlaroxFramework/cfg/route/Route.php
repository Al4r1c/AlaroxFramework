<?php
namespace AlaroxFramework\cfg\route;

class Route
{
    /**
     * @var string
     */
    private $_uri;

    /**
     * @var string
     */
    private $_controller;

    /**
     * @var string
     */
    private $_pattern;

    /**
     * @var string
     */
    private $_defaultAction;

    /**
     * @var array
     */
    private $_mapping;

    /**
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->_mapping;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    /**
     * @param string $defaultAction
     */
    public function setDefaultAction($defaultAction)
    {
        $this->_defaultAction = $defaultAction;
    }

    /**
     * @param array $mapping
     * @throws \InvalidArgumentException
     */
    public function setMapping($mapping)
    {
        if (!is_array($mapping)) {
            throw new \InvalidArgumentException('Expected array for parameter 1 mapping.');
        }

        $this->_mapping = $mapping;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        if (!startsWith($pattern, '/')) {
            $pattern = '/' . $pattern;
        }

        $this->_pattern = $pattern;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        if (!startsWith($uri, '/')) {
            $uri = '/' . $uri;
        }

        $this->_uri = $uri;
    }
}