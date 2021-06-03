<?php

use Phattarachai\Thaidate\Thaidate;


/**
 * Thai date based on php built-in date() function.
 *
 * @param string $format The format as same as PHP date function format. See http://php.net/manual/en/function.date.php
 * @param string|int $timestamp The optional timestamp is an integer Unix timestamp.
 * @param boolean $buddhistEra Use Buddhist era? set to true to use that or false not to use.
 * @return string Return the formatted date/time string.
 */
function thaidate(string $format = 'j F Y', $timestamp = 'now', bool $buddhistEra = true)
{
    if (is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
    }

    if ($timestamp instanceof DateTime) {
        $timestamp = $timestamp->getTimestamp();
    }

    return (new Thaidate)->date($format, $timestamp, $buddhistEra);
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);

    }
}
