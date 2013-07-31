<?php
namespace AlaroxFramework\utils\compressor;

abstract class AbstractCompressor
{
    /**
     * @param string $data
     * @return string
     */
    abstract public function compress($data);
}