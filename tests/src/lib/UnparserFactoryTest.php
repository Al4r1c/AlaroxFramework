<?php
namespace Tests\lib;

use AlaroxFramework\utils\unparse\UnparserFactory;

class UnparserFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnparserFactory
     */
    private $_unparserFactory;

    public function setUp()
    {
        $this->_unparserFactory = new UnparserFactory();
    }

    /**
     * @expectedException \Exception
     */
    public function testToArrayInexistant()
    {
        $this->_unparserFactory->getClass('exception');
    }

    public function testJsonClass()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\unparse\\Json', $this->_unparserFactory->getClass('json'));
    }

    public function testXmlClass()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\unparse\\Xml', $this->_unparserFactory->getClass('xml'));
    }

    public function testJsonToArray()
    {
        $json = $this->_unparserFactory->getClass('json');

        $this->assertEquals(
            array('id1' => array('parametre1' => 'variable1')), $json->toArray('{"id1":{"parametre1":"variable1"}}')
        );
    }

    public function testXmlToArray()
    {
        $xml = $this->_unparserFactory->getClass('xml');

        $this->assertEquals(
            array('id1' => array('parametre1' => 'variable1')),
            $xml->toArray('<root><element attr="id1"><element attr="parametre1">variable1</element></element></root>')
        );
    }
}
