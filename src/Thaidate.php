<?php

namespace Phattarachai\Thaidate;

class Thaidate
{

    public $longMonths = [
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
        'ธันวาคม'
    ];
    public $shortMonths = [
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
        'ธ.ค.'
    ];

    public $longDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
    public $shortDays = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'];


    /**
     * Thai date() function.
     *
     * @param string $format The format as same as PHP date function format. See http://php.net/manual/en/function.date.php
     * @param integer $timestamp The optional timestamp is an integer Unix timestamp.
     * @param boolean $buddhistEra Is Buddhist era or not.
     * @return string Return the formatted date/time string.
     */
    public function date(string $format, int $timestamp, bool $buddhistEra): string
    {
        $format = $this->parseDay($format, $timestamp);

        $format = $this->parseMonth($format, $timestamp);

        $format = $this->parseYear($format, $timestamp, $buddhistEra);


        return date($format, $timestamp);
    }

    /**
     * @param string $format
     * @param int $timestamp
     * @return string|string[]
     */
    protected function parseDay(string $format, int $timestamp)
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
        if (str_contains($format, 'F') === true) {
            return str_replace('F', $this->longMonths[$this->monthNum($timestamp)], $format);
        }

        if (str_contains($format, 'M') === true) {
            return str_replace('M', $this->shortMonths[$this->monthNum($timestamp)], $format);
        }

        return $format;
    }

    private function parseYear(string $format, $timestamp, bool $buddhistEra): string
    {
        if (!$buddhistEra) {
            return $format;
        }

        if (str_contains($format, 'o')) {
            return str_replace('o', (date('o', $timestamp) + 543), $format);
        }

        if (str_contains($format, 'Y')) {
            return str_replace('Y', (date('Y', $timestamp) + 543), $format);
        }

        if (str_contains($format, 'y')) {

            $year = (date('y', $timestamp) + 43) % 100;
            return str_replace('y', $year, $format);
        }

        return $format;
    }

    /**
     * @param int $timestamp
     * @return false|int|string
     */
    protected function monthNum(int $timestamp): int
    {
        return (date('n', $timestamp) - 1);
    }

    /**
     * @param int $timestamp
     * @return false|string
     */
    protected function dayNum(int $timestamp)
    {
        return date('w', $timestamp);
    }

}
