<?php
namespace AlaroxFramework\utils\compressor;

class GZip extends AbstractCompressor
{
    /**
     * @param string $data
     * @return string
     */
    public function compress($data)
    {
        return gzencode($data, 9);
    }
}