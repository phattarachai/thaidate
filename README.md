[comment]: <> ([![Travis]&#40;https://travis-ci.org/phattarachai/thaidate.svg?branch=master&#41;]&#40;https://travis-ci.org/phattarachai/thaidate?branch=master;)

[![Packagist](https://img.shields.io/packagist/dt/phattarachai/thaidate.svg)](https://github.com/phattarachai/line-notify/releases)

[comment]: <> ([![Test Coverage]&#40;https://codeclimate.com/github/phattarachai/thaidate/badges/coverage.svg&#41;]&#40;https://codeclimate.com/github/phattarachai/thaidate/coverage&#41;)

[comment]: <> ([![Maintainability]&#40;https://api.codeclimate.com/v1/badges/3dc8135eee70ae7da325/maintainability&#41;]&#40;https://codeclimate.com/github/corcel/corcel/maintainability&#41;)

# <a id="installation"></a> Installation

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

## Credit

A project by [phattarachai.dev](https://phattarachai.dev)

If my package make your life easier, please consider:

<a href="https://ko-fi.com/phattarachai#checkoutModal" target="_blank">Buy me a Coffee</a> |

<a href="https://twitter.com/phatchai" target="_blank">Follow Me on Twitter</a>

## License

The MIT License (MIT)
