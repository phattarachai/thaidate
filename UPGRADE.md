# Upgrade guide

## 13.x → 14.0

Most apps need no change: `thaidate('j F Y', $date)` and every Carbon macro work as before. Check the
points below if you call the helper with unusual values or use the `Thaidate` class directly.

### `thaidate()` is typed

```php
function thaidate(
    string $format = 'j F Y',
    string|int|DateTimeInterface|null $date = null,
    bool $buddhistEra = true,
    bool $thaiDigits = false,
): string
```

- **A string PHP can't read now throws** `Carbon\Exceptions\InvalidFormatException`. In 13.x,
  `thaidate('j F Y', 'not a date')` printed `1 มกราคม 2513` (the Unix epoch).
- **`null` means now.** In 13.x it also printed the Unix epoch, so a nullable column such as
  `thaidate('j F Y', $user->verified_at)` printed 1 January 2513. Check for null first if you show
  something else for a missing date.
- **A float or `false` is a `TypeError`.** Cast to `int` first.
- **The second parameter is named `$date`**, not `$timestamp`. This only matters to named arguments:
  `thaidate(timestamp: $x)` becomes `thaidate(date: $x)`.
- **A date string with an offset keeps it.** `thaidate('H:i', '2026-10-10T23:30:00+07:00')` prints
  `23:30` whatever the app's timezone, the same as a `DateTime` would. In 13.x the string went through
  `strtotime()` and printed in the default timezone.

### `Thaidate` is final and static

- `new Thaidate` and `(new Thaidate)->format(...)` become `Thaidate::format(...)`. The same goes for
  `parse()` and `fiscalYear()`.
- `Thaidate::date($format, $timestamp, $buddhistEra)` is gone. Use `thaidate($format, $timestamp)`, or
  `Thaidate::format($format, (new DateTimeImmutable)->setTimestamp($timestamp), $buddhistEra)`.
- The name lists are constants: `$thaidate->longMonths` becomes `Thaidate::LONG_MONTHS`, and likewise
  `SHORT_MONTHS`, `LONG_DAYS` and `SHORT_DAYS`.
- The class can't be extended. To change a name, format around it instead.
- `CarbonMacros` is internal.
