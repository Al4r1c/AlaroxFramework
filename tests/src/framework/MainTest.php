<?php
namespace Tests\Framework;

use AlaroxFramework\Main;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var Main */
    private $_framework;

    public function setUp()
    {
        $this->_framework = new Main();
    }

    public function testFirst()
    {
        $this->assertInstanceOf('\AlaroxFramework\Main', $this->_framework);
    }
}
