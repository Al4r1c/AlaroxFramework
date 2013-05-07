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

    public function testSetRestInfos()
    {
        $restInfos = $this->getMock('AlaroxFramework\cfg\configs\RestInfos');

        $this->_restClient->setRestInfos($restInfos);

        $this->assertAttributeEquals($restInfos, '_restInfos', $this->_restClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestInfosErrone()
    {
        $this->_restClient->setRestInfos(array());
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
        $restInfos = $this->getMock('AlaroxFramework\cfg\configs\RestInfos');
        $objetRequete = $this->getMock('AlaroxFramework\Utils\ObjetRequete');
        $objetReponse = $this->getMock('AlaroxFramework\Utils\ObjetReponse');

        $curlClient->expects($this->once())
            ->method('executer')
            ->with($restInfos, $objetRequete)
            ->will($this->returnValue($objetReponse));

        $this->_restClient->setCurlClient($curlClient);
        $this->_restClient->setRestInfos($restInfos);

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
    public function testExecuteRestInfosMissing()
    {
        $curlClient = $this->getMock('AlaroxFramework\utils\restclient\CurlClient');
        $this->_restClient->setCurlClient($curlClient);

        $this->_restClient->executerRequete($this->getMock('AlaroxFramework\Utils\ObjetRequete'));
    }
}
