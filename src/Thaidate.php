<?php

namespace Phattarachai\Thaidate;

use Carbon\Exceptions\InvalidFormatException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

/**
 * Formats, parses and reads the fiscal year of Thai dates. Every method is static and pure.
 */
final class Thaidate
{
    /** @var list<string> Thai digits indexed by their value. */
    public const THAI_DIGITS = [
        '0' => '๐', '1' => '๑', '2' => '๒', '3' => '๓', '4' => '๔',
        '5' => '๕', '6' => '๖', '7' => '๗', '8' => '๘', '9' => '๙',
    ];

    /** @var list<string> */
    private const ENGLISH_MONTHS = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /** @var list<string> */
    private const ENGLISH_DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    /** @var list<string> */
    public const LONG_MONTHS = [
        'มกราคม',
        'กุมภาพันธ์',
        'มีนาคม',
        'เมษายน',
        'พฤษภาคม',
        'มิถุนายน',
        'กรกฎาคม',
        'สิงหาคม',
        'กันยายน',
        'ตุลาคม',
        'พฤศจิกายน',
        'ธันวาคม',
    ];

    /** @var list<string> */
    public const SHORT_MONTHS = [
        'ม.ค.',
        'ก.พ.',
        'มี.ค.',
        'เม.ย.',
        'พ.ค.',
        'มิ.ย.',
        'ก.ค.',
        'ส.ค.',
        'ก.ย.',
        'ต.ค.',
        'พ.ย.',
        'ธ.ค.',
    ];

    /** @var list<string> */
    public const LONG_DAYS = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];

    /** @var list<string> */
    public const SHORT_DAYS = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'];

    /**
     * Turn what thaidate() accepts into a DateTime: null is now, a timestamp and a date string
     * use the default timezone, and a DateTime is kept as it is.
     *
     * @throws InvalidFormatException when the string isn't a date PHP can read.
     */
    public static function toDateTime(string|int|DateTimeInterface|null $date): DateTimeInterface
    {
        try {
            return match (true) {
                $date instanceof DateTimeInterface => $date,
                is_int($date) => (new DateTimeImmutable)->setTimestamp($date),
                default => new DateTimeImmutable($date ?? 'now'),
            };
        } catch (Exception $exception) {
            throw new InvalidFormatException("'{$date}' is not a date PHP can read.", previous: $exception);
        }
    }

    /**
     * Format a DateTime into a Thai date string, in that DateTime's own timezone.
     *
     * The format is read one token at a time, so a backslash escapes the next character
     * and every token is translated, however many the format holds.
     *
     * @param  string  $format  Same tokens as PHP's date().
     * @param  bool  $buddhistEra  Convert the year to the Buddhist era.
     * @param  bool  $thaiDigits  Print the date's numbers in Thai digits (๐–๙). Literal text in the format is left as written.
     */
    public static function format(string $format, DateTimeInterface $date, bool $buddhistEra, bool $thaiDigits = false): string
    {
        $output = '';
        $length = strlen($format);

        for ($i = 0; $i < $length; $i++) {
            if ($format[$i] === '\\') {
                $i++;
                $output .= $format[$i] ?? '';

                continue;
            }

            $value = self::token($format[$i], $date, $buddhistEra);

            $output .= $thaiDigits ? strtr($value, self::THAI_DIGITS) : $value;
        }

        return $output;
    }

    /**
     * The Thai government fiscal year (ปีงบประมาณ) a date falls in: 1 October to 30 September,
     * named after the year it ends in.
     *
     * @param  bool  $buddhistEra  Return the Buddhist-era year (2570) instead of the Gregorian one (2027).
     */
    public static function fiscalYear(DateTimeInterface $date, bool $buddhistEra = true): int
    {
        $year = (int) $date->format('Y') + ((int) $date->format('n') >= 10 ? 1 : 0);

        return $buddhistEra ? $year + 543 : $year;
    }

    /**
     * Parse a Thai date string, the reverse of format().
     *
     * `Y` reads a Buddhist-era year (2569) and `y` a two-digit one (69, read as 25xx). `F`, `M`,
     * `l` and `D` read Thai month and day names, long or short, and Thai digits are accepted
     * anywhere. Every other token works as in DateTime::createFromFormat(). Fields the format
     * doesn't hold take today's date and midnight.
     *
     * @throws InvalidFormatException when the value doesn't match the format or isn't a real date.
     */
    public static function parse(string $format, string $value, DateTimeZone|string|null $timezone = null): DateTimeImmutable
    {
        $timezone = match (true) {
            $timezone instanceof DateTimeZone => $timezone,
            is_string($timezone) => new DateTimeZone($timezone),
            default => new DateTimeZone(date_default_timezone_get()),
        };

        $tokens = self::tokens($format);

        [$numericFormat, $numericValue] = self::withoutNames($format, strtr($value, array_flip(self::THAI_DIGITS)));

        $parsed = date_parse_from_format($numericFormat, $numericValue);

        if ($parsed['error_count'] > 0) {
            throw new InvalidFormatException("Could not parse '{$value}' with the format '{$format}': ".implode(', ', $parsed['errors']));
        }

        if (in_array('The parsed time was invalid', $parsed['warnings'], true)) {
            throw new InvalidFormatException("'{$value}' is not a valid time.");
        }

        $timezone = self::parsedTimezone($parsed) ?? $timezone;
        $today = new DateTimeImmutable('now', $timezone);

        $year = match (true) {
            $parsed['year'] === false => (int) $today->format('Y'),
            in_array('Y', $tokens, true) => $parsed['year'] - 543,
            in_array('y', $tokens, true) => 2500 + $parsed['year'] % 100 - 543,
            default => $parsed['year'],
        };
        $month = $parsed['month'] === false ? (int) $today->format('n') : $parsed['month'];
        $day = $parsed['day'] === false ? (int) $today->format('j') : $parsed['day'];

        if (! checkdate($month, $day, $year)) {
            throw new InvalidFormatException("'{$value}' is not a valid date.");
        }

        return $today
            ->setDate($year, $month, $day)
            ->setTime(
                (int) $parsed['hour'],
                (int) $parsed['minute'],
                (int) $parsed['second'],
                (int) round((float) $parsed['fraction'] * 1_000_000),
            );
    }

    /**
     * The unescaped tokens in a format.
     *
     * @return list<string>
     */
    private static function tokens(string $format): array
    {
        $tokens = [];
        $length = strlen($format);

        for ($i = 0; $i < $length; $i++) {
            if ($format[$i] === '\\') {
                $i++;

                continue;
            }

            $tokens[] = $format[$i];
        }

        return $tokens;
    }

    /**
     * Rewrite a value and its format so date_parse_from_format() never reads a name.
     *
     * The value is matched against a pattern built from the format, so only the text that sits
     * where an F, M, l or D token is counts as a name; Thai text written into the format, such
     * as พ.ศ. or วันlที่, never does. A month name becomes its number (and the token `n`); a day
     * name is dropped, since the date already says which day it is.
     *
     * @return array{string, string} The format and the value.
     */
    private static function withoutNames(string $format, string $value): array
    {
        $pattern = '';
        $numericFormat = '';
        $length = strlen($format);

        for ($i = 0; $i < $length; $i++) {
            $token = $format[$i];

            if ($token === '\\') {
                $i++;
                $pattern .= preg_quote($format[$i] ?? '', '/');
                $numericFormat .= '\\'.($format[$i] ?? '');

                continue;
            }

            $pattern .= match ($token) {
                'F', 'M' => self::namePattern([...self::LONG_MONTHS, ...self::SHORT_MONTHS, ...self::ENGLISH_MONTHS, ...self::abbreviate(self::ENGLISH_MONTHS)]),
                'l', 'D' => self::namePattern([...self::LONG_DAYS, ...self::SHORT_DAYS, ...self::ENGLISH_DAYS, ...self::abbreviate(self::ENGLISH_DAYS)]),
                '#' => '[;:\/.,\-()]',
                '?' => '.',
                '*', '+' => '.*?',
                '!', '|' => '',
                default => preg_match('/[A-Za-z]/', $token) === 1 ? '.*?' : preg_quote($token, '/'),
            };

            $numericFormat .= match ($token) {
                'F', 'M' => 'n',
                'l', 'D' => '',
                default => $token,
            };
        }

        if (preg_match('/^'.$pattern.'$/ui', $value, $matches, PREG_OFFSET_CAPTURE) !== 1) {
            return [$format, $value];
        }

        foreach (array_reverse(array_slice($matches, 1)) as [$name, $offset]) {
            $value = substr_replace($value, (string) (self::monthNumber($name) ?? ''), $offset, strlen($name));
        }

        return [$numericFormat, $value];
    }

    /**
     * @param  list<string>  $names
     */
    private static function namePattern(array $names): string
    {
        usort($names, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return '('.implode('|', array_map(fn (string $name): string => preg_quote($name, '/'), $names)).')';
    }

    /**
     * The month number (1–12) a Thai or English month name stands for, or null for a day name.
     */
    private static function monthNumber(string $name): ?int
    {
        foreach ([self::LONG_MONTHS, self::SHORT_MONTHS, self::ENGLISH_MONTHS, self::abbreviate(self::ENGLISH_MONTHS)] as $names) {
            $index = array_search(strtolower($name), array_map(strtolower(...), $names), true);

            if ($index !== false) {
                return $index + 1;
            }
        }

        return null;
    }

    /**
     * @param  list<string>  $names
     * @return list<string>
     */
    private static function abbreviate(array $names): array
    {
        return array_map(static fn (string $name): string => substr($name, 0, 3), $names);
    }

    /**
     * The timezone the value itself named (e, T, O or P), if any.
     *
     * @param  array<string, mixed>  $parsed
     */
    private static function parsedTimezone(array $parsed): ?DateTimeZone
    {
        $offset = (int) ($parsed['zone'] ?? 0);

        return match ($parsed['zone_type'] ?? null) {
            1 => new DateTimeZone(sprintf('%s%02d:%02d', $offset < 0 ? '-' : '+', intdiv(abs($offset), 3600), abs($offset) % 3600 / 60)),
            2 => new DateTimeZone((string) $parsed['tz_abbr']),
            3 => new DateTimeZone((string) $parsed['tz_id']),
            default => null,
        };
    }

    private static function token(string $token, DateTimeInterface $date, bool $buddhistEra): string
    {
        return match ($token) {
            'l' => self::LONG_DAYS[self::dayNum($date)],
            'D' => self::SHORT_DAYS[self::dayNum($date)],
            'F' => self::LONG_MONTHS[self::monthNum($date)],
            'M' => self::SHORT_MONTHS[self::monthNum($date)],
            'Y', 'o' => $buddhistEra ? (string) ((int) $date->format($token) + 543) : $date->format($token),
            'y' => $buddhistEra ? sprintf('%02d', ((int) $date->format('y') + 43) % 100) : $date->format('y'),
            default => $date->format($token),
        };
    }

    private static function monthNum(DateTimeInterface $date): int
    {
        return (int) $date->format('n') - 1;
    }

    private static function dayNum(DateTimeInterface $date): int
    {
        return (int) $date->format('w');
    }
}
