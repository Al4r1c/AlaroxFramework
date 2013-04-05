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
     * @throws \Exception
     */
    public function setController($controller)
    {
        $this->_controller = strtolower($controller);
    }

    /**
     * @param string $defaultAction
     * @throws \Exception
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
        if (!startsWith($pattern = preg_replace('#(\/)\1+#', '$1', $pattern), '/')) {
            $pattern = '/' . $pattern;
        }

        $this->_pattern = $pattern;
    }

    /**
     * @param string $uri
     * @throws \Exception
     */
    public function setUri($uri)
    {
        if (!startsWith($uri = rtrim(preg_replace('#(\/)\1+#', '$1', $uri), '/'), '/')) {
            $uri = '/' . $uri;
        }

        $this->_uri = strtolower($uri);
    }
}