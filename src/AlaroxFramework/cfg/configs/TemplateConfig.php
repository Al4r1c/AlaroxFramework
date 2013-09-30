<?php
namespace AlaroxFramework\cfg\configs;

use AlaroxFramework\cfg\globals\GlobalVars;
use AlaroxFramework\utils\view\PlainView;

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
     * @var PlainView
     */
    private $_notFoundTemplate;

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
     * @return PlainView
     */
    public function getNotFoundTemplate()
    {
        return $this->_notFoundTemplate;
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
     * @param PlainView $errors
     */
    public function setNotFoundTemplate($errors)
    {
        $this->_notFoundTemplate = $errors;
    }
}