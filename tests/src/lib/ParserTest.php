<?php
namespace Tests\lib;

use AlaroxFramework\utils\parser\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $_parser;

    public function setUp()
    {
        $this->_parser = new Parser();
    }

    public function testSetParserFactory()
    {
        $unParserFactory = $this->getMock('\\AlaroxFramework\\utils\\parser\\ParserFactory');

        $this->_parser->setParserFactory($unParserFactory);

        $this->assertAttributeSame($unParserFactory, '_parserFactory', $this->_parser);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParserFactoryTypeErrone()
    {
        $this->_parser->setParserFactory(array());
    }

    public function testParse()
    {
        $parserFactory = $this->getMock('\\AlaroxFramework\\utils\\parser\\ParserFactory', array('getClass'));
        $abstractParser = $this->getMockForAbstractClass('\\AlaroxFramework\\utils\\parser\\AbstractParser');

        $parserFactory->expects($this->once())
            ->method('getClass')
            ->with('json')
            ->will($this->returnValue($abstractParser));

        $abstractParser->expects($this->once())
            ->method('parse')
            ->with(array('parameter' => 'var'))
            ->will($this->returnValue('{parameter:var}'));

        $this->_parser->setParserFactory($parserFactory);

        $this->assertEquals('{parameter:var}', $this->_parser->parse(array('parameter' => 'var'), 'json'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseNotArray()
    {
        $this->_parser->parse('exceptionString', 'json');
    }

    /**
     * @expectedException \Exception
     */
    public function testToArrayUnparserFactoryNotSet()
    {
        $this->_parser->parse(array('ok'), 'json');
    }
}
