<?php
namespace Tests\lib;

use AlaroxFramework\utils\parser\ParserFactory;

class ParserFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParserFactory
     */
    private $_unparserFactory;

    public function setUp()
    {
        $this->_unparserFactory = new ParserFactory();
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
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\parser\\Json', $this->_unparserFactory->getClass('json'));
    }

    public function testXmlClass()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\parser\\Xml', $this->_unparserFactory->getClass('xml'));
    }

    public function testPlainClass()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\parser\\Plain', $this->_unparserFactory->getClass('txt'));
    }

    public function testArrayToJson()
    {
        $json = $this->_unparserFactory->getClass('json');

        $this->assertEquals(
            '{"id1":{"parametre1":"variable1"}}',
            $json->parse(array('id1' => array('parametre1' => 'variable1')))
        );
    }

    public function testArrayToXml()
    {
        $xml = $this->_unparserFactory->getClass('xml');

        $this->assertEquals(
            "<?xml version=\"1.0\"?>\n<root><_id1><parametre1>variable1</parametre1></_id1></root>\n",
            $xml->parse(array('_id1' => array('parametre1' => 'variable1')))
        );
    }

    public function testArrayToPlain()
    {
        $plain = $this->_unparserFactory->getClass('txt');

        $this->assertEquals(
            'id1%5Bparametre1%5D=variable1',
            $plain->parse(array('id1' => array('parametre1' => 'variable1')))
        );
    }
}
