<?php
namespace Tests\Config;

use AlaroxFramework\cfg\configs\TemplateConfig;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class TemplateConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var TemplateConfig */
    private $_templateConfig;

    public function setUp()
    {
        $this->_templateConfig = new TemplateConfig();
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
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('basePath'));
        mkdir(vfsStream::url('basePath') . '/templateFolder');

        $this->_templateConfig->setTemplateDirectory(vfsStream::url('basePath/templateFolder'));

        $this->assertEquals(vfsStream::url('basePath/templateFolder'), $this->_templateConfig->getTemplateDirectory());
    }

    /**
     * @expectedException \Exception
     */
    public function testTemplateDirectoryDoesNotExist()
    {
        $this->_templateConfig->setTemplateDirectory('/path/to/fake');
    }

    public function testGlobalVar()
    {
        $this->_templateConfig->setGlobalVariables(
            $globalVar = $this->getMock('\\AlaroxFramework\\cfg\\globals\\GlobalVars')
        );

        $this->assertSame($globalVar, $this->_templateConfig->getGlobalVariables());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGlobalVarType()
    {
        $this->_templateConfig->setGlobalVariables('exception');
    }

    public function testNotFoundCallable()
    {
        $errorClosure = function () {
        };

        $this->_templateConfig->setNotFoundCallable($errorClosure);

        $this->assertSame($errorClosure, $this->_templateConfig->getNotFoundClosure());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotFoundCallableTypeCallable()
    {
        $this->_templateConfig->setNotFoundCallable('exception');
    }

    public function testTwigExtensionList()
    {
        $extList = array();

        $this->_templateConfig->setTwigExtensionsList($extList);

        $this->assertSame($extList, $this->_templateConfig->getTwigExtensionsList());
    }
}
