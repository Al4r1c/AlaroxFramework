<?php
namespace Tests\Config;

use AlaroxFramework\cfg\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server */
    private $_server;

    private static $donneesServer = array(
        'SERVER_SIGNATURE' => '<address>Apache/2.4.3 (Win32) OpenSSL/1.0.1c PHP/5.4.7 Server at server.com Port 80</address>',
        'SERVER_SOFTWARE' => 'Apache/2.4.3 (Win32) OpenSSL/1.0.1c PHP/5.4.7',
        'SERVER_NAME' => 'server.com',
        'SERVER_ADDR' => '127.0.0.1',
        'SERVER_PORT' => '80',
        'REMOTE_ADDR' => '127.0.0.1',
        'DOCUMENT_ROOT' => 'C:\www\nice',
        'REQUEST_URI' => '/ctrl/id'
    );

    public function setUp()
    {
        $this->_server = new Server();
    }

    public function testSetServeurVariable()
    {
        $this->_server->setServeurVariables(self::$donneesServer);

        $this->assertEquals(self::$donneesServer, $this->_server->getServeurVariables());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetServeurDonneesErronee()
    {
        $this->_server->setServeurVariables(null);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetServeurVarManquante()
    {
        $donneesServeur = self::$donneesServer;
        unset($donneesServeur['REQUEST_URI']);
        $this->_server->setServeurVariables($donneesServeur);
    }

    public function testGetUneVariableServeur()
    {
        $this->_server->setServeurVariables(self::$donneesServer);

        $this->assertEquals('server.com', $this->_server->getUneVariableServeur('SERVER_NAME'));
    }

    public function testGetUneVariableServeurNonTrouveRenvoiNull()
    {
        $this->_server->setServeurVariables(self::$donneesServer);

        $this->assertNull($this->_server->getUneVariableServeur('NO_NO_NO'));
    }
}