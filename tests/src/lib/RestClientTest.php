<?php
namespace Tests\lib;

use AlaroxFramework\utils\restclient\RestClient;

class RestClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestClient
     */
    private $_restClient;

    public function setUp()
    {
        $this->_restClient = new RestClient();
    }

    public function testSetRestServer()
    {
        $this->_restClient->setRestServerManager(
            $restServerManager = $this->getMock('AlaroxFramework\\cfg\\rest\\RestServerManager')
        );

        $this->assertAttributeEquals($restServerManager, '_restServerManager', $this->_restClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestServerErrone()
    {
        $this->_restClient->setRestServerManager(array());
    }

    public function testSetCurlClient()
    {
        $curlClient = $this->getMock('AlaroxFramework\\utils\\restclient\\CurlClient');

        $this->_restClient->setCurlClient($curlClient);

        $this->assertAttributeEquals($curlClient, '_curlClient', $this->_restClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCurlClientErrone()
    {
        $this->_restClient->setCurlClient(9);
    }

    public function testExecute()
    {
        $curlClient = $this->getMock('AlaroxFramework\utils\restclient\CurlClient', array('executer'));
        $restServer = $this->getMock('AlaroxFramework\cfg\rest\RestServer');
        $restServerManager = $this->getMock('AlaroxFramework\cfg\rest\RestServerManager', array('getRestServer'));
        $objetRequete = $this->getMock('AlaroxFramework\Utils\ObjetRequete');
        $objetReponse = $this->getMock('AlaroxFramework\Utils\ObjetReponse');

        $curlClient->expects($this->once())
            ->method('executer')
            ->with($restServer, $objetRequete)
            ->will($this->returnValue($objetReponse));

        $restServerManager->expects($this->once())
            ->method('getRestServer')
            ->with('restServerKey')
            ->will($this->returnValue($restServer));

        $this->_restClient->setCurlClient($curlClient);
        $this->_restClient->setRestServerManager($restServerManager);

        $this->assertEquals($objetReponse, $this->_restClient->executerRequete('restServerKey', $objetRequete));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteObjetRequeteErrone()
    {
        $this->_restClient->executerRequete('key', array());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteCurlClientMissing()
    {
        $this->_restClient->executerRequete('key', $this->getMock('AlaroxFramework\Utils\ObjetRequete'));
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteRestServerMissing()
    {
        $curlClient = $this->getMock('AlaroxFramework\utils\restclient\CurlClient');
        $this->_restClient->setCurlClient($curlClient);

        $this->_restClient->executerRequete('key', $this->getMock('AlaroxFramework\Utils\ObjetRequete'));
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteRestServerNotFound()
    {
        $restServerManager = $this->getMock('AlaroxFramework\cfg\rest\RestServerManager', array('getRestServer'));

        $restServerManager->expects($this->once())
            ->method('getRestServer')
            ->with('restServerKey')
            ->will($this->returnValue(null));

        $this->_restClient->setCurlClient(
            $this->getMock('AlaroxFramework\utils\restclient\CurlClient', array('executer'))
        );
        $this->_restClient->setRestServerManager($restServerManager);

        $this->_restClient->executerRequete('restServerKey', $this->getMock('AlaroxFramework\Utils\ObjetRequete'));
    }
}
