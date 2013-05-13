<?php
namespace Tests\Config;

use AlaroxFramework\cfg\rest\RestServerManager;

class RestServerManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestServerManager
     */
    private $_restServerManager;

    public function setUp()
    {
        $this->_restServerManager = new RestServerManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\rest\\RestServerManager', $this->_restServerManager);
    }

    public function testAddRestServer()
    {
        $restServer = $this->getMock('\\AlaroxFramework\\cfg\\rest\\RestServer');

        $this->_restServerManager->addRestServer('myRest', $restServer);

        $this->assertSame($restServer, $this->_restServerManager->getRestServer('myRest'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddRestServerType()
    {
        $this->_restServerManager->addRestServer('myRest', 'exception');
    }

    public function testGetNotFound()
    {
        $this->assertNull($this->_restServerManager->getRestServer('notFoundRest'));
    }
}
