<?php
namespace Tests\traitement;

use AlaroxFramework\traitement\ControllerExecutor;
use Tests\fakecontrollers\TestCtrl;
use Tests\fakecontrollers\TestCtrlBeforeAction;

class ControllerExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ControllerExecutor
     */
    private $_controllerExecutor;

    public function setUp()
    {
        $this->_controllerExecutor = new ControllerExecutor();
    }

    public function testSetControllerFactory()
    {
        $this->_controllerExecutor->setControllerFactory(
            $ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory')
        );

        $this->assertAttributeSame($ctrlFactory, '_controllerFactory', $this->_controllerExecutor);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCtrlFactoErrone()
    {
        $this->_controllerExecutor->setControllerFactory('bahnan');
    }

    public function testExecuterController()
    {
        $ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory', array('__call'));

        $ctrlFactory->expects($this->once())
        ->method('__call')
        ->with('testctrl', array(array()))
        ->will($this->returnValue(new TestCtrl()));

        $this->_controllerExecutor->setControllerFactory($ctrlFactory);

        $this->_controllerExecutor->executerControleur('testctrl', 'indexAction', array());
    }

    public function testExecuterControllerAvecBeforeAction()
    {
        $ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory', array('__call'));

        $ctrlFactory->expects($this->once())
        ->method('__call')
        ->with('TestCtrlBeforeAction', array(array()))
        ->will($this->returnValue(new TestCtrlBeforeAction()));

        $this->_controllerExecutor->setControllerFactory($ctrlFactory);

        $this->_controllerExecutor->executerControleur('TestCtrlBeforeAction', 'indexAction', array());
    }

    /**
     * @expectedException \AlaroxFramework\traitement\NotFoundException
     */
    public function testExecuterUriVideRouteNonTrouvee()
    {
        $ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory', array('__call'));

        $ctrlFactory->expects($this->once())
        ->method('__call')
        ->with('notgoodctrl', array(array()))
        ->will($this->throwException(new \Exception()));

        $this->_controllerExecutor->setControllerFactory($ctrlFactory);

        $this->_controllerExecutor->executerControleur('notgoodctrl', 'indexAction', array());
    }

    /**
     * @expectedException \AlaroxFramework\traitement\NotFoundException
     */
    public function testActionMethodeInexistante()
    {
        $ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory', array('__call'));

        $ctrlFactory->expects($this->once())
        ->method('__call')
        ->with('testctrl', array(array()))
        ->will($this->returnValue(new TestCtrl()));

        $this->_controllerExecutor->setControllerFactory($ctrlFactory);

        $this->_controllerExecutor->executerControleur('testctrl', 'dontexist', array());
    }

    /**
     * @expectedException \AlaroxFramework\traitement\NotFoundException
     */
    public function testActionMethodePrivee()
    {
        $ctrlFactory = $this->getMock('AlaroxFramework\cfg\configs\ControllerFactory', array('__call'));

        $ctrlFactory->expects($this->once())
        ->method('__call')
        ->with('testctrl', array(array()))
        ->will($this->returnValue(new TestCtrl()));

        $this->_controllerExecutor->setControllerFactory($ctrlFactory);

        $this->_controllerExecutor->executerControleur('testctrl', 'privatemethod', array());
    }
}
