# Thaidate

[![Latest Version on Packagist](https://img.shields.io/packagist/v/phattarachai/thaidate.svg?style=flat-square)](https://packagist.org/packages/phattarachai/thaidate)
[![Tests](https://img.shields.io/github/actions/workflow/status/phattarachai/thaidate/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/phattarachai/thaidate/actions/workflows/run-tests.yml?query=branch%3Amain)
[![Code Style](https://img.shields.io/github/actions/workflow/status/phattarachai/thaidate/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/phattarachai/thaidate/actions/workflows/fix-php-code-style-issues.yml?query=branch%3Amain)
[![PHP Version](https://img.shields.io/packagist/dependency-v/phattarachai/thaidate/php?style=flat-square&label=php&logo=php&logoColor=white)](https://packagist.org/packages/phattarachai/thaidate)
![Laravel Version](https://img.shields.io/badge/laravel-12%20%7C%2013-FF2D20?style=flat-square&logo=laravel&logoColor=white)
[![Total Downloads](https://img.shields.io/packagist/dt/phattarachai/thaidate.svg?style=flat-square)](https://packagist.org/packages/phattarachai/thaidate)

Thai dates for PHP and Laravel: a `thaidate()` helper and Carbon macros that print Buddhist-era years
(พ.ศ.) and Thai month and day names, read them back from user input, and work out the Thai government
fiscal year.

- [Installation](#installation)
- [thaidate()](#thaidate)
- [Carbon macros](#carbon-macros)
- [Thai digits](#thai-digits)
- [Fiscal year (ปีงบประมาณ)](#fiscal-year)
- [Reading Thai dates from input](#reading-thai-dates)
- [Timezone](#timezone)
- [Working with Carbon and JavaScript](#carbon-and-javascript)
- [Static analysis](#static-analysis)

## <a id="installation"></a> Installation

```bash
composer require phattarachai/thaidate
```

Laravel discovers the service provider, which registers the Carbon macros. The `thaidate()` helper works
in any PHP project.

## <a id="thaidate"></a> thaidate()

`thaidate()` takes the same format tokens as PHP's [date()](https://www.php.net/manual/en/datetime.format.php).
`l`, `D`, `F` and `M` print Thai day and month names, and `Y`, `y` and `o` print the Buddhist-era year.
Everything else, Thai text included, prints as written.

```php
echo thaidate();
// 25 กุมภาพันธ์ 2564

echo thaidate('วันlที่ j F พ.ศ. Y เวลา H:i น.');
// วันพฤหัสบดีที่ 25 กุมภาพันธ์ พ.ศ. 2564 เวลา 23:55 น.

echo thaidate('D j M y');
// พฤ. 25 ก.พ. 64
```

Escape a token with a backslash to print it as a letter: `thaidate('\Y\e\a\r Y')` prints `Year 2564`.

### Parameters

```php
thaidate(
    string $format = 'j F Y',
    string|int|DateTimeInterface|null $date = null,
    bool $buddhistEra = true,
    bool $thaiDigits = false,
): string
```

| Parameter | Description |
| --- | --- |
| `$format` | The same format tokens as PHP's `date()`. |
| `$date` | The date to format: a Unix timestamp, a date string PHP understands (`'2021-02-25'`, `'tomorrow'`), any `DateTimeInterface` (`DateTime`, `Carbon`, …), or `null` for now. A string PHP can't read throws `InvalidFormatException`. |
| `$buddhistEra` | `true` (default) prints `Y`, `y` and `o` as Buddhist-era years (พ.ศ.). Pass `false` to keep the Gregorian year (ค.ศ.). |
| `$thaiDigits` | `true` prints the date's numbers in Thai digits (๐–๙). See [Thai digits](#thai-digits). |

```php
echo thaidate('j F Y', '2021-02-25');
// 25 กุมภาพันธ์ 2564

echo thaidate('j F Y', 1614211200);
// 25 กุมภาพันธ์ 2564

echo thaidate('j F Y', new DateTime('2021-02-25'));
// 25 กุมภาพันธ์ 2564

echo thaidate('j F Y', '2021-02-25', false);
// 25 กุมภาพันธ์ 2021
```

The same formatter is available as static methods on `Phattarachai\Thaidate\Thaidate`: `format()`,
`parse()` and `fiscalYear()`, plus the name lists as constants (`Thaidate::LONG_MONTHS`, …).

Upgrading from 13.x? See [UPGRADE.md](UPGRADE.md).

## <a id="carbon-macros"></a> Carbon macros

| Macro | Returns |
| --- | --- |
| `->thaidate($format = 'j F Y', $buddhistEra = true, $thaiDigits = false)` | the date as Thai text, like `thaidate()` |
| `->thaiFiscalYear($buddhistEra = true)` | the fiscal year the date falls in, e.g. `2570` |
| `->startOfThaiFiscalYear()` / `->endOfThaiFiscalYear()` | 1 October 00:00:00 / 30 September 23:59:59 of that fiscal year |
| `::createFromThaiFormat($format, $value, $timezone = null)` | a Carbon read from Thai text, e.g. `10/10/2569` |

The macros work on `Illuminate\Support\Carbon`, `Carbon\Carbon` and `Carbon\CarbonImmutable`, so they work
on Eloquent date attributes and `now()` too, whichever date class the app uses.

```php
use Illuminate\Support\Carbon;

Carbon::parse('2021-02-25')->thaidate('D j M y');
// พฤ. 25 ก.พ. 64

Carbon::parse('2021-02-25')->thaidate('j F Y', false);
// 25 กุมภาพันธ์ 2021

$order->created_at->thaidate('j M Y H:i');
// 25 ก.พ. 2564 09:30
```

## <a id="thai-digits"></a> Thai digits

Official letters and documents often print numbers in Thai digits. Pass `$thaiDigits: true` to print the
date that way:

```php
echo thaidate('j F พ.ศ. Y', '2026-10-10', thaiDigits: true);
// ๑๐ ตุลาคม พ.ศ. ๒๕๖๙

echo now()->thaidate('วันที่ j เดือน F พ.ศ. Y', thaiDigits: true);
// วันที่ ๑๐ เดือน ตุลาคม พ.ศ. ๒๕๖๙
```

Only the numbers that come from the date change. Literal text in the format stays exactly as you wrote it.

## <a id="fiscal-year"></a> Fiscal year (ปีงบประมาณ)

The Thai government fiscal year runs from 1 October to 30 September, and it is named after the year it
ends in. So 10 October 2569 falls in fiscal year 2570.

```php
Carbon::parse('2026-10-10')->thaiFiscalYear();        // 2570
Carbon::parse('2026-09-30')->thaiFiscalYear();        // 2569
Carbon::parse('2026-10-10')->thaiFiscalYear(false);   // 2027 (Gregorian)

now()->startOfThaiFiscalYear();   // 2026-10-01 00:00:00
now()->endOfThaiFiscalYear();     // 2027-09-30 23:59:59
```

Use the start and end to scope a query to the current fiscal year:

```php
Invoice::whereBetween('issued_at', [now()->startOfThaiFiscalYear(), now()->endOfThaiFiscalYear()])->sum('total');
```

On a mutable `Carbon`, `startOfThaiFiscalYear()` and `endOfThaiFiscalYear()` change the instance, like
Carbon's own `startOfMonth()`. Call them on a `copy()`, or use `CarbonImmutable`, to keep the original.

## <a id="reading-thai-dates"></a> Reading Thai dates from input

Users type dates in พ.ศ. `createFromThaiFormat()` reads them back into a Carbon, the reverse of
`thaidate()`:

```php
Carbon::createFromThaiFormat('d/m/Y', '10/10/2569');
// 2026-10-10 00:00:00

Carbon::createFromThaiFormat('j F Y', '10 ตุลาคม 2569');
Carbon::createFromThaiFormat('j M y', '10 ต.ค. 69');
Carbon::createFromThaiFormat('วันlที่ j F พ.ศ. Y', 'วันเสาร์ที่ 10 ตุลาคม พ.ศ. 2569');
Carbon::createFromThaiFormat('j F Y H:i', '๑๐ ตุลาคม ๒๕๖๙ ๐๙:๐๕');
// all 2026-10-10
```

- `Y` reads a Buddhist-era year, and `y` reads a two-digit one as 25xx (`69` → 2569).
- `F`, `M`, `l` and `D` read Thai month and day names, long or short. English names work too.
- Thai digits are accepted anywhere.
- Every other token works as it does in `DateTime::createFromFormat()`, `H:i` and timezones included.
- Fields the format doesn't hold take today's date and midnight, so `d/m/Y` gives 00:00:00.
- A value that doesn't match the format, or a date that doesn't exist (29/02/2568), throws
  `Carbon\Exceptions\InvalidFormatException`.

Pass a timezone as the third argument to read the value in that zone. Called on `CarbonImmutable`, it
returns a `CarbonImmutable`.

In a form request, validate the input by parsing it:

```php
public function rules(): array
{
    return [
        'born_on' => ['required', function (string $attribute, mixed $value, Closure $fail) {
            try {
                Carbon::createFromThaiFormat('d/m/Y', (string) $value);
            } catch (InvalidFormatException) {
                $fail('กรุณากรอกวันที่เป็น วว/ดด/ปปปป (พ.ศ.)');
            }
        }],
    ];
}
```

## <a id="timezone"></a> Timezone

A `DateTimeInterface` is formatted in its own timezone, so a `Carbon` set to `Asia/Bangkok` prints
Bangkok time even when the app runs in UTC. A timestamp, or a date string without an offset, has no timezone of
its own, so it is formatted in PHP's default timezone (`date_default_timezone_get()`, which Laravel sets from
`app.timezone`), the same as `date()`.

```php
// app.timezone = UTC
Carbon::parse('2026-10-10 23:30', 'Asia/Bangkok')->thaidate('j F Y H:i');
// 10 ตุลาคม 2569 23:30

$order->created_at->setTimezone('Asia/Bangkok')->thaidate('j M Y H:i');
```

## <a id="carbon-and-javascript"></a> Working with Carbon and JavaScript

`thaidate` covers what Carbon and the browser don't: the Buddhist-era year in PHP's own format tokens,
fiscal years, and reading พ.ศ. back from input. For the rest, use what is already there.

**Relative time in PHP.** Carbon's Thai locale already does this:

```php
now()->subDays(3)->locale('th')->diffForHumans();
// 3 วันที่แล้ว
```

**Carbon's `translatedFormat()` keeps the Gregorian year.** `->locale('th')->translatedFormat('j F Y')`
prints `10 ตุลาคม 2026`. Use `->thaidate('j F Y')` when you want `10 ตุลาคม 2569`.

**In the browser (Inertia, React, Vue, Alpine).** JavaScript's `Intl` prints the Buddhist era for `th-TH`
on its own, so a front end doesn't need a package. Laravel serializes dates as ISO 8601 in UTC; give
`Intl` the timezone to show:

```js
const date = new Date(order.created_at) // "2026-10-10T16:30:00.000000Z"

new Intl.DateTimeFormat('th-TH', { dateStyle: 'long', timeZone: 'Asia/Bangkok' }).format(date)
// 10 ตุลาคม 2569

new Intl.DateTimeFormat('th-TH', { dateStyle: 'full', timeStyle: 'short', timeZone: 'Asia/Bangkok' }).format(date)
// วันเสาร์ที่ 10 ตุลาคม พ.ศ. 2569 เวลา 23:30

new Intl.DateTimeFormat('th-TH-u-nu-thai', { dateStyle: 'long', timeZone: 'Asia/Bangkok' }).format(date)
// ๑๐ ตุลาคม ๒๕๖๙

new Intl.RelativeTimeFormat('th', { numeric: 'auto' }).format(-1, 'day')
// เมื่อวาน
```

Always pass `timeZone`. Without it, the browser uses the visitor's own zone, and with Inertia SSR the
server's render and the browser's can disagree and cause a hydration mismatch.

**Which side should format?** Format in PHP with `thaidate()` when the exact text matters beyond the
screen: emails, PDFs, Excel exports, notifications, or a format `Intl` can't produce (`D j M y`,
Thai text around the tokens). Format in the browser with `Intl` for UI that only lives on the page. If a
page needs one exact string on both sides, send it from PHP as a ready-made prop:

```php
return Inertia::render('Orders/Show', [
    'order' => $order,
    'createdAtThai' => $order->created_at->setTimezone('Asia/Bangkok')->thaidate('j M Y H:i'),
]);
```

## <a id="static-analysis"></a> Static analysis

The macros are registered at runtime, which PHPStan can't see on its own. This package ships the
PHPStan support to fix that: `->thaidate()`, the fiscal-year macros and `::createFromThaiFormat()` resolve
with their real types and no "undefined method" errors or per-project baseline entries. It activates
automatically in any project using [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer):

```bash
composer require --dev phpstan/extension-installer
```

## ผู้พัฒนา

พัฒนาและดูแลโดย [บริษัท ภัทรชัย อาร์ทิซาน จำกัด](https://phattarachai.dev) (Phattarachai Artisan)
บริษัทที่ปรึกษาและพัฒนาเว็บ ที่เรียนรู้และแบ่งปันกับชุมชน Laravel
แพ็กเกจนี้แบ่งปันให้ชุมชนนำไปใช้และต่อยอดได้อย่างอิสระ

ดูแพ็กเกจอื่นของเราได้ที่ [phattarachai.dev/open-source](https://phattarachai.dev/open-source)
และติดต่อเราได้ที่ [phattarachai.dev](https://phattarachai.dev/#contact)

## License

The MIT License (MIT)
