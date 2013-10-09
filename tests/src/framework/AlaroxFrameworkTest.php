<?php
namespace Tests\Framework;

use AlaroxFramework\AlaroxFramework;
use AlaroxFramework\traitement\NotFoundException;

class AlaroxFrameworkTest extends \PHPUnit_Framework_TestCase
{
    /** @var AlaroxFramework */
    private $_framework;

    /**
     * @var array
     */
    private $_initialConfig = array();

    public function setUp()
    {
        $this->_framework = new AlaroxFramework();

        $this->_initialConfig['errorRepoting'] = error_reporting();
        $this->_initialConfig['displayError'] = ini_get('display_errors');
    }

    public function tearDown()
    {
        error_reporting($this->_initialConfig['errorRepoting']);
        ini_set('display_errors', $this->_initialConfig['displayError']);
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConteneurTypeErrone()
    {
        $this->_framework->setConteneur('conteneur');
    }

    public function testSetConfigDepuisChemin()
    {
        $arrayCfg = array(
            'configFile' => '/path/to/fichier',
            'routeFile' => '/path/to/routemap',
            'controllersPath' => '/path/to/controllers',
            'templatesPath' => '/path/to/templates/',
            'localesPath' => '/path/to/locale/'
        );

        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('createConfiguration'));
        $conteneur->expects($this->once())
        ->method('createConfiguration')
        ->with($arrayCfg)
        ->will($this->returnValue($this->getMock('\\AlaroxFramework\\cfg\\Config')));

        $this->_framework->setConteneur($conteneur);

        $this->_framework->genererConfig($arrayCfg);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConfigDepuisCheminMissingKey()
    {
        $this->_framework->genererConfig(array());
    }

    public function testProcess()
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', array('getDispatcher', 'getResponseManager'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $reponseManager = $this->getMock('\\AlaroxFramework\\reponse\ReponseManager', array('getHtmlResponse'));
        $htmlReponse =
            $this->getMock('\\AlaroxFramework\\utils\\HtmlReponse', array('getReponse', 'getStatusHttp'));


        $htmlReponse->expects($this->any())->method('getReponse')->will($this->returnValue('resultat'));
        $htmlReponse->expects($this->any())->method('getStatusHttp')->will($this->returnValue(200));

        $dispatcher->expects($this->once())->method('executerActionRequise')->will($this->returnValue('resultat'));

        $reponseManager->expects($this->once())->method('getHtmlResponse')->with('resultat')->will(
            $this->returnValue($htmlReponse)
        );

        $conteneur->expects($this->once())
        ->method('getDispatcher')
        ->will($this->returnValue($dispatcher));

        $conteneur->expects($this->once())
        ->method('getResponseManager')
        ->will($this->returnValue($reponseManager));


        $this->_framework->setConteneur($conteneur);


        $this->assertInstanceOf('\\AlaroxFramework\\utils\\HtmlReponse', $htmlReponse = $this->_framework->process());
        $this->assertEquals('resultat', $htmlReponse->getReponse());
        $this->assertEquals(200, $htmlReponse->getStatusHttp());
    }

    public function testErreurDispatcherRouteException()
    {
        $conteneur = $this->getFakeConteneur(array('getDispatcher', 'getResponseManager'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $responseManager = $this->getMock('\\AlaroxFramework\\response\\ResponseManager', array('getNotFoundTemplate'));
        $htmlResponseError = $this->getMock('\\AlaroxFramework\\utils\\HtmlResponse', array('getStatusHttp'));


        $dispatcher
        ->expects($this->once())
        ->method('executerActionRequise')
        ->will($this->throwException(new NotFoundException()));

        $conteneur->expects($this->once())
        ->method('getDispatcher')
        ->will($this->returnValue($dispatcher));

        $conteneur->expects($this->once())
        ->method('getResponseManager')
        ->will($this->returnValue($responseManager));

        $responseManager->expects($this->once())
        ->method('getNotFoundTemplate')
        ->will($this->returnValue($htmlResponseError));

        $htmlResponseError->expects($this->once())
        ->method('getStatusHttp')
        ->will($this->returnValue(404));


        $this->_framework->setConteneur($conteneur);

        $htmlReponse = $this->_framework->process();

        $this->assertEquals(404, $htmlReponse->getStatusHttp());
    }

    public function testErreurDispatcherFatalException()
    {
        $conteneur = $this->getFakeConteneur(array('getDispatcher', 'getResponseManager'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher', array('executerActionRequise'));
        $responseManager = $this->getMock('\\AlaroxFramework\\response\\ResponseManager', array('getNotFoundTemplate'));


        $dispatcher
        ->expects($this->once())
        ->method('executerActionRequise')
        ->will($this->throwException(new NotFoundException));

        $conteneur->expects($this->once())
        ->method('getDispatcher')
        ->will($this->returnValue($dispatcher));

        $conteneur->expects($this->once())
        ->method('getResponseManager')
        ->will($this->returnValue($responseManager));

        $responseManager->expects($this->once())
        ->method('getNotFoundTemplate')
        ->will($this->throwException(new \Exception()));


        $this->_framework->setConteneur($conteneur);

        $htmlReponse = $this->_framework->process();

        $this->assertEquals(500, $htmlReponse->getStatusHttp());
    }

    public function testErreurWebsiteProdResponseManager()
    {
        $conteneur = $this->getFakeConteneur(array('getDispatcher', 'getResponseManager'));
        $dispatcher = $this->getMock('\\AlaroxFramework\\traitement\\Dispatcher');
        $responseManager = $this->getMock('\\AlaroxFramework\\reponse\\ReponseManager', array('getHtmlResponse'));

        $responseManager
        ->expects($this->once())
        ->method('getHtmlResponse')
        ->will($this->throwException(new \Exception()));

        $conteneur->expects($this->once())
        ->method('getDispatcher')
        ->will($this->returnValue($dispatcher));

        $conteneur->expects($this->once())
        ->method('getResponseManager')
        ->will($this->returnValue($responseManager));

        $this->_framework->setConteneur($conteneur);

        $htmlReponse = $this->_framework->process();

        $this->assertInstanceOf(
            '\\AlaroxFramework\\utils\\HtmlReponse',
            $htmlReponse
        );
        $this->assertEquals(500, $htmlReponse->getStatusHttp());
    }

    private function getFakeConteneur($tabMethodes)
    {
        $conteneur = $this->getMock('\\AlaroxFramework\\Conteneur', $tabMethodes);

        return $conteneur;
    }
}
