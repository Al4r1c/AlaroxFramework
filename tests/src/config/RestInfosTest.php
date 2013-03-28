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
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will(
                $this->returnValue(
                    array(
                        'Url' => 'http://Server.com',
                        'Format' => 'json',
                        'Username' => 'username',
                        'PassKey' => 'password'
                    )
                )
            );

        $this->_restInfos->setRestInfosDepuisFichier($fichier);

        $this->assertEquals('http://Server.com', $this->_restInfos->getUrl());
    }

    /**
     * @expectedException \Exception
     */
    public function testRestInfosDepuisFichierMissingKey()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(true));

        $fichier->expects($this->once())
            ->method('loadFile')
            ->will($this->returnValue(array()));

        $this->_restInfos->setRestInfosDepuisFichier($fichier);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetRouteMapDepuisFichierInexistant()
    {
        $fichier = $this->getMock('AlaroxFileManager\FileManager\File', array('fileExist', 'loadFile'));
        $fichier->expects($this->once())
            ->method('fileExist')
            ->will($this->returnValue(false));

        $this->_restInfos->setRestInfosDepuisFichier($fichier);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRouteMapTypErrone()
    {
        $this->_restInfos->setRestInfosDepuisFichier(5);
    }
}
