<?php
namespace Tests\Config;

use AlaroxFramework\cfg\RouteMap;

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
        $this->assertInstanceOf('\AlaroxFramework\cfg\RouteMap', $this->_routeMap);
    }

    public function testSetRouteMapDepuisFichier()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue(array('route1' => 'ctrl1')));

        $this->_routeMap->setRouteMapDepuisFichier($fichier);

        $this->assertEquals(array('route1' => 'ctrl1'), $this->_routeMap->getRouteMap());
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
     * @expectedException \InvalidArgumentException
     */
    public function testSetRouteMapTypErrone()
    {
        $this->_routeMap->setRouteMapDepuisFichier(array());
    }
}
