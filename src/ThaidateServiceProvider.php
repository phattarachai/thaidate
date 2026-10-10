<?php

namespace Phattarachai\Thaidate;

use Carbon\Laravel\ServiceProvider;

class ThaidateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        CarbonMacros::register();
    }
}
