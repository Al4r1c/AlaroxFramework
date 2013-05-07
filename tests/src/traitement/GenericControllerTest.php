<?php
namespace Tests\traitement;

use AlaroxFramework\traitement\controller\GenericController;

class GenericControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericController
     */
    private $_genericCtrl;

    public function setUp()
    {
        $this->_genericCtrl = $this->getMockForAbstractClass('AlaroxFramework\traitement\controller\GenericController');
    }

    public function testInstance()
    {
        $this->assertInstanceOf('AlaroxFramework\traitement\controller\GenericController', $this->_genericCtrl);
    }

    public function testRestClient()
    {
        $restClient = $this->getMock('AlaroxFramework\utils\restclient\RestClient');

        $this->_genericCtrl->setRestClient($restClient);

        $class = new \ReflectionClass('AlaroxFramework\traitement\controller\GenericController');
        $method = $class->getMethod('getRestClient');
        $method->setAccessible(true);

        $this->assertSame($restClient, $method->invoke($this->_genericCtrl));
    }

    public function testTabVariables()
    {
        $this->_genericCtrl->setVariablesRequete(array('var1' => 'val1'));

        $class = new \ReflectionClass('AlaroxFramework\traitement\controller\GenericController');
        $method = $class->getMethod('getVariablesRequete');
        $method->setAccessible(true);

        $this->assertEquals(array('var1' => 'val1'), $method->invoke($this->_genericCtrl));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTabVariablesArray()
    {
        $this->_genericCtrl->setVariablesRequete('exception');
    }

    public function testGetUneVariable()
    {
        $this->_genericCtrl->setVariablesRequete(array('paramKey' => 'maVar'));

        $class = new \ReflectionClass('AlaroxFramework\traitement\controller\GenericController');
        $method = $class->getMethod('getUneVariableRequete');
        $method->setAccessible(true);

        $this->assertEquals('maVar', $method->invokeArgs($this->_genericCtrl, array('paramKey')));
        $this->assertNull($method->invokeArgs($this->_genericCtrl, array('keyNotFound')));
    }
}
