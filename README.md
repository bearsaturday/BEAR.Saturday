
----

# BEAR.Saturday

## What is BEAR.Satruday ?

BEAR.Saturday is a resource oriented web framework for PHP5.2+

Use [BEAR.Sunday](http://bearsunday.github.io/) for new project.

Requirements
------------

 * PHP 5.2 - 7.2 

## Install


```
composer require bearsaturday/bearsaturday ^0.10
```

## Upgrade from PEAR installed project

Here is the minimum `composer.json` to convert composer based project for exisitng PEAR-installed base project.

```json
{
    "name": "my-vendor/my-project",
    "description": "",
    "license": "proprietary",
    "require": {
        "php": ">=5.4",
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

It is NOT recommended becuase newer project should be created newer framework BEAR.Sunday. But you still install BEAR.Saturday from scrach.

** NOT RECOMMENDED **

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
