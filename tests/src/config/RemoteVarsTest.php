<?php
namespace Tests\Config;

use AlaroxFramework\cfg\globals\RemoteVars;

class RemoteVarsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RemoteVars
     */
    private $_remoteVars;

    public function setUp()
    {
        $this->_remoteVars = new RemoteVars();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\globals\\RemoteVars', $this->_remoteVars);
    }

    public function testRestClient()
    {
        $restClient = $this->getMock('AlaroxFramework\utils\restclient\RestClient');

        $this->_remoteVars->setRestClient($restClient);

        $this->assertAttributeSame($restClient, '_restClient', $this->_remoteVars);
    }

    public function testAddRemoteVar()
    {
        $objetRequete = $this->getMock('AlaroxFramework\utils\ObjetRequete');

        $this->_remoteVars->addRemoteVar('clef', $objetRequete);

        $this->assertAttributeCount(1, '_listeRemoteVars', $this->_remoteVars);
        $this->assertAttributeContainsOnly(
            'AlaroxFramework\utils\ObjetRequete',
            '_listeRemoteVars',
            $this->_remoteVars
        );
    }

    public function testGetRemoteVarsExecutees()
    {
        $restClient = $this->getMock('AlaroxFramework\utils\restclient\RestClient', array('executerRequete'));

        $objetRequete = $this->getMock('AlaroxFramework\utils\ObjetRequete');
        $objetReponse = $this->getMock('AlaroxFramework\utils\ObjetReponse', array('toArray'));


        $restClient->expects($this->once())
            ->method('executerRequete')
            ->with($objetRequete)
            ->will($this->returnValue($objetReponse));

        $objetReponse->expects($this->once())
            ->method('toArray')
            ->will($this->returnValue(array('value')));


        $this->_remoteVars->addRemoteVar('clef', $objetRequete);
        $this->_remoteVars->setRestClient($restClient);

        $this->assertEquals(array('clef' => array('value')), $this->_remoteVars->getRemoteVarsExecutees());
    }
}
