<?php
function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

/**
 * @param string $key
 * @param array $array
 * @param boolean $caseInsensitive
 * @return array|null
 */
function array_multisearch($key, $array, $caseInsensitive = false)
{
    if ($caseInsensitive === true) {
        $key = strtolower($key);
    }

    foreach (explode('.', $key) as $uneClef) {
        if (array_key_exists(
            $uneClef,
            ($caseInsensitive === false ? $array : $array = array_change_key_case($array, CASE_LOWER))
        )
        ) {
            $array = $array[$uneClef];
        } else {
            return null;
        }
    }

    return $array;
}