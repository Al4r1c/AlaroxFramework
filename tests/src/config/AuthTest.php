<?php
namespace Tests\Config;

use AlaroxFramework\cfg\rest\Auth;

class AuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Auth
     */
    private $_auth;

    public function setUp()
    {
        $this->_auth = new Auth();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\rest\\Auth', $this->_auth);
    }
    
    public function testSetPassword()
    {
        $this->_auth->setPrivateKey('PWD');

        $this->assertEquals('PWD', $this->_auth->getPrivateKey());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPasswordFausse()
    {
        $this->_auth->setPrivateKey(array());
    }

    public function testSetUsername()
    {
        $this->_auth->setUsername('myUsername');

        $this->assertEquals('myUsername', $this->_auth->getUsername());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUsernameTypeErrone()
    {
        $this->_auth->setUsername(3);
    }


    public function testSetMethode()
    {
        $this->_auth->setAuthentifMethode('maMethode');

        $this->assertEquals('maMethode', $this->_auth->getAuthentifMethode());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetMethodeString()
    {
        $this->_auth->setAuthentifMethode(array());
    }
}
