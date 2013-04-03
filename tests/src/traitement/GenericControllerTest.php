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
        $restClient = $this->getMock('AlaroxFramework\traitement\restclient\RestClient');

        $this->_genericCtrl->setRestClient($restClient);

        $class = new \ReflectionClass('AlaroxFramework\traitement\controller\GenericController');
        $method = $class->getMethod('getRestClient');
        $method->setAccessible(true);

        $this->assertSame($restClient, $method->invoke($this->_genericCtrl));
    }
}
