<?php
namespace AlaroxFramework\utils\compressor;

class CompressorFactory
{
    /**
     * @param string $typeCompressor
     * @return AbstractCompressor
     * @throws \Exception
     */
    public function getCompressor($typeCompressor)
    {
        switch (strtolower($typeCompressor)) {
            case 'gzip':
                return new GZip();
                break;
            default:
                throw new \Exception(sprintf('Format "%s" not supported for compression.', $typeCompressor));
                break;
        }
    }
}