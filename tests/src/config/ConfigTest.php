<?php
namespace Tests\Framework;

use AlaroxFramework\cfg\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $_config;

    static $cfgTest = array(
        'ControllerConfig' => array(
            'Default_controller' => 'ctrldef',
            'RestServer_url' => 'http://addr.com'
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

    public function setFakeCfg()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue(self::$cfgTest));

        $this->_config->recupererConfigDepuisFichier($fichier);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\cfg\Config', $this->_config);
    }

    public function testSetCfg()
    {
        $this->setFakeCfg();

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
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue(array('Missing')));

        $this->_config->recupererConfigDepuisFichier($fichier);
    }

    public function testGetValeurConfig()
    {
        $this->setFakeCfg();

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
        $this->setFakeCfg();

        $this->assertNull($this->_config->getConfigValeur('nope'));
    }
}
