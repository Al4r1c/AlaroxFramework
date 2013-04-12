<?php
namespace Tests\lib;

use AlaroxFramework\utils\ObjetRequete;

class ObjetRequeteTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjetRequete */
    private $_objetRequete;

    public function setUp()
    {
        $this->_objetRequete = new ObjetRequete();
    }

    public function testSetBody()
    {
        $this->_objetRequete->setBody(array('var1' => 'data1'));

        $this->assertEquals(array('var1' => 'data1'), $this->_objetRequete->getBody());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetBodyArray()
    {
        $this->_objetRequete->setBody('data');
    }

    public function testSetMethodeHttp()
    {
        $this->_objetRequete->setMethodeHttp('GET');

        $this->assertEquals('GET', $this->_objetRequete->getMethodeHttp());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetMethodeHttpErronee()
    {
        $this->_objetRequete->setMethodeHttp('NON');
    }

    public function testSetUri()
    {
        $this->_objetRequete->setUri('/mon/uri');

        $this->assertEquals('/mon/uri', $this->_objetRequete->getUri());
    }

    public function testSetUriNormalization()
    {
        $this->_objetRequete->setUri('mon/////uri//////');

        $this->assertEquals('/mon/uri', $this->_objetRequete->getUri());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUriString()
    {
        $this->_objetRequete->setUri(array());
    }
}
