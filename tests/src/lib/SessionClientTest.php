<?php
namespace Tests\lib;

use AlaroxFramework\utils\session\SessionClient;

class SessionClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionClient
     */
    private $_sessionClient;

    public function setUp()
    {
        $this->_sessionClient = new SessionClient();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\session\\SessionClient', $this->_sessionClient);
    }

    public function testSessionRef()
    {
        $testArray = array('h' => 'val');

        $this->_sessionClient->setSessionRef($testArray);

        $this->assertAttributeSame($testArray, '_sessionRef', $this->_sessionClient);
        $this->assertAttributeCount(1, '_sessionRef', $this->_sessionClient);

        $testArray['newKey'] = 'val';

        $this->assertAttributeSame($testArray, '_sessionRef', $this->_sessionClient);
        $this->assertAttributeCount(2, '_sessionRef', $this->_sessionClient);
    }

    public function testSetValue()
    {
        $testArray = array();
        $this->_sessionClient->setSessionRef($testArray);

        $this->_sessionClient->setSessionValue('newKey', 'newVal');

        $this->assertAttributeCount(1, '_sessionRef', $this->_sessionClient);
        $this->assertCount(1, $testArray);

        $this->assertEquals('newVal', $this->_sessionClient->getSessionValue('newKey'));
    }

    public function testDeleteValue()
    {
        $testArray = array('key1' => 'val1', 'key2' => 'val2');
        $this->_sessionClient->setSessionRef($testArray);

        $this->_sessionClient->setSessionValue('newKey', 'newVal');
        $this->assertAttributeCount(3, '_sessionRef', $this->_sessionClient);

        $this->_sessionClient->deleteValue('key1');
        $this->assertAttributeCount(2, '_sessionRef', $this->_sessionClient);
        $this->assertNull($this->_sessionClient->getSessionValue('key1'));
    }
}
