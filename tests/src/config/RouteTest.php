<?php
namespace Tests\Config;

use AlaroxFramework\cfg\route\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Route
     */
    private $_route;

    public function setUp()
    {
        $this->_route = new Route();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\cfg\route\Route', $this->_route);
    }

    public function testUri()
    {
        $this->_route->setUri('/monuri');

        $this->assertEquals('/monuri', $this->_route->getUri());
    }

    public function testSetUriAjouterSlashSiNonPresent()
    {
        $this->_route->setUri('monuri');

        $this->assertEquals('/monuri', $this->_route->getUri());
    }

    public function testController()
    {
        $this->_route->setController('controller');

        $this->assertEquals('controller', $this->_route->getController());
    }

    public function testPattern()
    {
        $this->_route->setPattern('/$action?');

        $this->assertEquals('/$action?', $this->_route->getPattern());
    }

    public function testPatternAjouterSlashSiNonPresent()
    {
        $this->_route->setPattern('pattern');

        $this->assertEquals('/pattern', $this->_route->getPattern());
    }

    public function testDefaultAction()
    {
        $this->_route->setDefaultAction('actionDef');

        $this->assertEquals('actionDef', $this->_route->getDefaultAction());
    }

    public function testMapping()
    {
        $this->_route->setMapping(array('/*' => 'actionMapped'));

        $this->assertEquals(array('/*' => 'actionMapped'), $this->_route->getMapping());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMappingArray()
    {
        $this->_route->setMapping('exception');
    }
}
