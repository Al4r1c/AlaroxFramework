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
        $viewName = 'monTpl.twig';

        $view = $this->getMock('\\AlaroxFramework\\utils\\View', array('getViewName', 'getVariables'));
        $twigEnv = $this->getMock('\\Twig_Environment', array('loadTemplate'));
        $twigTemplate = $this->getMockForAbstractClass('\\Twig_TemplateInterface');

        $view->expects($this->once())->method('getViewName')->will($this->returnValue($viewName));
        $view->expects($this->once())->method('getVariables')->will(
            $this->returnValue($tabVars)
        );

        $twigEnv->expects($this->once())->method('loadTemplate')->with($viewName)->will(
            $this->returnValue($twigTemplate)
        );

        $twigTemplate->expects($this->once())->method('render')->with($tabVars + $globalVars + $remoteVars)->will(
            $this->returnValue('My template as string')
        );

        $this->setVars($globalVars, $remoteVars);
        $this->_templateManager->setTwigEnv($twigEnv);

        $this->assertEquals('My template as string', $this->_templateManager->render($view));
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

    public function testSetTwigEnv()
    {
        $this->_templateManager->setTwigEnv($twigEnv = $this->getMock('\\Twig_Environment'));

        $this->assertAttributeSame($twigEnv, '_twigEnv', $this->_templateManager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTwigEnvType()
    {
        $this->_templateManager->setTwigEnv('raye');
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
        $mockTwigEnv = $this->getMock('\Twig_Environment', array('addExtension'));
        $mockTwigExtInterface = $this->getMock('\Twig_ExtensionInterface');

        $mockTwigEnv->expects($this->once())->method('addExtension')->with($mockTwigExtInterface);

        $this->_templateManager->setTwigEnv($mockTwigEnv);
        $this->_templateManager->addExtension($mockTwigExtInterface);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddExtensionType()
    {
        $this->_templateManager->addExtension('letsbug');
    }

    /**
     * @expectedException \Exception
     */
    public function testAddExtensionTwigEnvNotSet()
    {
        $this->_templateManager->addExtension($this->getMock('\Twig_ExtensionInterface'));
    }
}
