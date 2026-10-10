<?php

it('converts a short day/month/year format', function () {
    expect(thaidate('D j M y', strtotime('2021-02-25')))->toBe('พฤ. 25 ก.พ. 64');
});

it('converts a date in the 1900s', function () {
    expect(thaidate('D j M y', strtotime('1987-11-28')))->toBe('ส. 28 พ.ย. 30');
});

it('converts a date in the 2000s', function () {
    expect(thaidate('D j M y', strtotime('2021-11-28')))->toBe('อา. 28 พ.ย. 64');
});

it('converts a long format from a timestamp', function () {
    expect(thaidate('l j F Y', strtotime('2021-02-25')))->toBe('พฤหัสบดี 25 กุมภาพันธ์ 2564');
});

it('converts from a DateTime instance', function () {
    expect(thaidate('l j F Y', new DateTime('2021-02-25')))->toBe('พฤหัสบดี 25 กุมภาพันธ์ 2564');
});

it('keeps the Gregorian year when the Buddhist era is off', function () {
    expect(thaidate('l j F Y', strtotime('2021-02-25'), false))->toBe('พฤหัสบดี 25 กุมภาพันธ์ 2021');
});

it('accepts a date string', function () {
    expect(thaidate('j M Y', '2021-02-25'))->toBe('25 ก.พ. 2564');
});

it('formats the current time when given no timestamp', function () {
    expect(thaidate('j M Y'))->toBeString();
});

it('uses the default format', function () {
    expect(thaidate())->toBeString();
});

it('pads a Buddhist-era two-digit year with a leading zero', function () {
    expect(thaidate('y', '2057-06-01'))->toBe('00')
        ->and(thaidate('y', '2058-06-01'))->toBe('01');
});

it('formats a DateTime in its own timezone, not the default one', function () {
    date_default_timezone_set('UTC');

    $date = new DateTimeImmutable('2026-10-10 23:30', new DateTimeZone('Asia/Bangkok'));

    expect(thaidate('l j F Y H:i', $date))->toBe('เสาร์ 10 ตุลาคม 2569 23:30');
});

it('formats a timestamp in the default timezone', function () {
    date_default_timezone_set('Asia/Bangkok');

    $timestamp = (new DateTimeImmutable('2026-12-31 20:00', new DateTimeZone('UTC')))->getTimestamp();

    expect(thaidate('j F Y H:i', $timestamp))->toBe('1 มกราคม 2570 03:00');
});
