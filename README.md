# BEAR.Saturday

© 2008-2019

## What is BEAR.Saturday ?

BEAR.Saturdayは、PHP 5.2+用のリソース指向Webフレームワークです。2008年から2019年にかけて、エキサイト翻訳、エキサイトブログ、エキサイトニュース、エキサイトレシピなど、当時のエキサイトの主要サービスを支えていました。

この0.11.xでは、PHP 8.4/8.5で依存解決とテスト実行ができるようにしています。

Requirements
------------

 * PHP 8.4.1+
 * Composer 2


Development
-----------

Install dependencies and run the compatibility checks:

```bash
composer install
composer ci
```

Docker
------

Run the same checks in PHP 8.4 and PHP 8.5 containers:

```bash
docker compose run --rm php84
docker compose run --rm php85
```

Documentation
-------------

（日本語） https://github.com/bearsaturday/manual

## Upgrade from PEAR installed project

Here is the minimum `composer.json` to convert composer based project for existing PEAR-installed base project.

```json
{
    "name": "my-vendor/my-project",
    "description": "",
    "license": "proprietary",
    "require": {
        "php": ">=8.4.1",
        "bearsaturday/bearsaturday": "^0.10"
    },
    "repositories": [
        {
            "type": "pear",
            "url": "https://pear.php.net"
        }
    ],
    "minimum-stability": "dev",
    "prefer-stable": true,
    "include-path": [
        "./"
    ],
    "autoload": {
        "classmap": [
            "App"
        ]
    }
}

```

Create project
--------------

It is NOT recommended to create new BEAR.Saturday project. Use [BEAR.Sunday](http://bearsunday.github.io/) instead.

```
composer create-project bearsaturday/skeleton MyVendor.MyPackage
```

Hosting
-------

```
ln -s MyVendor.MyPackage/htdocs /path/to/vhost_dir
```

Demo
----

Run demo site [beardemo.local](https://github.com/bearsaturday/beardemo.local)

コーディングの参考にしてください

YouTube
-------

See [Hello World demo][2] in youtube. 


[2]: http://www.youtube.com/watch?v=NKdiNdNbH0Y


---

First public release: 31 July 2008
