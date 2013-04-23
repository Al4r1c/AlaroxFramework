<?php
/**
 * @param mixed $var
 * @return bool|null
 */
function getValidBoolean($var)
{
    if (is_bool($var) || !is_null($var = filter_var($var, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
        return $var;
    }

    return null;
}

/**
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

/**
 * @param string $haystack
 * @param string $needle
 * @return boolean
 */
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

/**
 * @param array $input
 * @param int $case
 * @return array
 */
function array_change_key_case_recursive(array $input, $case = null)
{
    $newArray = array();

    if (is_null($case)) {
        $case = CASE_LOWER;
    }

    foreach ($input as $key => $value) {
        if (is_array($value)) {
            $newArray[strtolower($key)] = array_change_key_case_recursive($value, $case);
        } else {
            $newArray[strtolower($key)] = $value;
        }
    }

    return $newArray;
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