<?php
namespace Tests\lib;

use AlaroxFramework\utils\ObjetReponse;

class ObjetReponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjetReponse */
    private $_objetReponse;

    public function setUp()
    {
        $this->_objetReponse = new ObjetReponse();
    }

    public function testSetCodeHttp()
    {
        $this->_objetReponse->setStatusHttp(500);

        $this->assertEquals(500, $this->_objetReponse->getStatusHttp());
    }

    /**
     * @expectedException    \InvalidArgumentException
     */
    public function testCodeHttpNonInt()
    {
        $this->_objetReponse->setStatusHttp('PALA');
    }

    /**
     * @expectedException     \Exception
     */
    public function testCodeHttpInvalide()
    {
        $this->_objetReponse->setStatusHttp(999);
    }

    public function testSetContenu()
    {
        $this->_objetReponse->setDonneesReponse('OK');
        $this->assertEquals('OK', $this->_objetReponse->getDonneesReponse());
    }

    public function testSetContenuNull()
    {
        $this->_objetReponse->setDonneesReponse(null);
        $this->assertNull($this->_objetReponse->getDonneesReponse());
    }

    /**
     * @expectedException     \InvalidArgumentException
     */
    public function testContenuInvalide()
    {
        $this->_objetReponse->setDonneesReponse(array());
    }

    public function testFormat()
    {
        $this->_objetReponse->setFormatMime('application/json');

        $this->assertEquals('application/json', $this->_objetReponse->getFormatMime());
    }

    /**
     * @expectedException     \Exception
     */
    public function testFormatErrone()
    {
        $this->_objetReponse->setFormatMime('application/fake');
    }

    public function testToArray()
    {
        $this->_objetReponse->setDonneesReponse('{"id1":{"parametre1":"variable1"}}');
        $this->_objetReponse->setFormatMime('application/json');

        $this->assertEquals(array('id1' => array('parametre1' => 'variable1')), $this->_objetReponse->toArray());
    }
}