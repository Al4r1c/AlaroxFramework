<?php
namespace Tests\Config;

use AlaroxFramework\cfg\route\Route;
use AlaroxFramework\cfg\route\RouteMap;

class RouteMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouteMap
     */
    private $_routeMap;

    public function setUp()
    {
        $this->_routeMap = new RouteMap();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\cfg\route\RouteMap', $this->_routeMap);
    }

    public function testAjouterUneRoute()
    {
        $route = new Route();

        $this->_routeMap->ajouterRoute($route);

        $this->assertContains($route, $this->_routeMap->getRoutes());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAjouterUneRouteTestType()
    {
        $this->_routeMap->ajouterRoute(50);
    }

    public function testSetStaticAliases()
    {
        $this->_routeMap->setStaticAliases(array('static'));

        $this->assertEquals(array('static'), $this->_routeMap->getStaticAliases());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetStaticAliasesArray()
    {
        $this->_routeMap->setStaticAliases('exception');
    }

    public function testSetControleurDefaut()
    {
        $this->_routeMap->setControlerParDefaut('controlleurNom');

        $this->assertEquals('controlleurNom', $this->_routeMap->getControlerParDefaut());
    }

    public function testSetRouteMapDepuisFichier()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will(
                $this->returnValue(
                    array('Default_controller' => 'defCtrl',
                        'RouteMap' => array(
                            '/routeTo' => array(
                                'controller' => 'ctrl',
                                'pattern' => 'pattern',
                                'defaultAction' => 'defAct',
                                'mapping' => array())
                        ),
                        'Static' => array('statik'))
                )
            );

        $this->_routeMap->setRouteMapDepuisFichier($fichier);

        $this->assertCount(1, $this->_routeMap->getRoutes());
        $this->assertContainsOnlyInstancesOf('\AlaroxFramework\cfg\route\Route', $this->_routeMap->getRoutes());
        $this->assertEquals('defCtrl', $this->_routeMap->getControlerParDefaut());
        $this->assertEquals(array('statik'), $this->_routeMap->getStaticAliases());
    }

    /**
     * @expectedException \Exception
     */
    public function testSetRouteMapDepuisFichierInexistant()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(false));

        $this->_routeMap->setRouteMapDepuisFichier($fichier);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetRouteMapMissingKey()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue(array('Static' => array())));

        $this->_routeMap->setRouteMapDepuisFichier($fichier);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetRouteMapMissingKeyInMap()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will(
                $this->returnValue(array('Default_controller' => '', 'RouteMap' => array('hello'), 'Static' => '',))
            );

        $this->_routeMap->setRouteMapDepuisFichier($fichier);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRouteMapTypeErrone()
    {
        $this->_routeMap->setRouteMapDepuisFichier(array());
    }

    public function testGetUneRouteByCtrl()
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getController'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('ctrlcontroll'));

        $this->_routeMap->ajouterRoute($route);

        $this->assertSame($route, $this->_routeMap->getUneRouteByController('ctrlcontroll'));
    }

    public function testGetUneRouteByCtrlNonTrouvee()
    {
        $this->assertNull($this->_routeMap->getUneRouteByController('monuri'));
    }
}
