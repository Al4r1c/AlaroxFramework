<?php
namespace Tests\Config;

use AlaroxFramework\cfg\i18n\Langue;

class LangueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Langue
     */
    private $_langueDispo;

    public function setUp()
    {
        $this->_langueDispo = new Langue();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\i18n\\Langue', $this->_langueDispo);
    }

    public function testAlias()
    {
        $this->_langueDispo->setAlias('fr');

        $this->assertEquals('fr', $this->_langueDispo->getAlias());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAliasString()
    {
        $this->_langueDispo->setAlias(array());
    }

    public function testIdentifiant()
    {
        $this->_langueDispo->setIdentifiant('French');

        $this->assertEquals('French', $this->_langueDispo->getIdentifiant());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIdentifiantString()
    {
        $this->_langueDispo->setIdentifiant(array());
    }

    public function testNomFichier()
    {
        $this->_langueDispo->setNomFichier('fr_FR');

        $this->assertEquals('fr_FR', $this->_langueDispo->getNomFichier());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNomFichierString()
    {
        $this->_langueDispo->setNomFichier(array());
    }
}
