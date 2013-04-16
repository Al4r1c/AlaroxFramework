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
            'Available' => array(
                'French' => array(
                    'alias' => 'fr',
                    'filename' => 'fr_FR.UTF-8'
                ),
                'English' => array(
                    'alias' => 'en',
                    'filename' => 'en_EN.UTF-8'
                )
            )
        )
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

        $this->assertEquals('dev', $this->_config->getVersion());
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\i18n\\Internationalization', $this->_config->getI18nConfig());
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\configs\\RestInfos', $this->_config->getRestInfos());
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

    public function testSetRouteMap()
    {
        $this->_config->setRouteMap($routeMap = $this->getMock('AlaroxFramework\cfg\route\RouteMap'));

        $this->assertSame($routeMap, $this->_config->getRouteMap());
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
        $this->_config->setServer($server = $this->getMock('AlaroxFramework\cfg\configs\Server'));

        $this->assertSame($server, $this->_config->getServer());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetServerTypeErrone()
    {
        $this->_config->setServer(5);
    }

    public function testSetRestInfos()
    {
        $this->_config->setRestInfos($restInfos = $this->getMock('AlaroxFramework\cfg\configs\RestInfos'));

        $this->assertSame($restInfos, $this->_config->getRestInfos());
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
        $this->_config->setCtrlFactory($ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory'));

        $this->assertSame($ctrlFactory, $this->_config->getCtrlFactory());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCtrlFactoErrone()
    {
        $this->_config->setCtrlFactory('yalll');
    }

    public function testSetI18n()
    {
        $this->_config->setI18nConfig($i18n = $this->getMock('AlaroxFramework\cfg\i18n\Internationalization'));

        $this->assertSame($i18n, $this->_config->getI18nConfig());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetI18nType()
    {
        $this->_config->setI18nConfig('exception');
    }

    public function testSetGlobals()
    {
        $globals = array('varone' => 'valeurone');

        $this->_config->setGlobals($globals);

        $this->assertSame($globals, $this->_config->getGlobals());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetGlobalsArray()
    {
        $this->_config->setGlobals('exception');
    }
}
