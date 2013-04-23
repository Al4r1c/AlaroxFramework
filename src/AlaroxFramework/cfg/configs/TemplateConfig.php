<?php
namespace AlaroxFramework\cfg\configs;

class TemplateConfig
{
    /**
     * @var boolean
     */
    private $_cache = false;

    /**
     * @var string
     */
    private $_charset;

    /**
     * @var array
     */
    private $_globalVariables;

    /**
     * @var string
     */
    private $_templateDirectory;

    /**
     * @return boolean
     */
    public function isCacheEnabled()
    {
        return $this->_cache;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * @return array
     */
    public function getGlobalVariables()
    {
        return $this->_globalVariables;
    }

    /**
     * @return string
     */
    public function getTemplateDirectory()
    {
        return $this->_templateDirectory;
    }

    /**
     * @param boolean $cache
     * @throws \InvalidArgumentException
     */
    public function setCache($cache)
    {
        if (is_bool($cache) || !is_null($cache = filter_var($cache, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
            $this->_cache = $cache;
        } else {
            throw new \InvalidArgumentException('Expected parameter 1 cache to be boolean.');
        }
    }

    /**
     * @param string $charset
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function setCharset($charset)
    {
        if (!is_string($charset)) {
            throw new \InvalidArgumentException('Expected parameter 1 charset to be string.');
        }

        if (!in_array(strtoupper($charset), array_map('strtoupper', mb_list_encodings()))) {
            throw new \Exception(sprintf('Charset value "%s" invalid.', $charset));
        }

        $this->_charset = $charset;
    }

    /**
     * @param array $globalVariables
     * @throws \InvalidArgumentException
     */
    public function setGlobalVariables($globalVariables)
    {
        if (!is_array($globalVariables)) {
            throw new \InvalidArgumentException('Expected parameter 1 globalVariables to be array.');
        }
        $this->_globalVariables = $globalVariables;
    }

    /**
     * @param string $repertoireTemplates
     */
    public function setTemplateDirectory($repertoireTemplates)
    {
        $this->_templateDirectory = $repertoireTemplates;
    }
}