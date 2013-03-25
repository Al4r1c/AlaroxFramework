<?php
namespace Framework\Tests;

use Framework\AlaroxFramework;
use Tests\TestCase;

class AlaroxFrameworkTest extends TestCase
{
    /** @var AlaroxFramework */
    private $_framework;

    public function setUp()
    {
        $this->_framework = new AlaroxFramework();
    }

    public function testFirst()
    {
        $this->assertInstanceOf('\Framework\AlaroxFramework', $this->_framework);
    }
}
