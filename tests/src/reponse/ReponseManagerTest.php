<?php
namespace Tests\reponse;

use AlaroxFramework\reponse\ReponseManager;

class ReponseManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReponseManager
     */
    private $_reponseManager;

    public function setUp()
    {
        $this->_reponseManager = new ReponseManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\reponse\\ReponseManager', $this->_reponseManager);
    }

    public function testSetTemplateManager()
    {
        $this->_reponseManager->setTemplateManager(
            $templateManager = $this->getMock('\\AlaroxFramework\\reponse\\TemplateManager')
        );

        $this->assertAttributeSame($templateManager, '_templateManager', $this->_reponseManager);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTemplateManagerType()
    {
        $this->_reponseManager->setTemplateManager(array());
    }

    public function testGetHtmlResponseWithView()
    {
        $view = $this->getMockForAbstractClass('\\AlaroxFramework\\utils\\view\\AbstractView');
        $templateManager = $this->getMock('\\AlaroxFramework\\reponse\\TemplateManager', array('render'));

        $templateManager
            ->expects($this->once())
            ->method('render')
            ->with($view)
            ->will($this->returnValue("<html></html>"));


        $this->_reponseManager->setTemplateManager($templateManager);

        $this->assertInstanceOf(
            '\\AlaroxFramework\\utils\\HtmlReponse',
            $htmlReponse = $this->_reponseManager->getHtmlResponse($view)
        );
        $this->assertEquals("<html></html>", $htmlReponse->getReponse());
        $this->assertEquals(200, $htmlReponse->getStatusHttp());
    }

    /**
     * @expectedException \Exception
     */
    public function testGetHtmlResponseWithViewNeedTemplateManager()
    {
        $view = $this->getMockForAbstractClass('\\AlaroxFramework\\utils\\view\\AbstractView');

        $this->_reponseManager->getHtmlResponse($view);
    }

    public function testGetHtmlResponseWithString()
    {
        $this->assertInstanceOf(
            '\\AlaroxFramework\\utils\\HtmlReponse',
            $htmlReponse = $this->_reponseManager->getHtmlResponse('myString')
        );
        $this->assertEquals("myString", $htmlReponse->getReponse());
        $this->assertEquals(200, $htmlReponse->getStatusHttp());
    }
}
