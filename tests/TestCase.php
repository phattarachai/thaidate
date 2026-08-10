<?php

namespace Phattarachai\Thaidate\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Phattarachai\Thaidate\ThaidateServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [ThaidateServiceProvider::class];
    }
}
