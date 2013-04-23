<?php
namespace Tests\lib;

use AlaroxFramework\utils\unparser\UnparserFactory;

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
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\unparser\\Json', $this->_unparserFactory->getClass('json'));
    }

    public function testXmlClass()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\unparser\\Xml', $this->_unparserFactory->getClass('xml'));
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
            array('id1' => array('parametre1' => 'variable1', 'param2' => array('hello', 'secondhello'))),
            $xml->toArray('<root><id1><parametre1>variable1</parametre1><param2>hello</param2><param2>secondhello</param2></id1></root>')
        );
    }
}
