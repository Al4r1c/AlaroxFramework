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
        $this->_ctrlFactory->setListControllers(array('Controller', 'Controller2'), array());

        $this->assertAttributeCount(2, '_listControllers', $this->_ctrlFactory);
        $this->assertAttributeContainsOnly('closure', '_listControllers', $this->_ctrlFactory);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCtrlsArray()
    {
        $this->_ctrlFactory->setListControllers(9, array());
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
        $this->_ctrlFactory->setListControllers(array('\\Tests\\fakecontrollers\\TestCtrl'), array('postVar' => 'value'));
        $this->_ctrlFactory->setRestClient($this->getMock('AlaroxFramework\utils\restclient\RestClient'));

        $this->assertInstanceOf(
            '\\Tests\\fakecontrollers\\TestCtrl',
            $testctrl = $this->_ctrlFactory->{'testctrl'}(array('some' => 'param'))
        );

        $class = new \ReflectionClass('\\Tests\\fakecontrollers\\TestCtrl');
        $methodVar = $class->getMethod('getVariablesRequete');
        $methodVar->setAccessible(true);
        $methodPost = $class->getMethod('getVariablesPost');
        $methodPost->setAccessible(true);

        $this->assertEquals(array('some' => 'param'), $methodVar->invoke($testctrl));
        $this->assertEquals(array('postVar' => 'value'), $methodPost->invoke($testctrl));
    }

    /**
     * @expectedException \Exception
     */
    public function testCallNotFound()
    {
        $this->_ctrlFactory->getBug();
    }
}