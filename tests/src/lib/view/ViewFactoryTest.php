<?php
namespace Tests\lib\view;

use AlaroxFramework\utils\view\ViewFactory;

class ViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewFactory
     */
    private $_viewFactory;

    public function setUp()
    {
        $this->_viewFactory = new ViewFactory();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\view\ViewFactory', $this->_viewFactory);
    }

    public function testPlainView()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\view\PlainView', $this->_viewFactory->getView('plain'));
    }

    public function testTemplateView()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\view\TemplateView', $this->_viewFactory->getView('template'));
    }

    /**
     * @expectedException \Exception
     */
    public function testTypeInexistant()
    {
        $this->_viewFactory->getView('never gonna exist');
    }
}
