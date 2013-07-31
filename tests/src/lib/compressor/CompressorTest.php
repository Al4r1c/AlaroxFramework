<?php
namespace Tests\lib\compressor;

use AlaroxFramework\utils\compressor\Compressor;

class CompressorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Compressor
     */
    private $_compressor;

    public function setUp()
    {
        $this->_compressor = new Compressor();
    }

    public function testSetCompressorFactory()
    {
        $unCompressorFactory = $this->getMock('\\AlaroxFramework\\utils\\compressor\\CompressorFactory');

        $this->_compressor->setCompressorFactory($unCompressorFactory);

        $this->assertAttributeSame($unCompressorFactory, '_compressorFactory', $this->_compressor);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCompressorFactoryTypeErrone()
    {
        $this->_compressor->setCompressorFactory(array());
    }

    public function testCompress()
    {
        $compressorFactory = $this->getMock('\\AlaroxFramework\\utils\\compressor\\CompressorFactory', array('getCompressor'));
        $abstractCompressor = $this->getMockForAbstractClass('\\AlaroxFramework\\utils\\compressor\\AbstractCompressor');

        $compressorFactory->expects($this->once())
            ->method('getCompressor')
            ->with('gzip')
            ->will($this->returnValue($abstractCompressor));

        $abstractCompressor->expects($this->once())
            ->method('compress')
            ->with('{parameter:var}')
            ->will($this->returnValue('compressedData:{parameter:var}'));

        $this->_compressor->setCompressorFactory($compressorFactory);

        $this->assertEquals('compressedData:{parameter:var}', $this->_compressor->compress('{parameter:var}', 'gzip'));
    }

    /**
     * @expectedException \Exception
     */
    public function testToArrayUncompressorFactoryNotSet()
    {
        $this->_compressor->compress(array('ok'), 'gzip');
    }
}
