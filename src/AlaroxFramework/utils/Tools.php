<?php
namespace AlaroxFramework\Utils;

class Tools
{
    /**
     * @param int $codeHttp
     * @return bool
     */
    public static function isValideHttpCode($codeHttp)
    {
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