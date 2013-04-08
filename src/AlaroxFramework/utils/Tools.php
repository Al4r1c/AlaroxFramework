<?php
namespace AlaroxFramework\utils;

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


    /**
     * @param string $mime
     * @return bool
     */
    public static function isValidMime($mime)
    {
        return in_array(
            strtolower($mime),
            array_map('strtolower', include(__DIR__ . '/const/mimes.php'))
        );
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

    /**
     * @param string $mime
     * @return string|null
     */
    public static function getFormat($mime)
    {
        if (self::isValidMime($mime)) {
            $tabFormats = include(__DIR__ . '/const/mimes.php');

            return array_search($mime, $tabFormats);
        } else {
            return null;
        }
    }
}