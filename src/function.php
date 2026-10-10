<?php

use Carbon\Exceptions\InvalidFormatException;
use Phattarachai\Thaidate\Thaidate;

/**
 * Thai date based on PHP's built-in date() function.
 *
 * A DateTimeInterface keeps its own timezone; a timestamp or a date string uses
 * date_default_timezone_get(), the same as date().
 *
 * @param  string  $format  Same tokens as PHP's date() — see https://php.net/manual/en/function.date.php
 * @param  string|int|DateTimeInterface|null  $date  A Unix timestamp, a date string, a DateTime, or null for now.
 * @param  bool  $buddhistEra  Convert the year to the Buddhist era.
 * @param  bool  $thaiDigits  Print the date's numbers in Thai digits (๐–๙).
 *
 * @throws InvalidFormatException when $date is a string PHP can't read as a date.
 */
function thaidate(string $format = 'j F Y', string|int|DateTimeInterface|null $date = null, bool $buddhistEra = true, bool $thaiDigits = false): string
{
    return Thaidate::format($format, Thaidate::toDateTime($date), $buddhistEra, $thaiDigits);
}
