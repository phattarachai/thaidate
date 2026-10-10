<?php

namespace Phattarachai\Thaidate;

use Carbon\Exceptions\InvalidFormatException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class Thaidate
{
    /** @var list<string> Thai digits indexed by their value. */
    public const THAI_DIGITS = [
        '0' => '๐', '1' => '๑', '2' => '๒', '3' => '๓', '4' => '๔',
        '5' => '๕', '6' => '๖', '7' => '๗', '8' => '๘', '9' => '๙',
    ];

    /** @var list<string> */
    protected const ENGLISH_MONTHS = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /** @var list<string> */
    protected const ENGLISH_DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    /** @var list<string> */
    public array $longMonths = [
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
    public array $shortMonths = [
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
    public array $longDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];

    /** @var list<string> */
    public array $shortDays = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'];

    /**
     * Format a Unix timestamp into a Thai date string, in the default timezone.
     *
     * @param  string  $format  Same tokens as PHP's date().
     * @param  int  $timestamp  A Unix timestamp.
     * @param  bool  $buddhistEra  Convert the year to the Buddhist era.
     * @param  bool  $thaiDigits  Print the date's numbers in Thai digits (๐–๙).
     */
    public function date(string $format, int $timestamp, bool $buddhistEra, bool $thaiDigits = false): string
    {
        return $this->format($format, (new DateTimeImmutable)->setTimestamp($timestamp), $buddhistEra, $thaiDigits);
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
    public function format(string $format, DateTimeInterface $date, bool $buddhistEra, bool $thaiDigits = false): string
    {
        $output = '';
        $length = strlen($format);

        for ($i = 0; $i < $length; $i++) {
            if ($format[$i] === '\\') {
                $i++;
                $output .= $format[$i] ?? '';

                continue;
            }

            $value = $this->token($format[$i], $date, $buddhistEra);

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
    public function fiscalYear(DateTimeInterface $date, bool $buddhistEra = true): int
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
    public function parse(string $format, string $value, DateTimeZone|string|null $timezone = null): DateTimeImmutable
    {
        $timezone = match (true) {
            $timezone instanceof DateTimeZone => $timezone,
            is_string($timezone) => new DateTimeZone($timezone),
            default => new DateTimeZone(date_default_timezone_get()),
        };

        $tokens = $this->tokens($format);

        [$numericFormat, $numericValue] = $this->withoutNames($format, strtr($value, array_flip(self::THAI_DIGITS)));

        $parsed = date_parse_from_format($numericFormat, $numericValue);

        if ($parsed['error_count'] > 0) {
            throw new InvalidFormatException("Could not parse '{$value}' with the format '{$format}': ".implode(', ', $parsed['errors']));
        }

        if (in_array('The parsed time was invalid', $parsed['warnings'], true)) {
            throw new InvalidFormatException("'{$value}' is not a valid time.");
        }

        $timezone = $this->parsedTimezone($parsed) ?? $timezone;
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
    protected function tokens(string $format): array
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
    protected function withoutNames(string $format, string $value): array
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
                'F', 'M' => $this->namePattern([...$this->longMonths, ...$this->shortMonths, ...self::ENGLISH_MONTHS, ...$this->abbreviate(self::ENGLISH_MONTHS)]),
                'l', 'D' => $this->namePattern([...$this->longDays, ...$this->shortDays, ...self::ENGLISH_DAYS, ...$this->abbreviate(self::ENGLISH_DAYS)]),
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
            $value = substr_replace($value, (string) ($this->monthNumber($name) ?? ''), $offset, strlen($name));
        }

        return [$numericFormat, $value];
    }

    /**
     * @param  list<string>  $names
     */
    protected function namePattern(array $names): string
    {
        usort($names, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        return '('.implode('|', array_map(fn (string $name): string => preg_quote($name, '/'), $names)).')';
    }

    /**
     * The month number (1–12) a Thai or English month name stands for, or null for a day name.
     */
    protected function monthNumber(string $name): ?int
    {
        foreach ([$this->longMonths, $this->shortMonths, self::ENGLISH_MONTHS, $this->abbreviate(self::ENGLISH_MONTHS)] as $names) {
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
    protected function abbreviate(array $names): array
    {
        return array_map(fn (string $name): string => substr($name, 0, 3), $names);
    }

    /**
     * The timezone the value itself named (e, T, O or P), if any.
     *
     * @param  array<string, mixed>  $parsed
     */
    protected function parsedTimezone(array $parsed): ?DateTimeZone
    {
        $offset = (int) ($parsed['zone'] ?? 0);

        return match ($parsed['zone_type'] ?? null) {
            1 => new DateTimeZone(sprintf('%s%02d:%02d', $offset < 0 ? '-' : '+', intdiv(abs($offset), 3600), abs($offset) % 3600 / 60)),
            2 => new DateTimeZone((string) $parsed['tz_abbr']),
            3 => new DateTimeZone((string) $parsed['tz_id']),
            default => null,
        };
    }

    protected function token(string $token, DateTimeInterface $date, bool $buddhistEra): string
    {
        return match ($token) {
            'l' => $this->longDays[$this->dayNum($date)],
            'D' => $this->shortDays[$this->dayNum($date)],
            'F' => $this->longMonths[$this->monthNum($date)],
            'M' => $this->shortMonths[$this->monthNum($date)],
            'Y', 'o' => $buddhistEra ? (string) ((int) $date->format($token) + 543) : $date->format($token),
            'y' => $buddhistEra ? sprintf('%02d', ((int) $date->format('y') + 43) % 100) : $date->format('y'),
            default => $date->format($token),
        };
    }

    protected function monthNum(DateTimeInterface $date): int
    {
        return (int) $date->format('n') - 1;
    }

    protected function dayNum(DateTimeInterface $date): int
    {
        return (int) $date->format('w');
    }
}
