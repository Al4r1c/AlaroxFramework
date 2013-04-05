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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUriString()
    {
        $this->_route->setUri(array());
    }

    public function testSetUriAjouterSlashSiNonPresent()
    {
        $uri = 'monuri';
        $this->_route->setUri($uri);

        $this->assertEquals('/monuri', $this->_route->getUri());
    }

    public function testUriSlashFormalisee()
    {
        $uri = 'monuri//////getto//////slashed//slasheeee////';
        $this->_route->setUri($uri);

        $this->assertEquals('/monuri/getto/slashed/slasheeee', $this->_route->getUri());
    }

    public function testController()
    {
        $ctrl = 'controller';
        $this->_route->setController($ctrl);

        $this->assertEquals('controller', $this->_route->getController());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testControllerString()
    {
        $this->_route->setController(array());
    }

    public function testPattern()
    {
        $pattern = '/$action?';
        $this->_route->setPattern($pattern);

        $this->assertEquals('/$action?', $this->_route->getPattern());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPatterString()
    {
        $this->_route->setPattern(array());
    }

    public function testPatternAjouterSlashSiNonPresent()
    {
        $pattern = 'pattern';
        $this->_route->setPattern($pattern);

        $this->assertEquals('/pattern', $this->_route->getPattern());
    }

    public function testPatternFormalise()
    {
        $pattern = '/$action?////slashed//$next?';
        $this->_route->setPattern($pattern);

        $this->assertEquals('/$action?/slashed/$next?', $this->_route->getPattern());
    }

    public function testDefaultAction()
    {
        $actionDef = 'actionDef';
        $this->_route->setDefaultAction($actionDef);

        $this->assertEquals('actionDef', $this->_route->getDefaultAction());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDefaultActionString()
    {
        $this->_route->setDefaultAction(array());
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
