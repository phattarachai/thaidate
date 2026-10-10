<?php

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;

it('names the fiscal year after the year it ends in', function (string $date, int $fiscalYear) {
    expect(Carbon::parse($date)->thaiFiscalYear())->toBe($fiscalYear);
})->with([
    'last day of FY 2569' => ['2026-09-30 23:59:59', 2569],
    'first day of FY 2570' => ['2026-10-01 00:00:00', 2570],
    'January is in the FY that started the October before' => ['2027-01-15', 2570],
]);

it('returns the Gregorian fiscal year when the Buddhist era is off', function () {
    expect(Carbon::parse('2026-10-10')->thaiFiscalYear(false))->toBe(2027);
});

it('finds the start and end of the fiscal year', function () {
    $date = CarbonImmutable::parse('2027-03-15 10:00');

    expect($date->startOfThaiFiscalYear()->toDateTimeString())->toBe('2026-10-01 00:00:00')
        ->and($date->endOfThaiFiscalYear()->toDateTimeString())->toBe('2027-09-30 23:59:59')
        ->and(CarbonImmutable::parse('2026-10-10')->startOfThaiFiscalYear()->toDateString())->toBe('2026-10-01');
});
