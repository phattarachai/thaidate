<?php


namespace Phattarachai\thaidate;

use Carbon\Laravel\ServiceProvider;

class ThaidateServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerPublishables();
    }

    public function register()
    {
    }


}
