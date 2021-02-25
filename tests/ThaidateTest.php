<?php


class ThaidateTest extends Orchestra\Testbench\TestCase
{

    /** @test */
    public function convert_short_date_and_month()
    {
        $result = thaidate('D j M y', strtotime('2021-02-25'));

        $this->assertEquals('พฤ. 25 ก.พ. 64', $result);
    }

    /** @test */
    public function convert_from_timestampe()
    {
        $result = thaidate('l j F Y', strtotime('2021-02-25'));

        $this->assertEquals('พฤหัสบดี 25 กุมภาพันธ์ 2564', $result);
    }

    /** @test */
    public function convert_from_timestampe_gregorian_calendar()
    {
        $result = thaidate('l j F Y', strtotime('2021-02-25'), false);

        $this->assertEquals('พฤหัสบดี 25 กุมภาพันธ์ 2021', $result);
    }

    /** @test */
    public function convert_from_string()
    {
        $result = thaidate('j M Y', '2021-02-25');

        $this->assertEquals('25 ก.พ. 2564', $result);
    }

    /** @test */
    public function convert_from_now()
    {
        $result = thaidate('j M Y');

        $this->assertIsString($result);
    }

}
