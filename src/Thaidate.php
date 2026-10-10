<?php

namespace Phattarachai\Thaidate;

use DateTimeImmutable;
use DateTimeInterface;

class Thaidate
{
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
     */
    public function date(string $format, int $timestamp, bool $buddhistEra): string
    {
        return $this->format($format, (new DateTimeImmutable)->setTimestamp($timestamp), $buddhistEra);
    }

    /**
     * Format a DateTime into a Thai date string, in that DateTime's own timezone.
     *
     * The format is read one token at a time, so a backslash escapes the next character
     * and every token is translated, however many the format holds.
     *
     * @param  string  $format  Same tokens as PHP's date().
     * @param  bool  $buddhistEra  Convert the year to the Buddhist era.
     */
    public function format(string $format, DateTimeInterface $date, bool $buddhistEra): string
    {
        $output = '';
        $length = strlen($format);

        for ($i = 0; $i < $length; $i++) {
            if ($format[$i] === '\\') {
                $i++;
                $output .= $format[$i] ?? '';

                continue;
            }

            $output .= $this->token($format[$i], $date, $buddhistEra);
        }

        return $output;
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
