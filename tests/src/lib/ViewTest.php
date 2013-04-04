<?php
namespace Tests\lib;

use AlaroxFramework\utils\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var View
     */
    private $_view;

    public function setUp()
    {
        $this->_view = new View();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\View', $this->_view);
    }

    public function testSetViewName()
    {
        $this->_view->renderView('index.twig');

        $this->assertEquals('index.twig', $this->_view->getViewName());
    }

    /**
     * @expectedException \Exception
     */
    public function testSetViewNameExtensionTwig()
    {
        $this->_view->renderView('index.nope');
    }

    public function testWithKey()
    {
        $this->_view->with('mykey', 'myvalue');

        $this->assertArrayHasKey('mykey', $this->_view->getVariables());
        $this->assertContains('myvalue', $this->_view->getVariables());
    }

    /**
     * @expectedException \Exception
     */
    public function testWithKeyNull()
    {
        $this->_view->with(null, 'myvalue');
    }

    /**
     * @expectedException \Exception
     */
    public function testWithValueInterdite()
    {
        $this->_view->with(
            'key', function () {
            }
        );
    }

    public function testWithMap()
    {
        $this->_view->with('newkey', 'myvalue');
        $this->_view->withMap(array('key1' => 'var1', 'key2' => 'var2'));

        $this->assertCount(3, $this->_view->getVariables());
        $this->assertArrayHasKey('key1', $this->_view->getVariables());
        $this->assertContains('var2', $this->_view->getVariables());
    }

    /**
     * @expectedException \Exception
     */
    public function testWithMapArray()
    {
        $this->_view->withMap('key');
    }
}
