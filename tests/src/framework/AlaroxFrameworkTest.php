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

        $this->_framework->genererConfigDepuisFichiers(
            '/path/to/fichier', '/path/to/routemap', '/path/to/controllers', '/path/to/templates', '/path/to/locales'
        );
    }

    public function testProcess()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getDispatcher', 'getResponseManager'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $reponseManager = $this->getMock('\\AlaroxFramework\\reponse\ReponseManager', array('getHtmlResponse'));
        $htmlReponse = $this->getMock('\\AlaroxFramework\\utils\\HtmlReponse', array('getCorpsReponse', 'getStatusHttp'));
        $config = $this->getMock('\\AlaroxFramework\\cfg\\Config');


        $htmlReponse->expects($this->any())->method('getCorpsReponse')->will($this->returnValue('resultat'));
        $htmlReponse->expects($this->any())->method('getStatusHttp')->will($this->returnValue(200));

        $dispatcher->expects($this->once())->method('executerActionRequise')->will($this->returnValue('resultat'));

        $reponseManager->expects($this->once())->method('getHtmlResponse')->with('resultat')->will($this->returnValue($htmlReponse));

        $conteneur->expects($this->once())
            ->method('getDispatcher')
            ->with($config)
            ->will($this->returnValue($dispatcher));

        $conteneur->expects($this->once())
            ->method('getResponseManager')
            ->with($config)
            ->will($this->returnValue($reponseManager));


        $this->_framework->setConteneur($conteneur);
        $this->_framework->setConfig($config);


        $this->assertInstanceOf('\\AlaroxFramework\\utils\\HtmlReponse', $htmlReponse = $this->_framework->process());
        $this->assertEquals('resultat', $htmlReponse->getCorpsReponse());
        $this->assertEquals(200, $htmlReponse->getStatusHttp());
    }

    /**
     * @expectedException \Exception
     */
    public function testErreurWebsiteDev()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getDispatcher'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $config = $this->getMock('\\AlaroxFramework\\cfg\\Config', array('isProdVersion'));


        $dispatcher
            ->expects($this->once())
            ->method('executerActionRequise')
            ->will($this->throwException(new \Exception()));

        $conteneur->expects($this->once())
            ->method('getDispatcher')
            ->with($config)
            ->will($this->returnValue($dispatcher));

        $config->expects($this->atLeastOnce())
            ->method('isProdVersion')
            ->will($this->returnValue(false));


        $this->_framework->setConteneur($conteneur);
        $this->_framework->setConfig($config);

        $this->_framework->process();
    }

    public function testErreurWebsiteProd()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getDispatcher'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $config = $this->getMock('\\AlaroxFramework\\cfg\\Config', array('isProdVersion'));


        $dispatcher
            ->expects($this->once())
            ->method('executerActionRequise')
            ->will($this->throwException(new \Exception()));

        $conteneur->expects($this->once())
            ->method('getDispatcher')
            ->with($config)
            ->will($this->returnValue($dispatcher));

        $config->expects($this->atLeastOnce())
            ->method('isProdVersion')
            ->will($this->returnValue(true));


        $this->_framework->setConteneur($conteneur);
        $this->_framework->setConfig($config);


        $this->assertInstanceOf('\\AlaroxFramework\\utils\\HtmlReponse', $htmlReponse = $this->_framework->process());
        $this->assertEquals(500, $htmlReponse->getStatusHttp());
    }
}
