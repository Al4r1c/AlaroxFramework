<?php
namespace Tests\reponse;

use AlaroxFramework\AlaroxFramework;
use AlaroxFramework\reponse\TemplateManager;

class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateManager
     */
    private $_templateManager;

    public function setUp()
    {
        $this->_templateManager = new TemplateManager();
    }

    public function goTestRender($tabVars = array(), $globalVars = array(), $remoteVars = array())
    {
        $content = 'Some Plain Content';

        $view = $this->getMock('\\AlaroxFramework\\utils\\view\\PlainView', array('getViewData', 'getVariables'));
        $twigEnvFactory = $this->getMock('\\AlaroxFramework\\utils\\twig\\TwigEnvFactory', array('getTwigEnv'));
        $twigEnv = $this->getMock('\\Twig_Environment', array('render'));

        $view->expects($this->once())->method('getViewData')->will($this->returnValue($content));
        $view->expects($this->once())->method('getVariables')->will(
            $this->returnValue($tabVars)
        );

        $twigEnvFactory->expects($this->once())->method('getTwigEnv')->with(substr(get_class($view), strrpos(get_class($view), '\\') + 1))->will(
            $this->returnValue($twigEnv)
        );

        $twigEnv->expects($this->once())->method('render')->with($content, $tabVars + $globalVars + $remoteVars)->will(
            $this->returnValue($content)
        );

        $this->setVars($globalVars, $remoteVars);
        $this->_templateManager->setTwigEnvFactory($twigEnvFactory);

        $this->assertEquals($content, $this->_templateManager->render($view));
    }

    /**
     * @param array $staticVars
     * @param array $remoteVars
     */
    public function setVars($staticVars, $remoteVars)
    {
        $globalVar =
            $this->getMock(
                '\\AlaroxFramework\\cfg\\configs\\GlobalVars',
                array('getStaticVars', 'getRemoteVarsExecutees')
            );

        $globalVar->expects($this->once())
        ->method('getStaticVars')
        ->will($this->returnValue($staticVars));

        $globalVar->expects($this->once())
        ->method('getRemoteVarsExecutees')
        ->will($this->returnValue($remoteVars));

        $this->_templateManager->setGlobalVar($globalVar);
    }


    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\reponse\\TemplateManager', $this->_templateManager);
    }

    public function testSetTwigEnvFactory()
    {
        $this->_templateManager->setTwigEnvFactory(
            $twigEnv = $this->getMock('\\AlaroxFramework\\utils\\twig\\TwigEnvFactory')
        );

        $this->assertAttributeSame($twigEnv, '_twigEnvFactory', $this->_templateManager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTwigEnvType()
    {
        $this->_templateManager->setTwigEnvFactory('raye');
    }

    public function testGlobalVar()
    {
        $this->_templateManager->setGlobalVar(
            $globalVar = $this->getMock('\\AlaroxFramework\\cfg\\globals\\GlobalVars')
        );

        $this->assertAttributeSame($globalVar, '_globalVar', $this->_templateManager);
    }

    public function testRenderAvecGlobalVar()
    {
        $tabVar = array('id' => array('attr' => 'valeur'));
        $globalVar = array('title' => 'mon titre');
        $remoteVars = array('title' => 'mon titre effacé');

        $this->goTestRender();
        $this->goTestRender($tabVar, $globalVar, $remoteVars);
    }

    public function testRenderAvecExtension()
    {
        $content = 'Some Plain Content';

        $view = $this->getMock('\\AlaroxFramework\\utils\\view\\PlainView', array('getViewData', 'getVariables'));
        $twigEnvFactory = $this->getMock('\\AlaroxFramework\\utils\\twig\\TwigEnvFactory', array('getTwigEnv'));
        $twigEnv = $this->getMock('\\Twig_Environment', array('render', 'addExtension'));
        $mockTwigExtInterface = $this->getMock('\Twig_ExtensionInterface');

        $view->expects($this->once())->method('getViewData')->will($this->returnValue($content));
        $view->expects($this->once())->method('getVariables')->will(
            $this->returnValue(array())
        );

        $twigEnvFactory->expects($this->once())->method('getTwigEnv')->with(substr(get_class($view), strrpos(get_class($view), '\\') + 1))->will(
            $this->returnValue($twigEnv)
        );

        $twigEnv->expects($this->once())->method('render')->with($content, array())->will(
            $this->returnValue($content)
        );

        $twigEnv->expects($this->once())->method('addExtension')->with($mockTwigExtInterface);

        $this->setVars(array(), array());
        $this->_templateManager->setTwigEnvFactory($twigEnvFactory);
        $this->_templateManager->addExtension($mockTwigExtInterface);

        $this->assertEquals($content, $this->_templateManager->render($view));
    }

    /**
     * @expectedException \Exception
     */
    public function testRenderTwigEnvNotSet()
    {
        $this->_templateManager->render($this->getMock('\\AlaroxFramework\\utils\\View'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderNotView()
    {
        $this->_templateManager->render('exception');
    }

    public function testAddExtension()
    {
        $mockTwigExtInterface = $this->getMock('\Twig_ExtensionInterface');

        $this->assertAttributeInternalType('array', '_listeExtension', $this->_templateManager);
        $this->assertAttributeCount(0, '_listeExtension', $this->_templateManager);

        $this->_templateManager->addExtension($mockTwigExtInterface);

        $this->assertAttributeCount(1, '_listeExtension', $this->_templateManager);
        $this->assertAttributeContains($mockTwigExtInterface, '_listeExtension', $this->_templateManager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddExtensionType()
    {
        $this->_templateManager->addExtension('letsbug');
    }
}
