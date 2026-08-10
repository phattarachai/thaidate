# Thaidate

[![Latest Version on Packagist](https://img.shields.io/packagist/v/phattarachai/thaidate.svg?style=flat-square)](https://packagist.org/packages/phattarachai/thaidate)
[![Tests](https://img.shields.io/github/actions/workflow/status/phattarachai/thaidate/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/phattarachai/thaidate/actions/workflows/run-tests.yml?query=branch%3Amain)
[![Code Style](https://img.shields.io/github/actions/workflow/status/phattarachai/thaidate/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/phattarachai/thaidate/actions/workflows/fix-php-code-style-issues.yml?query=branch%3Amain)
[![PHP Version](https://img.shields.io/packagist/dependency-v/phattarachai/thaidate/php?style=flat-square&label=php&logo=php&logoColor=white)](https://packagist.org/packages/phattarachai/thaidate)
![Laravel Version](https://img.shields.io/badge/laravel-12%20%7C%2013-FF2D20?style=flat-square&logo=laravel&logoColor=white)
[![Total Downloads](https://img.shields.io/packagist/dt/phattarachai/thaidate.svg?style=flat-square)](https://packagist.org/packages/phattarachai/thaidate)

Display dates in Thai — as a `thaidate()` helper and a Carbon `->thaidate()` macro, with Buddhist-era
years and Thai month/day names.

## <a id="installation"></a> Installation

```
composer require phattarachai/thaidate
```

# <a id="thaidate()"></a> thaidate() function

Display date in Thai using the same PHP built-in [date()](https://www.php.net/manual/en/function.date.php)
function attributes. The date format is as same
as [PHP Datetime Format](https://www.php.net/manual/en/datetime.format.php).

Default Format `j F Y`:

```php
echo thaidate();    
// 25 กุมภาพันธ์ 2564
```

With PHP Date Format:

```php
echo thaidate('วันlที่ j F พ.ศ.Y เวลา H:i:s');
// results: วันพฤหัสบดีที่ 25 กุมภาพันธ์ พ.ศ.2564 เวลา 23:55:29
```

# Laravel Carbon Usage

You can also use `thaidate()` function directly from Laravel carbon instance. It uses Laravel macro feature to add the
functionality as a carbon method.

```php
use Illuminate\Support\Carbon;

Carbon::parse('2021-02-25')->thaidate();
// 25 กุมภาพันธ์ 2564

Carbon::parse('2021-02-25')->thaidate('D j M y');
// พฤ. 25 ก.พ. 64
```

This means you can use thaidate() directly from an Eloquent model attributes that is a date attribute as well.

```php
$user->created_at->thaidate();
// 25 กุมภาพันธ์ 2564 
```

## Static analysis

The `->thaidate()` macro is registered at runtime, which PHPStan can't see on its own. This package
ships the PHPStan support to fix that — `->thaidate()` resolves with no "undefined method" errors and
no per-project baseline entries. It activates automatically in any project using
[`phpstan/extension-installer`](https://github.com/phpstan/extension-installer):

```bash
composer require --dev phpstan/extension-installer
```

## ผู้พัฒนา

🙋‍♂️ สวัสดีครับ ผมอ๊อฟนะครับ เป็น Full Stack Web Developer
รับ Implement งาน Project ทางด้าน Web Application สำหรับองค์กร ธุรกิจ SME ส่วนงานราชการและบริษัทขนาดใหญ่ครับ  
https://phattarachai.dev

line:
[phat-chai](https://line.me/ti/p/~phat-chai)

## License

The MIT License (MIT)
