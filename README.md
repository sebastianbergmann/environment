# Environment

This component provides functionality that helps writing PHP code that has runtime-specific (PHP / HHVM) execution paths.

[![Latest Stable Version](https://poser.pugx.org/sebastian/environment/v/stable.png)](https://packagist.org/packages/sebastian/environment)
[![Build Status](https://travis-ci.org/sebastianbergmann/environment.png?branch=master)](https://travis-ci.org/sebastianbergmann/environment)

## Installation

To add Environment as a local, per-project dependency to your project, simply add a dependency on `sebastian/environment` to your project's `composer.json` file. Here is a minimal example of a `composer.json` file that just defines a dependency on Environment 1.0:

    {
        "require": {
            "sebastian/environment": "1.0.*"
        }
    }

## Usage

```php
<?php
use SebastianBergmann\Environment\Environment;

$env = new Environment;

var_dump($env->canCollectCodeCoverage());
var_dump($env->getBinary());
var_dump($env->hasXdebug());
var_dump($env->isHHVM());
var_dump($env->isPHP());
```

### Output on PHP

    $ php --version
    PHP 5.5.8 (cli) (built: Jan  9 2014 08:33:30)
    Copyright (c) 1997-2013 The PHP Group
    Zend Engine v2.5.0, Copyright (c) 1998-2013 Zend Technologies
        with Xdebug v2.2.3, Copyright (c) 2002-2013, by Derick Rethans


    $ php example.php
    bool(true)
    string(14) "'/usr/bin/php'"
    bool(true)
    bool(false)
    bool(true)

### Output on HHVM

    $ hhvm --version
    HipHop VM 2.4.0-dev (rel)
    Compiler: heads/master-0-ga98e57cabee7e7f0d14493ab17d5c7ab0157eb98
    Repo schema: 8d6e69287c41c1f09bb4d327421720d1922cfc67


    $ hhvm example.php
    bool(true)
    string(42) "'/usr/local/src/hhvm/hphp/hhvm/hhvm' --php"
    bool(false)
    bool(true)
    bool(false)

