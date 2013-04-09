<?php
namespace AlaroxFramework\utils;

class Tools
{
    /**
     * @param int $codeHttp
     * @throws \InvalidArgumentException
     * @return bool
     */
    public static function isValideHttpCode($codeHttp)
    {
        if (!is_int($codeHttp)) {
            throw new \InvalidArgumentException('Parameter 1 codeHttp must be integer.');
        }

        return array_key_exists($codeHttp, include(__DIR__ . '/const/httpcode.php'));
    }

    /**
     * @param string $format
     * @return bool
     */
    public static function isValideFormat($format)
    {
        return array_key_exists(
            strtolower($format), array_change_key_case(include(__DIR__ . '/const/mimes.php'), CASE_LOWER)
        );
    }

    /**
     * @param string $mime
     * @return bool
     */
    public static function isValidMime($mime)
    {
        return in_array(strtolower($mime), array_map('strtolower', include(__DIR__ . '/const/mimes.php')));
    }

    /**
     * @param string $format
     * @return string|null
     * @codeCoverageIgnore
     */
    public static function getMimePourFormat($format)
    {
        if (self::isValideFormat($format)) {
            $tabFormats = include(__DIR__ . '/const/mimes.php');

            return $tabFormats[$format];
        } else {
            return null;
        }
    }
}