<?php
namespace Tests\lib;

use AlaroxFramework\utils\twig\TwigEnvFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class TwigEnvFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigEnvFactory
     */
    private $_twigEnvFactory;

    public function setUp()
    {
        $this->_twigEnvFactory = new TwigEnvFactory();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\twig\TwigEnvFactory', $this->_twigEnvFactory);
    }

    public function testTemplateConfig()
    {
        $templateConfig = $this->getMock('\AlaroxFramework\utils\twig\TwigEnvFactory');

        $this->_twigEnvFactory->setTemplateConfig($templateConfig);

        $this->assertAttributeSame($templateConfig, '_templateConfig', $this->_twigEnvFactory);
    }

    public function testPlainView()
    {
        $this->assertInstanceOf('\Twig_Environment', $this->_twigEnvFactory->getTwigEnv('PlainView'));
    }

    public function testTemplateView()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('basePath'));
        mkdir(vfsStream::url('basePath') . '/someFolder');

        $templateConfig =
            $this->getMock(
                '\AlaroxFramework\utils\twig\TwigEnvFactory',
                array('getCharset', 'isCacheEnabled', 'getTemplateDirectory')
            );

        $templateConfig
        ->expects($this->once())
        ->method('getTemplateDirectory')
        ->will($this->returnValue(vfsStream::url('basePath/someFolder')));

        $templateConfig->expects($this->once())->method('getCharset');

        $templateConfig->expects($this->once())->method('isCacheEnabled')->will($this->returnValue(true));

        $this->_twigEnvFactory->setTemplateConfig($templateConfig);

        $this->assertInstanceOf('\Twig_Environment', $this->_twigEnvFactory->getTwigEnv('TemplateView'));
    }

    /**
     * @expectedException \Exception
     */
    public function testUnknownView()
    {
        $this->_twigEnvFactory->getTwigEnv('go bug');
    }
}
