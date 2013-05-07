<?php
namespace Tests\Config;

use AlaroxFramework\cfg\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $_config;

    protected static $cfgTest = array(
        'Website_version' => 'dev',
        'TemplateConfig' => array(
            'Cache' => true,
            'Charset' => 'utf-8',
            'Variables' => array(
                'Static' => array(
                    'Name' => 'WebName',
                    'Media_url' => 'http://media.addr.com'
                ),
                'Remote' => array(
                    'liste' => array(
                        'uri' => '/uri',
                        'method' => 'GET')
                )
            ),
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

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\cfg\Config', $this->_config);
    }

    public function testFichierConfigExisteConfigValide()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue(self::$cfgTest));

        $this->assertInternalType('array', $this->_config->validerEtChargerFichier($fichier));
    }

    /**
     * @expectedException \Exception
     */
    public function testFichierConfigExisteConfigInvalide()
    {
        $cfg = self::$cfgTest;
        unset($cfg['Website_version']);

        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue($cfg));

        $this->assertInternalType('array', $this->_config->validerEtChargerFichier($fichier));
    }

    /**
     * @expectedException \Exception
     */
    public function testFichierConfigNexistePas()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(false));

        $this->_config->validerEtChargerFichier($fichier);
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

    public function testSetRestClient()
    {
        $this->_config->setRestClient($restClient = $this->getMock('\\AlaroxFramework\\utils\\restclient\\RestClient'));

        $this->assertSame($restClient, $this->_config->getRestClient());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestClientTypeErrone()
    {
        $this->_config->setRestClient(5);
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

    public function testProdVersion()
    {
        $this->_config->setVersion('ProD');
        $this->assertTrue($this->_config->isProdVersion());
    }

    public function testSetTemplateConfig()
    {
        $this->_config->setTemplateConfig(
            $templateConfig = $this->getMock('AlaroxFramework\cfg\configs\TemplateConfig')
        );

        $this->assertSame($templateConfig, $this->_config->getTemplateConfig());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTemplateConfigType()
    {
        $this->_config->setTemplateConfig('exception');
    }
}
