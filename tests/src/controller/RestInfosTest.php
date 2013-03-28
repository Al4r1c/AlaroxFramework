<?php
namespace Tests\Controller;

use AlaroxFramework\Controller\RestInfos;

class RestInfosTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestInfos
     */
    private $_restClient;

    public function setUp()
    {
        $this->_restClient = new RestInfos();
    }

    public function testSetBody()
    {
        $this->_restClient->setBody(array('var1' => 'data1'));

        $this->assertEquals(array('var1' => 'data1'), $this->_restClient->getBody());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetBodyArray()
    {
        $this->_restClient->setBody('data');
    }

    public function testSetFormatEnvoi()
    {
        $this->_restClient->setFormatEnvoi('json');

        $this->assertEquals('json', $this->_restClient->getFormatEnvoi());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetFormatEnvoiErrone()
    {
        $this->_restClient->setFormatEnvoi('getout');
    }

    public function testSetMethodeHttp()
    {
        $this->_restClient->setMethodeHttp('GET');

        $this->assertEquals('GET', $this->_restClient->getMethodeHttp());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetMethodeHttpErronee()
    {
        $this->_restClient->setMethodeHttp('NON');
    }

    public function testSetPassword()
    {
        $this->_restClient->setPassword('PWD');

        $this->assertEquals('PWD', $this->_restClient->getPassword());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPasswordFausse()
    {
        $this->_restClient->setPassword(array());
    }

    public function testSetUrl()
    {
        $this->_restClient->setUrl('http://rest.server.com/');

        $this->assertEquals('http://rest.server.com/', $this->_restClient->getUrl());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUrlFausse()
    {
        $this->_restClient->setUrl('away');
    }

    public function testSetUsername()
    {
        $this->_restClient->setUsername('myUsername');

        $this->assertEquals('myUsername', $this->_restClient->getUsername());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUsernameTypeErrone()
    {
        $this->_restClient->setUsername(3);
    }
}
