<?php
namespace Tests\Config;

use AlaroxFramework\cfg\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $_config;

    protected static $cfgTest = array(
        'ControllerConfig' => array(
            'Default_controller' => 'ctrldef',
            'RestServer' => array(
                'Url' => 'http://addr.com',
                'Username' => 'Username',
                'Key' => 'MaClef',
                'Format' => 'json'
            ),
            'RouteMap' => true
        ),
        'TemplateConfig' => array(
            'Name' => 'WebName',
            'Media_url' => 'http://media.addr.com'
        ),
        'InternationalizationConfig' => array(
            'Enabled' => true,
            'Default_language' => 'fr',
            'Available' => array('English' => 'en'))
    );

    public function setUp()
    {
        $this->_config = new Config();
    }

    public function setFakeCfg($tabRenvoyee)
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue($tabRenvoyee));

        $this->_config->recupererConfigDepuisFichier($fichier);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\cfg\Config', $this->_config);
    }

    public function testSetCfg()
    {
        $this->setFakeCfg(self::$cfgTest);

        $this->assertAttributeEquals(self::$cfgTest, '_tabConfiguration', $this->_config);
    }

    /**
     * @expectedException \Exception
     */
    public function testFichierConfigNexistePas()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(false));

        $this->_config->recupererConfigDepuisFichier($fichier);
    }

    /**
     * @expectedException \Exception
     */
    public function testValeursMinimales()
    {
        $tableauConfigTest = self::$cfgTest;
        unset($tableauConfigTest['ControllerConfig']);
        $this->setFakeCfg($tableauConfigTest);
    }

    public function testGetValeurConfig()
    {
        $this->setFakeCfg(self::$cfgTest);

        $this->assertEquals('ctrldef', $this->_config->getConfigValeur('controllerconfig.default_controller'));
        $this->assertEquals(
            array(
                'Name' => 'WebName',
                'Media_url' => 'http://media.addr.com'
            ), $this->_config->getConfigValeur('TemplateConfig')
        );
    }

    public function testValeurNonTrouveeNull()
    {
        $this->setFakeCfg(self::$cfgTest);

        $this->assertNull($this->_config->getConfigValeur('nope'));
    }

    public function testSetRouteMap()
    {
        $routeMap = $this->getMock('AlaroxFramework\cfg\RouteMap');

        $this->_config->setRouteMap($routeMap);

        $this->assertEquals($routeMap, $this->_config->getConfigValeur('ControllerConfig.RouteMapFile'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRouteMapTypeErrone()
    {
        $this->_config->setRouteMap(5);
    }

    public function testSetServer()
    {
        $server = $this->getMock('AlaroxFramework\cfg\Server', array('getUneVariableServeur'));
        $server->expects($this->once())
            ->method('getUneVariableServeur')
            ->with('REQUEST_URI')
            ->will($this->returnValue('/ctrl/uri'));

        $this->_config->parseServer($server);

        $this->assertEquals('/ctrl/uri', $this->_config->getConfigValeur('ControllerConfig.Uri'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetServerTypeErrone()
    {
        $this->_config->parseServer(5);
    }

    public function testSetRestInfos()
    {
        $routeMap = $this->getMock('AlaroxFramework\cfg\RestInfos');

        $this->_config->setRestInfos($routeMap);

        $this->assertEquals($routeMap, $this->_config->getConfigValeur('ControllerConfig.RestServer'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestInfosTypeErrone()
    {
        $this->_config->setRestInfos(5);
    }
}
