<?php

namespace Phattarachai\Thaidate;

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
     * Format a Unix timestamp into a Thai date string.
     *
     * @param  string  $format  Same tokens as PHP's date().
     * @param  int  $timestamp  A Unix timestamp.
     * @param  bool  $buddhistEra  Convert the year to the Buddhist era.
     */
    public function date(string $format, int $timestamp, bool $buddhistEra): string
    {
        $format = $this->parseDay($format, $timestamp);

        $format = $this->parseMonth($format, $timestamp);

        $format = $this->parseYear($format, $timestamp, $buddhistEra);

        return date($format, $timestamp);
    }

    protected function parseDay(string $format, int $timestamp): string
    {
        if (str_contains($format, 'l')) {
            return str_replace('l', $this->longDays[$this->dayNum($timestamp)], $format);
        }

        if (str_contains($format, 'D')) {
            return str_replace('D', $this->shortDays[$this->dayNum($timestamp)], $format);
        }

        return $format;
    }

    protected function parseMonth(string $format, int $timestamp): string
    {
        if (str_contains($format, 'F')) {
            return str_replace('F', $this->longMonths[$this->monthNum($timestamp)], $format);
        }

        if (str_contains($format, 'M')) {
            return str_replace('M', $this->shortMonths[$this->monthNum($timestamp)], $format);
        }

        return $format;
    }

    private function parseYear(string $format, int $timestamp, bool $buddhistEra): string
    {
        if (! $buddhistEra) {
            return $format;
        }

        if (str_contains($format, 'o')) {
            return str_replace('o', (string) ((int) date('o', $timestamp) + 543), $format);
        }

        if (str_contains($format, 'Y')) {
            return str_replace('Y', (string) ((int) date('Y', $timestamp) + 543), $format);
        }

        if (str_contains($format, 'y')) {
            $year = ((int) date('y', $timestamp) + 43) % 100;

            return str_replace('y', (string) $year, $format);
        }

        return $format;
    }

    protected function monthNum(int $timestamp): int
    {
        return (int) date('n', $timestamp) - 1;
    }

    protected function dayNum(int $timestamp): int
    {
        return (int) date('w', $timestamp);
    }
}
