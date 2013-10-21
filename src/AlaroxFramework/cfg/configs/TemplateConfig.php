<?php
namespace AlaroxFramework\cfg\configs;

use AlaroxFramework\cfg\globals\GlobalVars;

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
     * @var GlobalVars
     */
    private $_globalVariables;

    /**
     * @var string
     */
    private $_templateDirectory;

    /**
     * @var array
     */
    private $_twigExtensionsList;

    /**
     * @var \Closure
     */
    private $_notFoundClosure;

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
     * @return string
     */
    public function getTemplateDirectory()
    {
        return $this->_templateDirectory;
    }

    /**
     * @return GlobalVars
     */
    public function getGlobalVariables()
    {
        return $this->_globalVariables;
    }

    /**
     * @return \Closure
     */
    public function getNotFoundClosure()
    {
        return $this->_notFoundClosure;
    }

    /**
     * @return array
     */
    public function getTwigExtensionsList()
    {
        return $this->_twigExtensionsList;
    }

    /**
     * @param boolean $cache
     * @throws \InvalidArgumentException
     */
    public function setCache($cache)
    {
        if (is_null($varBool = getValidBoolean($cache))) {
            throw new \InvalidArgumentException('Expected parameter 1 cache to be boolean.');
        }

        $this->_cache = $varBool;
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
     * @param GlobalVars $globalVariables
     * @throws \InvalidArgumentException
     */
    public function setGlobalVariables($globalVariables)
    {
        if (!$globalVariables instanceof GlobalVars) {
            throw new \InvalidArgumentException('Expected parameter 1 globalVariables to be instance of GlobarVars.');
        }

        $this->_globalVariables = $globalVariables;
    }

    /**
     * @param string $repertoireTemplates
     * @throws \Exception
     */
    public function setTemplateDirectory($repertoireTemplates)
    {
        if (!is_dir($repertoireTemplates)) {
            throw new \Exception(sprintf(
                'Defined template directory "%s" does not exist.',
                realpath($repertoireTemplates)
            ));
        }

        $this->_templateDirectory = $repertoireTemplates;
    }

    /**
     * @param \Closure $errors
     * @throws \InvalidArgumentException
     */
    public function setNotFoundCallable($errors)
    {
        if (!is_callable($errors)) {
            throw new \InvalidArgumentException('Expected parameter 1 errors to be callable.');
        }

        $this->_notFoundClosure = $errors;
    }

    /**
     * @param array $twigExtensionsList
     */
    public function setTwigExtensionsList($twigExtensionsList)
    {
        $this->_twigExtensionsList = $twigExtensionsList;
    }
}