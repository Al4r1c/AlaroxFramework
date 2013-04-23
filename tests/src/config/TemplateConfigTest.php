<?php
namespace Tests\Config;

use AlaroxFramework\cfg\configs\TemplateConfig;

class TemplateConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var TemplateConfig */
    private $_templateConfig;

    public function setUp()
    {
        $this->_templateConfig = new TemplateConfig();
    }

    public function testSetGlobals()
    {
        $globals = array('varone' => 'valeurone');

        $this->_templateConfig->setGlobalVariables($globals);

        $this->assertSame($globals, $this->_templateConfig->getGlobalVariables());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetGlobalsArray()
    {
        $this->_templateConfig->setGlobalVariables('exception');
    }

    public function testSetCache()
    {
        $this->_templateConfig->setCache(true);

        $this->assertTrue($this->_templateConfig->isCacheEnabled());
    }

    public function testSetCacheString()
    {
        $this->_templateConfig->setCache('off');

        $this->assertFalse($this->_templateConfig->isCacheEnabled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCacheBoolean()
    {
        $this->_templateConfig->setCache('bug');
    }

    public function testSetCharset()
    {
        $this->_templateConfig->setCharset('utf-8');

        $this->assertEquals('utf-8', $this->_templateConfig->getCharset());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCharsetString()
    {
        $this->_templateConfig->setCharset(array());
    }

    /**
     * @expectedException \Exception
     */
    public function testSetCharsetInvalid()
    {
        $this->_templateConfig->setCharset('utf-9465324354');
    }

    public function testTemplateDirectory()
    {
        $this->_templateConfig->setTemplateDirectory('/path/templates');

        $this->assertEquals('/path/templates', $this->_templateConfig->getTemplateDirectory());
    }
}
