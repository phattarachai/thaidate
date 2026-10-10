<?php

namespace Phattarachai\Thaidate;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Carbon;

class ThaidateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Carbon::macro('thaidate', function (string $format = 'j F Y', bool $buddhistEra = true): string {
            return thaidate($format, $this, $buddhistEra);
        });
    }
}
