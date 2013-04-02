<?php
namespace Tests\traitement;

use AlaroxFramework\traitement\controller\ControllerFactory;

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
        $this->_ctrlFactory->setListControllers(array(__CLASS__));

        $class = explode('\\', __CLASS__);

        $this->assertInstanceOf(__CLASS__, $this->_ctrlFactory->{end($class)}());
    }

    /**
     * @expectedException \Exception
     */
    public function testCallNotFound()
    {
        $this->_ctrlFactory->getBug();
    }
}