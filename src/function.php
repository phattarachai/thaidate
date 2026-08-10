<?php

use Phattarachai\Thaidate\Thaidate;

/**
 * Thai date based on PHP's built-in date() function.
 *
 * @param  string  $format  Same tokens as PHP's date() — see https://php.net/manual/en/function.date.php
 * @param  string|int|DateTimeInterface  $timestamp  A Unix timestamp, a strtotime() string, or a DateTime.
 * @param  bool  $buddhistEra  Convert the year to the Buddhist era.
 */
function thaidate(string $format = 'j F Y', $timestamp = 'now', bool $buddhistEra = true): string
{
    if (is_string($timestamp)) {
        $timestamp = strtotime($timestamp);
    }

    if ($timestamp instanceof DateTimeInterface) {
        $timestamp = $timestamp->getTimestamp();
    }

    return (new Thaidate)->date($format, (int) $timestamp, $buddhistEra);
}
