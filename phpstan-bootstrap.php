<?php

use Phattarachai\Thaidate\CarbonMacros;

/**
 * Register this package's Carbon macros for static analysis.
 *
 * ThaidateServiceProvider::boot() adds these macros at runtime, but a service provider never
 * boots during a PHPStan pass — so nesbot/carbon's bundled MacroExtension can't see it and
 * every ->thaidate() or ->thaiFiscalYear() call reads as an undefined method. Registering them here on the default
 * factory lets that extension resolve the call on any CarbonInterface subclass. Consumers get
 * this for free via extra.phpstan.includes + phpstan/extension-installer; no per-project setup.
 */
CarbonMacros::register();
