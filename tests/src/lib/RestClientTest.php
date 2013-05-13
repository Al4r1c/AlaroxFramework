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
        $this->_restClient->setRestServer($restServer = $this->getMock('AlaroxFramework\cfg\rest\RestServer'));

        $this->assertAttributeEquals($restServer, '_restServer', $this->_restClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestServerErrone()
    {
        $this->_restClient->setRestServer(array());
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
        $objetRequete = $this->getMock('AlaroxFramework\Utils\ObjetRequete');
        $objetReponse = $this->getMock('AlaroxFramework\Utils\ObjetReponse');

        $curlClient->expects($this->once())
            ->method('executer')
            ->with($restServer, $objetRequete)
            ->will($this->returnValue($objetReponse));

        $this->_restClient->setCurlClient($curlClient);
        $this->_restClient->setRestServer($restServer);

        $this->assertEquals($objetReponse, $this->_restClient->executerRequete($objetRequete));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteObjetRequeteErrone()
    {
        $this->_restClient->executerRequete(array());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteCurlClientMissing()
    {
        $this->_restClient->executerRequete($this->getMock('AlaroxFramework\Utils\ObjetRequete'));
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteRestServerMissing()
    {
        $curlClient = $this->getMock('AlaroxFramework\utils\restclient\CurlClient');
        $this->_restClient->setCurlClient($curlClient);

        $this->_restClient->executerRequete($this->getMock('AlaroxFramework\Utils\ObjetRequete'));
    }
}
