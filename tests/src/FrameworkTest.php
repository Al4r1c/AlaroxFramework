<?php
namespace Tests;

use Framework\Framework;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    /** @var Framework */
    private $_framework;

    public function setUp()
    {
        $this->_framework = new Framework();
    }

    public function testFirst() {
        $this->assertInstanceOf('\Framework\Framework', $this->_framework);
    }
}
