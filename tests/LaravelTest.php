<?php


use Illuminate\Support\Carbon;
use Phattarachai\Thaidate\ThaidateServiceProvider;

class LaravelTest extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return [ThaidateServiceProvider::class];
    }

    /** @test */
    public function convert_short_format()
    {
        $result = Carbon::parse('2021-02-25')->thaidate('D j M y');

        $this->assertEquals('พฤ. 25 ก.พ. 64', $result);
    }

    /** @test */
    public function convert_long_format()
    {
        $result = Carbon::parse('2021-02-25')->thaidate('l j F Y');

        $this->assertEquals('พฤหัสบดี 25 กุมภาพันธ์ 2564', $result);
    }

}
