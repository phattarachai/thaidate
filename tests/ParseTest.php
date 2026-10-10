<?php

use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;

it('reads a Buddhist-era date', function () {
    expect(Carbon::createFromThaiFormat('d/m/Y', '10/10/2569')->toDateTimeString())->toBe('2026-10-10 00:00:00');
});

it('reads Thai month and day names, long or short', function (string $format, string $value) {
    expect(Carbon::createFromThaiFormat($format, $value)->toDateString())->toBe('2026-10-10');
})->with([
    ['j F Y', '10 ตุลาคม 2569'],
    ['j M y', '10 ต.ค. 69'],
    ['l j F Y', 'เสาร์ 10 ตุลาคม 2569'],
    ['D j M Y', 'ส. 10 ต.ค. 2569'],
    ['วันlที่ j F พ.ศ. Y', 'วันเสาร์ที่ 10 ตุลาคม พ.ศ. 2569'],
    ['D j M พ.ศ. Y', 'ส. 10 ต.ค. พ.ศ. 2569'],
    'English names still work' => ['D j M Y', 'Sat 10 Oct 2569'],
]);

it('reads Thai digits', function () {
    expect(Carbon::createFromThaiFormat('j F Y H:i', '๑๐ ตุลาคม ๒๕๖๙ ๐๙:๐๕')->toDateTimeString())->toBe('2026-10-10 09:05:00');
});

it('accepts 29 February in a Buddhist-era leap year', function () {
    expect(Carbon::createFromThaiFormat('d/m/Y', '29/02/2567')->toDateString())->toBe('2024-02-29');
});

it('rejects a date that does not exist', function () {
    Carbon::createFromThaiFormat('d/m/Y', '29/02/2568');
})->throws(InvalidFormatException::class);

it('rejects a month name it does not know', function () {
    Carbon::createFromThaiFormat('j F Y', '10 ตุลา 2569');
})->throws(InvalidFormatException::class);

it('rejects a value that does not match the format', function () {
    Carbon::createFromThaiFormat('d/m/Y', '10 ตุลาคม 2569');
})->throws(InvalidFormatException::class);

it('uses the timezone it is given', function () {
    $date = Carbon::createFromThaiFormat('d/m/Y H:i', '10/10/2569 23:30', 'Asia/Bangkok');

    expect($date->timezoneName)->toBe('Asia/Bangkok')
        ->and($date->copy()->utc()->toDateTimeString())->toBe('2026-10-10 16:30:00');
});

it('returns the class it is called on', function () {
    expect(CarbonImmutable::createFromThaiFormat('d/m/Y', '10/10/2569'))->toBeInstanceOf(CarbonImmutable::class)
        ->and(Carbon::createFromThaiFormat('d/m/Y', '10/10/2569'))->toBeInstanceOf(Carbon::class);
});

it('round-trips with thaidate()', function () {
    $date = Carbon::parse('2026-10-10 09:05');

    expect(Carbon::createFromThaiFormat('l j F Y H:i', $date->thaidate('l j F Y H:i'))->equalTo($date))->toBeTrue();
});

it('uses a timezone written in the value', function (string $value, string $offset) {
    expect(Carbon::createFromThaiFormat('d/m/Y H:i P', $value, 'UTC')->format('P'))->toBe($offset);
})->with([
    ['10/10/2569 09:00 +07:00', '+07:00'],
    ['10/10/2569 09:00 -00:30', '-00:30'],
]);

it('rejects a time that does not exist', function () {
    Carbon::createFromThaiFormat('d/m/Y H:i', '10/10/2569 25:00');
})->throws(InvalidFormatException::class);
