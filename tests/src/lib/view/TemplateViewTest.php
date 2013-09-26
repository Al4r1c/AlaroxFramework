<?php
namespace Tests\lib\view;

use AlaroxFramework\utils\view\TemplateView;

class TemplateViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplateView
     */
    private $_view;

    public function setUp()
    {
        $this->_view = new TemplateView();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\view\TemplateView', $this->_view);
    }

    public function testSetViewName()
    {
        $this->_view->renderView('index.twig');

        $this->assertEquals('index.twig', $this->_view->getViewData());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetViewNameExtensionTwig()
    {
        $this->_view->renderView('index.nope');
    }
}
