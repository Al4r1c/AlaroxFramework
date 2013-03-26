<?php
namespace Tests\Framework;

use AlaroxFramework\AlaroxFramework;

class AlaroxFrameworkTest extends \PHPUnit_Framework_TestCase
{
    /** @var AlaroxFramework */
    private $_framework;

    public function setUp()
    {
        $this->_framework = new AlaroxFramework();
    }

    public function testFirst()
    {
        $this->assertInstanceOf('\AlaroxFramework\AlaroxFramework', $this->_framework);
    }

    public function testSetConteneur()
    {
        $conteneur = $this->getMock('\AlaroxFramework\Conteneur');

        $this->_framework->setConteneur($conteneur);

        $this->assertAttributeEquals($conteneur, '_conteneur', $this->_framework);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConteneurTypeErrone()
    {
        $this->_framework->setConteneur('conteneur');
    }

    public function testSetConfigDepuisChemin()
    {
        $conteneur = $this->getMock('\AlaroxFramework\Conteneur', array('getConfig'));
        $conteneur->expects($this->once())
            ->method('getConfig')
            ->with('/path/to/fichier', '/path/to/routemap')
            ->will($this->returnValue($this->getMock('\AlaroxFramework\cfg\Config')));

        $this->_framework->setConteneur($conteneur);

        $this->_framework->genererConfigDepuisFichier('/path/to/fichier', '/path/to/routemap');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetConfigDepuisCheminTypeErrone()
    {
        $conteneur = $this->getMock('\AlaroxFramework\Conteneur', array('getConfig'));
        $conteneur->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue('Problem'));

        $this->_framework->setConteneur($conteneur);

        $this->_framework->genererConfigDepuisFichier('/path/to/fichier', '/path/to/routemap');
    }

    public function testProcess()
    {
        $this->assertNull($this->_framework->process());
    }
}
