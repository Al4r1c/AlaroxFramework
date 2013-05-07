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
        $this->_routeMap->setStaticAliases(array('/static'));

        $this->assertEquals(array('/static'), $this->_routeMap->getStaticAliases());
    }

    public function testSetUriFormalisee()
    {
        $this->_routeMap->setStaticAliases(array('///sta-tic//path///way/go///'));

        $this->assertEquals(array('/sta-tic/path/way/go'), $this->_routeMap->getStaticAliases());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetStaticAliasesArray()
    {
        $this->_routeMap->setStaticAliases('exception');
    }

    public function testSetRouteDefaut()
    {
        $this->_routeMap->setRouteParDefaut($route = $this->getMock('\AlaroxFramework\cfg\route\Route'));

        $this->assertSame($route, $this->_routeMap->getRouteParDefaut());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRouteDefautType()
    {
        $this->_routeMap->setRouteParDefaut(50);
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
                    array(
                        'Default' => array(
                            'controller' => 'defCtrl',
                            'action' => 'defAct'
                        ),
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
        $this->assertEquals('defctrl', $this->_routeMap->getRouteParDefaut()->getController());
        $this->assertEquals(array('/statik'), $this->_routeMap->getStaticAliases());
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
    public function testSetRouteMapKeyUriWrong()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will(
                $this->returnValue(
                    array('Default' => array(
                        'controller' => 'defCtrl',
                        'action' => 'defAct'
                    ),
                        'RouteMap' => array(array()),
                        'Static' => '')
                )
            );

        $this->_routeMap->setRouteMapDepuisFichier($fichier);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetRouteMapMissingKeyControllerInMap()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will(
                $this->returnValue(
                    array('Default' => array(
                        'controller' => 'defCtrl',
                        'action' => 'defAct'
                    ),
                        'RouteMap' => array('/hello' => array()),
                        'Static' => '',)
                )
            );

        $this->_routeMap->setRouteMapDepuisFichier($fichier);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetRouteMapMissingKeysDefualtCtrlAndMappingInMap()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will(
                $this->returnValue(
                    array('Default' => array(
                        'controller' => 'defCtrl',
                        'action' => 'defAct'
                    ),
                        'RouteMap' => array('/hello' => array(
                            'controller' => 'ctrl'
                        )),
                        'Static' => '',)
                )
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
}
