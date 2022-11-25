![test](https://github.com/phattarachai/thaidate/actions/workflows/php.yml/badge.svg)
[![Packagist](https://img.shields.io/packagist/dt/phattarachai/thaidate.svg)](https://github.com/phattarachai/thaidate/releases)
[![Maintainability](https://api.codeclimate.com/v1/badges/866379571541812960f6/maintainability)](https://codeclimate.com/github/phattarachai/thaidate/maintainability)

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

## สนับสนุนผู้พัฒนา

🙋‍♂️ สวัสดีครับ ผมอ๊อฟนะครับ เป็น Full Stack Web Developer มีสร้าง package ขึ้นมาใช้งานในโปรเจคตัวเองที่ใช้งานบ่อย ๆ
ถ้ามีข้อเสนอแนะอยากให้ทำ package อะไรบน PHP / Laravel / NodeJS / Python หรือภาษาอื่น ๆ ทักทายมาได้เลยครับ

line:
[phat-chai](https://line.me/ti/p/~phat-chai)

💻 รับงาน Freelance หากมีโปรเจคที่น่าสนใจ หาทีมงานร่วมงาน หาโปรแกรมเมอร์ที่มีประสบการณ์ช่วยแก้โจทย์ที่ท้าทาย
ติดต่อมาได้เลยครับ ยินดีให้ความช่วยเหลือและรอสร้างผลงานที่ดีร่วมกันครับ

📄 เข้ามาดูประวัติและผลงานได้ที่ https://phattarachai.dev

<a href="https://phattarachai.dev">
    <img src="/art/phattarachai.dev.png" alt="https://phattarachai.dev" width="419px" />
</a>

## License

The MIT License (MIT)
