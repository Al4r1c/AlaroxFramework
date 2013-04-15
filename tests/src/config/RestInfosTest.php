<?php
namespace Tests\Config;

use AlaroxFramework\cfg\RestInfos;

class RestInfosTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RestInfos
     */
    private $_restInfos;

    public function setUp()
    {
        $this->_restInfos = new RestInfos();
    }

    public function testSetFormatEnvoi()
    {
        $this->_restInfos->setFormatEnvoi('json');

        $this->assertEquals('json', $this->_restInfos->getFormatEnvoi());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetFormatEnvoiErrone()
    {
        $this->_restInfos->setFormatEnvoi('getout');
    }

    public function testSetPassword()
    {
        $this->_restInfos->setPassword('PWD');

        $this->assertEquals('PWD', $this->_restInfos->getPassword());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPasswordFausse()
    {
        $this->_restInfos->setPassword(array());
    }

    public function testSetUrl()
    {
        $this->_restInfos->setUrl('http://rest.server.com/');

        $this->assertEquals('http://rest.server.com/', $this->_restInfos->getUrl());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUrlFausse()
    {
        $this->_restInfos->setUrl('away');
    }

    public function testSetUsername()
    {
        $this->_restInfos->setUsername('myUsername');

        $this->assertEquals('myUsername', $this->_restInfos->getUsername());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUsernameTypeErrone()
    {
        $this->_restInfos->setUsername(3);
    }

    public function testRestInfosDepuisFichier()
    {
        $this->_restInfos->parseRestInfos(
            array(
                'Url' => 'http://Server.com',
                'Format' => 'json',
                'Authentification' => array(
                    'Enabled' => true,
                    'Method' => 'method',
                    'Username' => 'username',
                    'PassKey' => 'password'
                )
            )
        );

        $this->assertEquals('http://Server.com', $this->_restInfos->getUrl());
    }

    /**
     * @expectedException \Exception
     */
    public function testRestInfosDepuisFichierMissingKey()
    {
        $this->_restInfos->parseRestInfos(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRouteMapTypeErrone()
    {
        $this->_restInfos->parseRestInfos(5);
    }

    public function testAuthentifEnabled()
    {
        $this->_restInfos->setAuthentifEnabled(true);

        $this->assertTrue($this->_restInfos->isAuthEnabled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAuthentifEnableddErrone()
    {
        $this->_restInfos->setAuthentifEnabled('exception');
    }

    public function testSetMethode() {
        $this->_restInfos->setAuthentifMethode('maMethode');

        $this->assertEquals('maMethode', $this->_restInfos->getAuthentifMethode());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetMethodeString() {
        $this->_restInfos->setAuthentifMethode(array());
    }
}

