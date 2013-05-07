<?php
namespace Tests\config;

use AlaroxFramework\cfg\configs\ControllerFactory;

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

    public function testRestClient()
    {
        $restClient = $this->getMock('AlaroxFramework\utils\restclient\RestClient');

        $this->_ctrlFactory->setRestClient($restClient);

        $this->assertAttributeSame($restClient, '_restClient', $this->_ctrlFactory);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRestClientErrone()
    {
        $this->_ctrlFactory->setRestClient(9);
    }

    public function testCall()
    {
        $this->_ctrlFactory->setListControllers(array('\\Tests\\fakecontrollers\\TestCtrl'));
        $this->_ctrlFactory->setRestClient($this->getMock('AlaroxFramework\utils\restclient\RestClient'));

        $this->assertInstanceOf(
            '\\Tests\\fakecontrollers\\TestCtrl',
            $this->_ctrlFactory->{'testctrl'}(array())
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