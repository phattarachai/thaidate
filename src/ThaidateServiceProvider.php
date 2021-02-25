<?php


namespace Phattarachai\Thaidate;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Carbon;

class ThaidateServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Carbon::macro('thaidate', function ($format = 'j F Y') {
            return thaidate($format, $this->timestamp);
        });
    }

}
