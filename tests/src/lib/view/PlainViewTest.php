<?php
namespace Tests\lib\view;

use AlaroxFramework\utils\view\PlainView;

class PlainViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PlainView
     */
    private $_view;

    public function setUp()
    {
        $this->_view = new PlainView();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\AlaroxFramework\utils\view\PlainView', $this->_view);
    }

    public function testSetViewName()
    {
        $this->_view->renderView('Some content');

        $this->assertEquals('Some content', $this->_view->getViewData());
    }
}
