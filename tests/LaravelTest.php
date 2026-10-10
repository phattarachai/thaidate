<?php

use Illuminate\Support\Carbon;

it('resolves the ->thaidate() macro in a short format', function () {
    expect(Carbon::parse('2021-02-25')->thaidate('D j M y'))->toBe('พฤ. 25 ก.พ. 64');
});

it('resolves the ->thaidate() macro in a long format', function () {
    expect(Carbon::parse('2021-02-25')->thaidate('l j F Y'))->toBe('พฤหัสบดี 25 กุมภาพันธ์ 2564');
});

it('formats in the Carbon instance timezone near midnight', function () {
    date_default_timezone_set('UTC');

    expect(Carbon::parse('2026-10-10 23:30', 'Asia/Bangkok')->thaidate('l j F Y H:i'))
        ->toBe('เสาร์ 10 ตุลาคม 2569 23:30');
});

it('formats a UTC Carbon in UTC when the default timezone is ahead', function () {
    date_default_timezone_set('Asia/Bangkok');

    expect(Carbon::parse('2026-12-31 20:00', 'UTC')->thaidate('D j M Y H:i'))
        ->toBe('พฤ. 31 ธ.ค. 2569 20:00');
});

it('keeps the Gregorian year when the macro turns the Buddhist era off', function () {
    expect(Carbon::parse('2021-02-25')->thaidate('j F Y', false))->toBe('25 กุมภาพันธ์ 2021');
});
