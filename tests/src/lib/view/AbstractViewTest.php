<?php
namespace Tests\lib\view;

use AlaroxFramework\utils\view\AbstractView;

class AbstractViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractView
     */
    private $_abstractView;

    public function setUp()
    {
        $this->_abstractView = $this->getMockForAbstractClass('\\AlaroxFramework\\utils\\view\\AbstractView');
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\view\\AbstractView', $this->_abstractView);
    }


    public function testWithKey()
    {
        $this->_abstractView->with('mykey', 'myvalue');

        $this->assertArrayHasKey('mykey', $this->_abstractView->getVariables());
        $this->assertContains('myvalue', $this->_abstractView->getVariables());
    }

    /**
     * @expectedException \Exception
     */
    public function testWithKeyNull()
    {
        $this->_abstractView->with(null, 'myvalue');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithValueInterdite()
    {
        $this->_abstractView->with(
            'key',
            function () {
            }
        );
    }

    public function testWithMap()
    {
        $this->_abstractView->with('newkey', 'myvalue');
        $this->_abstractView->withMap(array('key1' => 'var1', 'key2' => 'var2'));

        $this->assertCount(3, $this->_abstractView->getVariables());
        $this->assertArrayHasKey('key1', $this->_abstractView->getVariables());
        $this->assertContains('var2', $this->_abstractView->getVariables());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithMapArray()
    {
        $this->_abstractView->withMap('key');
    }

    public function testWithObjetReponse()
    {
        $objetReponse = $this->getMock('\\AlaroxFramework\\utils\\ObjetReponse', array('toArray'));
        $objetReponse
        ->expects($this->once())
        ->method('toArray')
        ->will($this->returnValue(array('idObj' => array('attribute' => 'valeur'))));

        $this->_abstractView->withResponseObject($objetReponse);

        $this->assertCount(1, $this->_abstractView->getVariables());
        $this->assertArrayHasKey('responseObject', $this->_abstractView->getVariables());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testWithObjetReponseType()
    {
        $this->_abstractView->withResponseObject(array());
    }
}
