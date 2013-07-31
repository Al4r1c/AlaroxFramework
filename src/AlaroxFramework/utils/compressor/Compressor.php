<?php
namespace AlaroxFramework\utils\compressor;

class Compressor
{
    /**
     * @var CompressorFactory
     */
    private $_compressorFactory;

    /**
     * @param CompressorFactory $compressorFactory
     * @throws \InvalidArgumentException
     */
    public function setCompressorFactory($compressorFactory)
    {
        if (!$compressorFactory instanceof CompressorFactory) {
            throw new \InvalidArgumentException(
                'Expected parameter 1 $compressorFactory to be instance of CompressorFactory.'
            );
        }

        $this->_compressorFactory = $compressorFactory;
    }

    /**
     * @param string $data
     * @param string $format
     * @throws \Exception
     * @return string
     */
    public function compress($data, $format)
    {
        if (!$this->_compressorFactory instanceof CompressorFactory) {
            throw new \Exception('Compressor factory is not set.');
        }

        $classCompressor = $this->_compressorFactory->getCompressor($format);

        return $classCompressor->compress($data);
    }
}