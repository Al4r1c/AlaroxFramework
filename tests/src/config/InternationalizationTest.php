<?php
namespace Tests\Config;

use AlaroxFramework\cfg\i18n\Internationalization;

class InternationalizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Internationalization
     */
    private $_i18nConfig;

    public function setUp()
    {
        $this->_i18nConfig = new Internationalization();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\i18n\\Internationalization', $this->_i18nConfig);
    }

    public function testSetActif()
    {
        $this->_i18nConfig->setActif(true);

        $this->assertTrue($this->_i18nConfig->isActivated());

        $this->_i18nConfig->setActif('off');

        $this->assertFalse($this->_i18nConfig->isActivated());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetActifBoolean()
    {
        $this->_i18nConfig->setActif('error');
    }

    public function testSetLangueDefaut()
    {
        $this->_i18nConfig->setLangueDefaut('French');

        $this->assertEquals('French', $this->_i18nConfig->getLangueDefaut());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetLangueDefautString()
    {
        $this->_i18nConfig->setLangueDefaut(array());
    }

    public function testAddLangueDispo()
    {
        $this->_i18nConfig->addLanguesDispo($uneLangue = $this->getMock('\\AlaroxFramework\\cfg\\i18n\\Langue'));
        $this->_i18nConfig->addLanguesDispo($this->getMock('\\AlaroxFramework\\cfg\\i18n\\Langue'));

        $this->assertAttributeContains($uneLangue, '_languesDispo', $this->_i18nConfig);
        $this->assertAttributeCount(2, '_languesDispo', $this->_i18nConfig);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddLangueDispoType()
    {
        $this->_i18nConfig->addLanguesDispo('exception');
    }

    public function testGetLanguesDispoByAlias()
    {
        $uneLangue = $this->getMock('\\AlaroxFramework\\cfg\\i18n\\Langue');
        $uneLangue->expects($this->once())->method('getAlias')->will($this->returnValue('fr'));

        $this->_i18nConfig->addLanguesDispo($uneLangue);

        $this->assertSame($uneLangue, $this->_i18nConfig->getLanguesDispoByAlias('fr'));
    }

    public function testGetLanguesDispoByAliasNonTrouve()
    {
        $this->assertFalse($this->_i18nConfig->getLanguesDispoByAlias('en'));
    }
}
