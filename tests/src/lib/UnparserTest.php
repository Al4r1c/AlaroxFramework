<?php
namespace Tests\lib;

use AlaroxFramework\utils\unparse\Unparser;

class UnparserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Unparser
     */
    private $_unparser;

    public function setUp()
    {
        $this->_unparser = new Unparser();
    }

    public function testSetUnparserFactory()
    {
        $unParserFactory = $this->getMock('\\AlaroxFramework\\utils\\unparse\\UnparserFactory');

        $this->_unparser->setUnparserFactory($unParserFactory);

        $this->assertAttributeSame($unParserFactory, '_unparserFactory', $this->_unparser);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUnparserFactoryTypeErrone()
    {
        $this->_unparser->setUnparserFactory(array());
    }

    public function testToArray()
    {
        $unParserFactory = $this->getMock('\\AlaroxFramework\\utils\\unparse\\UnparserFactory', array('getClass'));
        $abstractUnparser = $this->getMockForAbstractClass('\\AlaroxFramework\\utils\\unparse\\AbstractUnparser');

        $unParserFactory->expects($this->once())
            ->method('getClass')
            ->with('json')
            ->will($this->returnValue($abstractUnparser));

        $abstractUnparser->expects($this->once())
            ->method('toArray')
            ->with('{parameter:var}')
            ->will($this->returnValue(array('parameter' => 'var')));

        $this->_unparser->setUnparserFactory($unParserFactory);

        $this->assertEquals(array('parameter' => 'var'), $this->_unparser->toArray('{parameter:var}', 'json'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testToArrayNotString()
    {
        $this->_unparser->toArray(array(), 'json');
    }

    /**
     * @expectedException \Exception
     */
    public function testToArrayUnparserFactoryNotSet()
    {
        $this->_unparser->toArray('string', 'json');
    }
}
