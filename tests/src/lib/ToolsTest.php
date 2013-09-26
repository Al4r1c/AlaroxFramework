<?php
namespace Tests\lib;

use AlaroxFramework\utils\tools\Tools;

class ToolsTest extends \PHPUnit_Framework_TestCase
{
    public function testIsValideHttpCode()
    {
        $this->assertTrue(Tools::isValideHttpCode(500));
        $this->assertFalse(Tools::isValideHttpCode(999));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsValideHttpCodeType()
    {
        Tools::isValideHttpCode('pala');
    }

    public function testIsValideFormat()
    {
        $this->assertFalse(Tools::isValideFormat('fake'));
        $this->assertTrue(Tools::isValideFormat('json'));
    }

    public function testIsValidMime()
    {
        $this->assertFalse(Tools::isValidMime('fake'));
        $this->assertTrue(Tools::isValidMime('application/xml'));
    }

    public function testGetMimePourFormat()
    {
        $this->assertEquals('application/json', Tools::getMimePourFormat('json'));
        $this->assertNull(Tools::getMimePourFormat('not_exist'));
    }

    public function testGetFormatPourMime()
    {
        $this->assertEquals('json', Tools::getFormatPourMime('application/json'));
        $this->assertNull(Tools::getFormatPourMime('not_exist'));
    }

    public function testGetCodeHttp() {
        $this->assertInternalType('array', Tools::getMessageHttpCode(500));
        $this->assertNull(Tools::getMessageHttpCode(99999));
    }
}
