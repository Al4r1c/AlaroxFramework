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
            strtolower($format),
            array_change_key_case(include(__DIR__ . '/const/mimes.php'), CASE_LOWER)
        );
    }

    /**
     * @param string $formatMimeRecherchee
     * @return bool
     */
    public static function isValidMime($formatMimeRecherchee)
    {
        foreach (include(__DIR__ . '/const/mimes.php') as $formatsMime) {
            if (in_array($formatMimeRecherchee, $formatsMime)) {
                return true;
            }
        }

        return false;
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

            return current($tabFormats[$format]);
        } else {
            return null;
        }
    }

    public static function getFormatPourMime($formatMimeRecherchee)
    {
        if (self::isValidMime($formatMimeRecherchee)) {
            $found = false;

            foreach (include(__DIR__ . '/const/mimes.php') as $type => $formatsMime) {
                if (in_array($formatMimeRecherchee, $formatsMime)) {
                    $found = $type;
                }
            }

            return $found;
        } else {
            return null;
        }
    }
}