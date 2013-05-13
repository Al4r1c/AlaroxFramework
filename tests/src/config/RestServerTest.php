<?php
namespace Tests\Config;

use AlaroxFramework\cfg\rest\RestServer;

class RestServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestServer
     */
    private $_restServer;

    public function setUp()
    {
        $this->_restServer = new RestServer();
    }

    public function testSetFormatEnvoi()
    {
        $this->_restServer->setFormatEnvoi('json');

        $this->assertEquals('json', $this->_restServer->getFormatEnvoi());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetFormatEnvoiErrone()
    {
        $this->_restServer->setFormatEnvoi('getout');
    }

    public function testSetUrl()
    {
        $this->_restServer->setUrl('http://rest.server.com/');

        $this->assertEquals('http://rest.server.com/', $this->_restServer->getUrl());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUrlFausse()
    {
        $this->_restServer->setUrl('away');
    }

    public function testSetAuth()
    {
        $auth = $this->getMock('\\AlaroxFramework\\cfg\\rest\\Auth');

        $this->_restServer->setAuth($auth);

        $this->assertTrue($this->_restServer->isAuthEnabled());
        $this->assertSame($auth, $this->_restServer->getAuth());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetAuthTypeErrone()
    {
        $this->_restServer->setAuth('exception');
    }
}

