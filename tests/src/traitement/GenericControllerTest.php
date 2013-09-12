<?php
namespace Tests\traitement;

use AlaroxFramework\traitement\controller\GenericController;

class GenericControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericController
     */
    private $_genericCtrl;

    public function setUp()
    {
        $this->_genericCtrl =
            $this->getMockForAbstractClass('\\AlaroxFramework\\traitement\\controller\\GenericController');
    }

    public function testInstance()
    {
        $this->assertInstanceOf('AlaroxFramework\\traitement\\controller\\GenericController', $this->_genericCtrl);
        $this->assertAttributeInstanceOf('AlaroxFileManager\\AlaroxFile', '_alaroxFile', $this->_genericCtrl);
    }

    public function testRestClient()
    {
        $restClient = $this->getMock('AlaroxFramework\\utils\\restclient\\RestClient');

        $this->_genericCtrl->setRestClient($restClient);

        $this->assertAttributeSame($restClient, '_restClient', $this->_genericCtrl);
    }

    public function testTabVariables()
    {
        $this->_genericCtrl->setVariablesRequete(array('var1' => 'val1'));

        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('getVariablesRequete');
        $method->setAccessible(true);

        $this->assertEquals(array('var1' => 'val1'), $method->invoke($this->_genericCtrl));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTabVariablesArray()
    {
        $this->_genericCtrl->setVariablesRequete('exception');
    }

    public function testGetUneVariable()
    {
        $this->_genericCtrl->setVariablesRequete(array('paramKey' => 'maVar'));

        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('getUneVariableRequete');
        $method->setAccessible(true);

        $this->assertEquals('maVar', $method->invokeArgs($this->_genericCtrl, array('paramKey')));
        $this->assertNull($method->invokeArgs($this->_genericCtrl, array('keyNotFound')));
    }

    public function testGenerateView()
    {
        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('generateView');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            'AlaroxFramework\\utils\\View',
            $view = $method->invokeArgs($this->_genericCtrl, array('template.twig'))
        );
        $this->assertEquals('template.twig', $view->getViewName());
    }

    public function testExecuteRequest()
    {
        $objetRequete = $this->getMock('AlaroxFramework\\utils\\ObjetRequete');
        $objetReponse = $this->getMock('AlaroxFramework\\utils\\ObjetReponse');
        $restClient = $this->getMock('AlaroxFramework\\utils\restclient\\RestClient', array('executerRequete'));

        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('executeRequest');
        $method->setAccessible(true);

        $restClient->expects($this->once())
        ->method('executerRequete')
        ->with('remote', $objetRequete)
        ->will($this->returnValue($objetReponse));

        $this->_genericCtrl->setRestClient($restClient);

        $this->assertSame($objetReponse, $method->invokeArgs($this->_genericCtrl, array('remote', $objetRequete)));
    }

    public function testVariablesPost()
    {
        $this->_genericCtrl->setVariablesPost(array('lets' => 'go'));

        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('getVariablesPost');
        $method->setAccessible(true);

        $this->assertEquals(array('lets' => 'go'), $method->invoke($this->_genericCtrl));
    }

    public function testGetUneVariablePost()
    {
        $this->_genericCtrl->setVariablesPost(array('aaa' => 'bbb'));

        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('getUneVariablePost');
        $method->setAccessible(true);

        $this->assertEquals('bbb', $method->invokeArgs($this->_genericCtrl, array('aaa')));
        $this->assertNull($method->invokeArgs($this->_genericCtrl, array('keyNotFound')));
    }

    public function testGetFile()
    {
        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('getFile');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            'AlaroxFileManager\\FileManager\\File',
            $method->invokeArgs($this->_genericCtrl, array('myFile.txt'))
        );
    }

    public function testAddPostViewVariable()
    {
        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('addBeforeGenerateViewVariables');
        $method->setAccessible(true);
        $method->invokeArgs($this->_genericCtrl, array('var', 'value'));

        $this->testGenerateView();
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirectUrl()
    {
        $class = new \ReflectionClass('\\AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('redirectToUrl');
        $method->setAccessible(true);
        $method->invokeArgs($this->_genericCtrl, array('http://google.fr/'));

        $headersList = xdebug_get_headers();
        $this->assertContains('Location: http://google.fr/', $headersList, false);
        $this->assertCount(1, $headersList);
        $this->assertEquals(302, http_response_code());
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirectUrlCodeHttp()
    {
        $class = new \ReflectionClass('\\AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('redirectToUrl');
        $method->setAccessible(true);
        $method->invokeArgs($this->_genericCtrl, array('http://google.fr/', 500, false));

        $this->assertEquals(500, http_response_code());
    }

    public function testSessionClient()
    {
        $class = new \ReflectionClass('\\AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('getSession');
        $method->setAccessible(true);


        $sessionClient = $this->getMock('AlaroxFramework\\utils\\session\\SessionClient');

        $this->_genericCtrl->setSessionClient($sessionClient);

        $this->assertSame($sessionClient, $method->invoke($this->_genericCtrl));
    }

    public function testGetAlaroxFile()
    {
        $class = new \ReflectionClass('AlaroxFramework\\traitement\\controller\\GenericController');
        $method = $class->getMethod('getAlaroxFile');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            'AlaroxFileManager\\AlaroxFile',
            $method->invoke($this->_genericCtrl)
        );
    }
}
