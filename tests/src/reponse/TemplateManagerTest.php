<?php
namespace Tests\reponse;

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

    public function testRender()
    {
        $viewName = 'monTpl.twig';
        $tabVar = array('id' => array('attr' => 'valeur'));

        $view = $this->getMock('\\AlaroxFramework\\utils\\View', array('getViewName', 'getVariables'));
        $twigEnv = $this->getMock('\\Twig_Environment', array('loadTemplate'));
        $twigTemplate = $this->getMockForAbstractClass('\\Twig_TemplateInterface');

        $view->expects($this->once())->method('getViewName')->will($this->returnValue($viewName));
        $view->expects($this->once())->method('getVariables')->will(
            $this->returnValue($tabVar)
        );

        $twigEnv->expects($this->once())->method('loadTemplate')->with($viewName)->will(
            $this->returnValue($twigTemplate)
        );

        $twigTemplate->expects($this->once())->method('render')->with($tabVar)->will(
            $this->returnValue('My template as string')
        );

        $this->_templateManager->setTwigEnv($twigEnv);
        $this->assertEquals('My template as string', $this->_templateManager->render($view));
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
}
