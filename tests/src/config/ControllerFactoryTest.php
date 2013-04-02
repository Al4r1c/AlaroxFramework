<?php
namespace Tests\config;

use AlaroxFramework\cfg\ControllerFactory;

class ControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ControllerFactory
     */
    private $_ctrlFactory;

    public function setUp()
    {
        $this->_ctrlFactory = new ControllerFactory();
    }

    public function testSetCtrls()
    {
        $this->_ctrlFactory->setListControllers(array('Controller', 'Controller2'));

        $this->assertAttributeCount(2, '_listControllers', $this->_ctrlFactory);
        $this->assertAttributeContainsOnly('closure', '_listControllers', $this->_ctrlFactory);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCtrlsArray()
    {
        $this->_ctrlFactory->setListControllers(9);
    }

    public function testCall()
    {
        $this->_ctrlFactory->setListControllers(array('\\Tests\\fakecontrollers\\Index'));

        $this->assertInstanceOf(
            '\\Tests\\fakecontrollers\\Index',
            $this->_ctrlFactory->{'index'}($this->getMock('AlaroxFramework\traitement\restclient\RestClient'))
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testCallNotFound()
    {
        $this->_ctrlFactory->getBug();
    }
}