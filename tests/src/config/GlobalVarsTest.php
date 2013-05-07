<?php
namespace Tests\Config;

use AlaroxFramework\cfg\globals\GlobalVars;

class GlobalVarsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GlobalVars
     */
    private $_globalVars;

    public function setUp()
    {
        $this->_globalVars = new GlobalVars();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\cfg\\globals\\GlobalVars', $this->_globalVars);
    }

    public function testAddGlobalVar()
    {
        $this->_globalVars->addStaticVar('clef', 'value');

        $this->assertCount(1, $this->_globalVars->getStaticVars());
    }

    public function testSetRemoteVars()
    {
        $this->_globalVars->setRemoteVars($this->getMock('\\AlaroxFramework\\cfg\\globals\\RemoteVars'));

        $this->assertAttributeInstanceOf(
            '\\AlaroxFramework\\cfg\\globals\\RemoteVars',
            '_remoteVars',
            $this->_globalVars
        );
    }

    public function testGetRemoteVarsExecutees()
    {
        $remoteVars = $this->getMock('\\AlaroxFramework\\cfg\\globals\\RemoteVars', array('getRemoteVarsExecutees'));

        $remoteVars->expects($this->once())
            ->method('getRemoteVarsExecutees')
            ->will($this->returnValue(array('val')));

        $this->_globalVars->setRemoteVars($remoteVars);

        $this->assertEquals(array('val'), $this->_globalVars->getRemoteVarsExecutees());
    }
}
