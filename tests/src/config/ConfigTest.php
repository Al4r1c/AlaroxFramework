<?php
namespace Tests\Config;

use AlaroxFramework\cfg\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $_config;

    protected static $cfgTest = array(
        'Website_version' => 'dev',
        'TemplateVars' => array(
            'Name' => 'WebName',
            'Media_url' => 'http://media.addr.com'
        ),
        'RestServer' => array(
            'Url' => 'http://google.fr/',
            'Format' => 'json',
            'Authentification' => array(
                'Enabled' => true,
                'Method' => 'method',
                'Username' => 'user',
                'PassKey' => 'key'
            )
        ),
        'InternationalizationConfig' => array(
            'Enabled' => true,
            'Default_language' => 'English',
            'Available' => array('English' => 'en', 'French' => 'fr'))
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

        $this->assertAttributeCount(4, '_tabConfiguration', $this->_config);
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
    public function testLangueNonAvailable()
    {
        $tabCfg = self::$cfgTest;
        unset($tabCfg['InternationalizationConfig']['Available']['English']);

        $this->setFakeCfg($tabCfg);
    }

    /**
     * @expectedException \Exception
     */
    public function testValeursMinimales()
    {
        $tableauConfigTest = self::$cfgTest;
        unset($tableauConfigTest['TemplateVars']);
        $this->setFakeCfg($tableauConfigTest);
    }

    public function testGetValeurConfig()
    {
        $this->setFakeCfg(self::$cfgTest);

        $this->assertEquals('WebName', $this->_config->getConfigValeur('TemplateVars.name'));
        $this->assertEquals(
            array(
                'Name' => 'WebName',
                'Media_url' => 'http://media.addr.com'
            ), $this->_config->getConfigValeur('TemplateVars')
        );
    }

    public function testValeurNonTrouveeNull()
    {
        $this->setFakeCfg(self::$cfgTest);

        $this->assertNull($this->_config->getConfigValeur('nope'));
    }

    public function testSetRouteMap()
    {
        $routeMap = $this->getMock('AlaroxFramework\cfg\route\RouteMap');

        $this->_config->setRouteMap($routeMap);

        $this->assertEquals($routeMap, $this->_config->getConfigValeur('ControllerConfig.RouteMap'));
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
        $server = $this->getMock('AlaroxFramework\cfg\configs\Server', array('getUneVariableServeur'));
        $server->expects($this->once())
            ->method('getUneVariableServeur')
            ->with('REQUEST_URI_NODIR')
            ->will($this->returnValue('/ctrl/uri'));

        $this->_config->recupererUriDepuisServer($server);

        $this->assertEquals('/ctrl/uri', $this->_config->getConfigValeur('ControllerConfig.Uri'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetServerTypeErrone()
    {
        $this->_config->recupererUriDepuisServer(5);
    }

    public function testSetRestInfos()
    {
        $routeMap = $this->getMock('AlaroxFramework\cfg\configs\RestInfos');

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

    public function testSetControllerFactory()
    {
        $ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory');

        $this->_config->setControllerFactory($ctrlFactory);

        $this->assertSame($ctrlFactory, $this->_config->getConfigValeur('ControllerConfig.CtrlFactory'));
    }

    /**
     * @expectedException \Exception
     */
    public function testSetCtrlFactoErrone()
    {
        $this->_config->setControllerFactory('yalll');
    }
}
