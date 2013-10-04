<?php
namespace Tests\Config;

use AlaroxFramework\cfg\configs\Server;

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
        'QUERY_STRING' => '',
        'DOCUMENT_ROOT' => 'C:\www\nice',
        'REQUEST_URI' => '/nice/ctrl/id',
        'PHP_SELF' => '/nice/index.php'
    );

    public function setUp()
    {
        $this->_server = new Server();
    }

    public function testSetServeurVariable()
    {
        $this->_server->setServeurVariables(self::$donneesServer);

        $this->assertInternalType('array', $this->_server->getServeurVariables());
        $this->assertArrayHasKey('REQUEST_URI_NODIR', $this->_server->getServeurVariables());
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

    public function testSetServeurVariablePhpSelfDifferent()
    {
        $donneesServeur = self::$donneesServer;
        $donneesServeur['PHP_SELF'] = '/index.php';
        $donneesServeur['REQUEST_URI'] = '/ctrl/id';

        $this->_server->setServeurVariables($donneesServeur);

        $this->assertEquals('/ctrl/id', $this->_server->getUneVariableServeur('REQUEST_URI_NODIR'));
    }

    public function testSetServeurVariableQueryStringt()
    {
        $donneesServeur = self::$donneesServer;
        $donneesServeur['PHP_SELF'] = '/index.php';
        $donneesServeur['REQUEST_URI'] = '/someuri?id=1';
        $donneesServeur['QUERY_STRING'] = 'id=1';

        $this->_server->setServeurVariables($donneesServeur);

        $this->assertEquals('/someuri', $this->_server->getUneVariableServeur('REQUEST_URI_NODIR'));
    }
}