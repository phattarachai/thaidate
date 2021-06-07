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
    public function convert_my_short_birth_date()
    {
        $result = thaidate('D j M y', strtotime('1987-11-28'));

        $this->assertEquals('ส. 28 พ.ย. 30', $result);
    }

    /** @test */
    public function convert_my_short_34_years_old()
    {
        $result = thaidate('D j M y', strtotime('2021-11-28'));

        $this->assertEquals('อา. 28 พ.ย. 64', $result);
    }

    /** @test */
    public function convert_from_timestampe()
    {
        $result = thaidate('l j F Y', strtotime('2021-02-25'));

        $this->assertEquals('พฤหัสบดี 25 กุมภาพันธ์ 2564', $result);
    }

    /** @test */
    public function convert_from_php_date_time()
    {
        $result = thaidate('l j F Y', new DateTime('2021-02-25'));

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

    /** @test */
    public function default_format()
    {
        $result = thaidate();

        $this->assertIsString($result);
    }

}
