<?php

use Illuminate\Support\Carbon;

/**
 * Register the thaidate() Carbon macro for static analysis.
 *
 * ThaidateServiceProvider::boot() adds this macro at runtime, but a service provider never
 * boots during a PHPStan pass — so nesbot/carbon's bundled MacroExtension can't see it and
 * every ->thaidate() call reads as an undefined method. Registering it here on the default
 * factory lets that extension resolve the call on any CarbonInterface subclass. Consumers get
 * this for free via extra.phpstan.includes + phpstan/extension-installer; no per-project setup.
 */
Carbon::macro('thaidate', function (string $format = 'j F Y'): string {
    return thaidate($format, $this->timestamp);
});
