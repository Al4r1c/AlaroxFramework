<?php
namespace Tests\Controller;

use AlaroxFramework\Utils\ObjetReponse;

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
        $this->_objetReponse->setDonneesReponse(array('param' => 'variable', 'param2' => 'var2'));
        $this->assertCount(2, $this->_objetReponse->getDonneesReponse());
    }

    /**
     * @expectedException     \InvalidArgumentException
     */
    public function testContenuInvalide()
    {
        $this->_objetReponse->setDonneesReponse('INVALID');
    }

    public function testFormat()
    {
        $this->_objetReponse->setFormat('json');

        $this->assertEquals('json', $this->_objetReponse->getFormat());
    }

    /**
     * @expectedException     \Exception
     */
    public function testFormatErrone()
    {
        $this->_objetReponse->setFormat('fake');
    }
}