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
     * @param  string  $format  Same tokens as PHP's date().
     * @param  bool  $buddhistEra  Convert the year to the Buddhist era.
     */
    public function format(string $format, DateTimeInterface $date, bool $buddhistEra): string
    {
        $format = $this->parseDay($format, $date);

        $format = $this->parseMonth($format, $date);

        $format = $this->parseYear($format, $date, $buddhistEra);

        return $date->format($format);
    }

    protected function parseDay(string $format, DateTimeInterface $date): string
    {
        if (str_contains($format, 'l')) {
            return str_replace('l', $this->longDays[$this->dayNum($date)], $format);
        }

        if (str_contains($format, 'D')) {
            return str_replace('D', $this->shortDays[$this->dayNum($date)], $format);
        }

        return $format;
    }

    protected function parseMonth(string $format, DateTimeInterface $date): string
    {
        if (str_contains($format, 'F')) {
            return str_replace('F', $this->longMonths[$this->monthNum($date)], $format);
        }

        if (str_contains($format, 'M')) {
            return str_replace('M', $this->shortMonths[$this->monthNum($date)], $format);
        }

        return $format;
    }

    private function parseYear(string $format, DateTimeInterface $date, bool $buddhistEra): string
    {
        if (! $buddhistEra) {
            return $format;
        }

        if (str_contains($format, 'o')) {
            return str_replace('o', (string) ((int) $date->format('o') + 543), $format);
        }

        if (str_contains($format, 'Y')) {
            return str_replace('Y', (string) ((int) $date->format('Y') + 543), $format);
        }

        if (str_contains($format, 'y')) {
            $year = ((int) $date->format('y') + 43) % 100;

            return str_replace('y', sprintf('%02d', $year), $format);
        }

        return $format;
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
