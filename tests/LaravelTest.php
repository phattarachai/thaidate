<?php

use Illuminate\Support\Carbon;

it('resolves the ->thaidate() macro in a short format', function () {
    expect(Carbon::parse('2021-02-25')->thaidate('D j M y'))->toBe('พฤ. 25 ก.พ. 64');
});

it('resolves the ->thaidate() macro in a long format', function () {
    expect(Carbon::parse('2021-02-25')->thaidate('l j F Y'))->toBe('พฤหัสบดี 25 กุมภาพันธ์ 2564');
});
