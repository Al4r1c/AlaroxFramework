<?php
namespace Tests\lib\compressor;

use AlaroxFramework\utils\compressor\CompressorFactory;

class CompressorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompressorFactory
     */
    private $_compressorFactory;

    public function setUp()
    {
        $this->_compressorFactory = new CompressorFactory();
    }

    /**
     * @expectedException \Exception
     */
    public function testToArrayInexistant()
    {
        $this->_compressorFactory->getCompressor('exception');
    }

    public function testGZip()
    {
        $this->assertInstanceOf(
            '\\AlaroxFramework\\utils\\compressor\\GZip',
            $this->_compressorFactory->getCompressor('gzip')
        );
    }

    public function testGZipCompress()
    {
        $gzipper = $this->_compressorFactory->getCompressor('gzip');

        $this->assertEquals(gzencode('data', 9), $gzipper->compress('data'));
    }
}
