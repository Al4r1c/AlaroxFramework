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
        $uri = '/monuri';
        $this->_route->setUri($uri);

        $this->assertEquals('/monuri', $this->_route->getUri());
    }

    public function testSetUriAjouterSlashSiNonPresent()
    {
        $uri = 'monuri';
        $this->_route->setUri($uri);

        $this->assertEquals('/monuri', $this->_route->getUri());
    }

    /**
     * @expectedException \Exception
     */
    public function testUriNotSet()
    {
        $this->_route->setUri($uri);
    }

    public function testController()
    {
        $ctrl = 'controller';
        $this->_route->setController($ctrl);

        $this->assertEquals('controller', $this->_route->getController());
    }

    /**
     * @expectedException \Exception
     */
    public function testControllerNotSet()
    {
        $this->_route->setController($ctrl);
    }

    public function testPattern()
    {
        $pattern = '/$action?';
        $this->_route->setPattern($pattern);

        $this->assertEquals('/$action?', $this->_route->getPattern());
    }

    public function testPatternAjouterSlashSiNonPresent()
    {
        $pattern = 'pattern';
        $this->_route->setPattern($pattern);

        $this->assertEquals('/pattern', $this->_route->getPattern());
    }

    public function testDefaultAction()
    {
        $actionDef = 'actionDef';
        $this->_route->setDefaultAction($actionDef);

        $this->assertEquals('actionDef', $this->_route->getDefaultAction());
    }

    /**
     * @expectedException \Exception
     */
    public function testDefaultActionNotSet()
    {
        $this->_route->setDefaultAction($defAct);
    }

    public function testMapping()
    {
        $mapping = array('/*' => 'actionMapped');
        $this->_route->setMapping($mapping);

        $this->assertEquals(array('/*' => 'actionMapped'), $this->_route->getMapping());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMappingArray()
    {
        $mapping = 'exception';
        $this->_route->setMapping($mapping);
    }
}
