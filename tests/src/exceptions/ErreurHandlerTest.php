<?php
namespace Tests\exceptions;

use AlaroxFramework\exceptions\ErreurHandler;

class ErreurHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ErreurHandler
     */
    private $_erreurHandler;

    /**
     * @var int
     */
    private $_errorReport;

    public function setUp()
    {
        $this->_erreurHandler = new ErreurHandler();
        $this->_errorReport = error_reporting();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\exceptions\\ErreurHandler', $this->_erreurHandler);
    }

    public function testErrorHandlerNoReport()
    {
        error_reporting(0);

        $this->assertFalse($this->_erreurHandler->errorHandler(2, 'message', 'fichier', 10));
    }

    /**
     * @expectedException \ErrorException
     */
    public function testErrorHandlerThrow()
    {
        error_reporting(E_ALL);

        $this->assertFalse($this->_erreurHandler->errorHandler(2, 'message', 'fichier', 10));
    }

    public function tearDown()
    {
        error_reporting($this->_errorReport);
    }
}
