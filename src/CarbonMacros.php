<?php

namespace Phattarachai\Thaidate;

use DateTimeZone;
use Illuminate\Support\Carbon;

/**
 * The Carbon macros this package adds. ThaidateServiceProvider registers them at runtime and
 * phpstan-bootstrap.php registers them for static analysis, so both always agree.
 */
class CarbonMacros
{
    public static function register(): void
    {
        Carbon::macro('thaidate', function (string $format = 'j F Y', bool $buddhistEra = true, bool $thaiDigits = false): string {
            return thaidate($format, $this, $buddhistEra, $thaiDigits);
        });

        Carbon::macro('thaiFiscalYear', function (bool $buddhistEra = true): int {
            return (new Thaidate)->fiscalYear($this, $buddhistEra);
        });

        Carbon::macro('startOfThaiFiscalYear', function (): static {
            // Carbon's macro() types $this as the concrete class it was called on, not as static.
            return $this->setDate($this->month >= 10 ? $this->year : $this->year - 1, 10, 1)->startOfDay(); // @phpstan-ignore return.type
        });

        Carbon::macro('endOfThaiFiscalYear', function (): static {
            // Carbon's macro() types $this as the concrete class it was called on, not as static.
            return $this->setDate($this->month >= 10 ? $this->year + 1 : $this->year, 9, 30)->endOfDay(); // @phpstan-ignore return.type
        });

        // A static closure, so Carbon's PHPStan extension reads this as a static method. Carbon
        // binds its scope to the class it was called on, so static is CarbonImmutable when called
        // as CarbonImmutable::createFromThaiFormat(); PHPStan only sees this file's class here.
        $createFromThaiFormat = static function (string $format, string $value, DateTimeZone|string|null $timezone = null): static {
            return static::instance((new Thaidate)->parse($format, $value, $timezone)); // @phpstan-ignore staticMethod.notFound
        };

        Carbon::macro('createFromThaiFormat', $createFromThaiFormat);
    }
}
