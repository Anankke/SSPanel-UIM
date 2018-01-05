[![Stories in Ready](https://badge.waffle.io/voku/anti-xss.png?label=ready&title=Ready)](https://waffle.io/voku/anti-xss)
[![Build Status](https://travis-ci.org/voku/anti-xss.svg)](https://travis-ci.org/voku/anti-xss)
[![Coverage Status](https://coveralls.io/repos/voku/anti-xss/badge.svg?branch=master&service=github)](https://coveralls.io/github/voku/anti-xss?branch=master)
[![codecov.io](http://codecov.io/github/voku/anti-xss/coverage.svg?branch=master)](http://codecov.io/github/voku/anti-xss?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/anti-xss/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/anti-xss/?branch=master)
[![Codacy Badge](https://www.codacy.com/project/badge/8e3c9da417124971b8d8e0c1046c24c7)](https://www.codacy.com/app/voku/anti-xss)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/03a4657f-8b27-4387-93f6-9d2a63713484/mini.png)](https://insight.sensiolabs.com/projects/03a4657f-8b27-4387-93f6-9d2a63713484)
[![Reference Status](https://www.versioneye.com/php/voku:anti-xss/reference_badge.svg?style=flat)](https://www.versioneye.com/php/voku:anti-xss/references)
[![Dependency Status](https://www.versioneye.com/php/voku:anti-xss/dev-master/badge.svg)](https://www.versioneye.com/php/voku:anti-xss/dev-master)
[![Latest Stable Version](https://poser.pugx.org/voku/anti-xss/v/stable)](https://packagist.org/packages/voku/anti-xss) 
[![Total Downloads](https://poser.pugx.org/voku/anti-xss/downloads)](https://packagist.org/packages/voku/anti-xss) 
[![Latest Unstable Version](https://poser.pugx.org/voku/anti-xss/v/unstable)](https://packagist.org/packages/voku/anti-xss)
[![PHP 7 ready](http://php7ready.timesplinter.ch/voku/anti-xss/badge.svg)](https://travis-ci.org/voku/anti-xss)
[![License](https://poser.pugx.org/voku/anti-xss/license)](https://packagist.org/packages/voku/anti-xss)


AntiXSS - Library
=============

"Cross-site scripting (XSS) is a type of computer security vulnerability typically found in Web applications. XSS enables 
attackers to inject client-side script into Web pages viewed by other users. A cross-site scripting vulnerability may be 
used by attackers to bypass access controls such as the same origin policy. Cross-site scripting carried out on websites 
accounted for roughly 84% of all security vulnerabilities documented by Symantec as of 2007." - http://en.wikipedia.org/wiki/Cross-site_scripting

DEMO:
=====
[http://anti-xss-demo.suckup.de/](http://anti-xss-demo.suckup.de/)

NOTES:
======
1) Use [filter_input()](http://php.net/manual/de/function.filter-input.php) - don't use GLOBAL-Array (e.g. $_SESSION, $_GET, $_POST, $_SERVER) directly

2) Use [HTML Purifier](http://htmlpurifier.org/) if you need a more configurable solution

3) Add "Content Security Policy's" -> [Introduction to Content Security Policy](http://www.html5rocks.com/en/tutorials/security/content-security-policy/)

4) DO NOT WRITE YOUR OWN REGEX TO PARSE HTML!

5) READ THIS TEXT -> [XSS (Cross Site Scripting) Prevention Cheat Sheet](https://www.owasp.org/index.php/XSS_%28Cross_Site_Scripting%29_Prevention_Cheat_Sheet)

6) TEST THIS TOOL -> [Zed Attack Proxy (ZAP)](https://github.com/zaproxy/zaproxy)

Install via "composer require"
======
```shell
composer require voku/anti-xss
```

Usage:
======

    $antiXss = new AntiXSS();

Example 1: (HTML Character)

    $harm_string = "Hello, i try to <script>alert('Hack');</script> your site";
    $harmless_string = $antiXss->xss_clean($harm_string);
    
    // Hello, i try to alert&#40;'Hack'&#41;; your site

Example 2: (Hexadecimal HTML Character)

    $harm_string = "<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>";
    $harmless_string = $antiXss->xss_clean($harm_string);
        
    // <IMG >
    
Example 3: (Unicode Hex Character)

    $harm_string = "<a href='&#x2000;javascript:alert(1)'>CLICK</a>";
    $harmless_string = $antiXss->xss_clean($harm_string);
        
    // <a >CLICK</a>

Example 4: (Unicode Character)

    $harm_string = "<a href=\"\u0001java\u0003script:alert(1)\">CLICK<a>";
    $harmless_string = $antiXss->xss_clean($harm_string);
        
    // <a >CLICK</a>

Unit Test:
==========

1) [Composer](https://getcomposer.org) is a prerequisite for running the tests.

```
composer install
```

2) The tests can be executed by running this command from the root directory:

```bash
./vendor/bin/phpunit
```

