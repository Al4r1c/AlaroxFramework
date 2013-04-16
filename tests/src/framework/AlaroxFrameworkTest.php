<?php
namespace Tests\Framework;

use AlaroxFramework\AlaroxFramework;

class AlaroxFrameworkTest extends \PHPUnit_Framework_TestCase
{
    /** @var AlaroxFramework */
    private $_framework;

    public function setUp()
    {
        $this->_framework = new AlaroxFramework();
    }

    public function testFirst()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\AlaroxFramework', $this->_framework);
    }

    public function testSetConteneur()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur');

        $this->_framework->setConteneur($conteneur);

        $this->assertAttributeEquals($conteneur, '_conteneur', $this->_framework);
    }

    public function testSetConfig()
    {
        $config = $this->getMock('\\AlaroxFramework\\cfg\\Config');

        $this->_framework->setConfig($config);

        $this->assertAttributeEquals($config, '_config', $this->_framework);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConfigDepuisCheminTypeErrone()
    {
        $this->_framework->setConfig(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConteneurTypeErrone()
    {
        $this->_framework->setConteneur('conteneur');
    }

    public function testSetConfigDepuisChemin()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getConfig'));
        $conteneur->expects($this->once())
            ->method('getConfig')
            ->with('/path/to/fichier', '/path/to/routemap', '/path/to/controllers')
            ->will($this->returnValue($this->getMock('\\AlaroxFramework\\cfg\\Config')));

        $this->_framework->setConteneur($conteneur);

        $this->_framework->genererConfigDepuisFichiers('/path/to/fichier', '/path/to/routemap', '/path/to/controllers');
    }

    public function testProcess()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getDispatcher'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $config = $this->getMock('\\AlaroxFramework\\cfg\\Config');


        $dispatcher->expects($this->once())->method('executerActionRequise')->will($this->returnValue('resultat'));

        $conteneur->expects($this->once())
            ->method('getDispatcher')
            ->with($config)
            ->will($this->returnValue($dispatcher));


        $this->_framework->setConteneur($conteneur);
        $this->_framework->setConfig($config);


        $this->assertInstanceOf('\\AlaroxFramework\\utils\\HtmlReponse', $htmlReponse = $this->_framework->process());
        $this->assertEquals('resultat', $htmlReponse->getCorpsReponse());
    }

    /**
     * @expectedException \Exception
     */
    public function testErreurWebsiteDev()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getDispatcher'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $config = $this->getMock('\\AlaroxFramework\\cfg\\Config', array('getVersion'));


        $dispatcher
            ->expects($this->once())
            ->method('executerActionRequise')
            ->will($this->throwException(new \Exception()));

        $conteneur->expects($this->once())
            ->method('getDispatcher')
            ->with($config)
            ->will($this->returnValue($dispatcher));

        $config->expects($this->atLeastOnce())
            ->method('getVersion')
            ->will($this->returnValue('dev'));


        $this->_framework->setConteneur($conteneur);
        $this->_framework->setConfig($config);

        $this->_framework->process();
    }

    public function testErreurWebsiteProd()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getDispatcher'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $config = $this->getMock('\\AlaroxFramework\\cfg\\Config', array('getVersion'));


        $dispatcher
            ->expects($this->once())
            ->method('executerActionRequise')
            ->will($this->throwException(new \Exception()));

        $conteneur->expects($this->once())
            ->method('getDispatcher')
            ->with($config)
            ->will($this->returnValue($dispatcher));

        $config->expects($this->atLeastOnce())
            ->method('getVersion')
            ->will($this->returnValue('prod'));


        $this->_framework->setConteneur($conteneur);
        $this->_framework->setConfig($config);


        $this->assertInstanceOf('\\AlaroxFramework\\utils\\HtmlReponse', $htmlReponse = $this->_framework->process());
        $this->assertEquals(404, $htmlReponse->getStatusHttp());
    }
}
