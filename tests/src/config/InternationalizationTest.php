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
        $this->_i18nConfig->setLangueDefaut($uneLangue = $this->getMock('\\AlaroxFramework\\cfg\\i18n\\Langue'));

        $this->assertSame($uneLangue, $this->_i18nConfig->getLangueDefaut());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetLangueDefautType()
    {
        $this->_i18nConfig->setLangueDefaut(array());
    }

    public function testAddLangueDispo()
    {
        $this->_i18nConfig->addLanguesDispo($uneLangue = $this->getMock('\\AlaroxFramework\\cfg\\i18n\\Langue'));
        $this->_i18nConfig->addLanguesDispo($this->getMock('\\AlaroxFramework\\cfg\\i18n\\Langue'));

        $this->assertContains($uneLangue, $this->_i18nConfig->getLanguesDispo());
        $this->assertCount(2, $this->_i18nConfig->getLanguesDispo());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddLangueDispoType()
    {
        $this->_i18nConfig->addLanguesDispo('exception');
    }


    public function testGetLanguesDispoById()
    {
        $uneLangue = $this->getMock('\\AlaroxFramework\\cfg\\i18n\\Langue');
        $uneLangue->expects($this->once())->method('getIdentifiant')->will($this->returnValue('French'));

        $this->_i18nConfig->addLanguesDispo($uneLangue);

        $this->assertSame($uneLangue, $this->_i18nConfig->getLanguesDispoById('French'));
    }

    public function testGetLanguesDispoByAliasNonId()
    {
        $this->assertFalse($this->_i18nConfig->getLanguesDispoById('Italiano'));
    }

    public function testDossier()
    {
        $this->_i18nConfig->setDossierLocales('/path');

        $this->assertEquals('/path', $this->_i18nConfig->getDossierLocales());
    }
}
