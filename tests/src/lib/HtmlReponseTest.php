<?php
namespace Tests\lib;

use AlaroxFramework\utils\HtmlReponse;

class HtmlReponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlReponse
     */
    private $_htmlReponse;

    public function setUp()
    {
        $this->_htmlReponse = new HtmlReponse();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\HtmlReponse', $this->_htmlReponse);
    }

    public function testStatus()
    {
        $this->_htmlReponse->setStatusHttp(500);

        $this->assertEquals(500, $this->_htmlReponse->getStatusHttp());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStatusErrone()
    {
        $this->_htmlReponse->setStatusHttp('exception');
    }

    /**
     * @expectedException \Exception
     */
    public function testStatusCodeErrone()
    {
        $this->_htmlReponse->setStatusHttp(999);
    }

    public function testSetHtml()
    {
        $this->_htmlReponse->setCorpsReponse('<html></html>');

        $this->assertEquals('<html></html>', $this->_htmlReponse->getCorpsReponse());
    }
}
