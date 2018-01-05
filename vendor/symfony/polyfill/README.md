Symfony Polyfill
================

This project backports features found in the latest PHP versions and provides
compatibility layers for some extensions and functions. It is intended to be
used when portability across PHP versions and extensions is desired.

Polyfills are provided for:
- the `apcu` extension when the legacy `apc` extension is installed;
- the `mbstring` and `iconv` extensions;
- the `Normalizer` class and the `grapheme_*` functions;
- the `utf8_encode` and `utf8_decode` functions from the `xml` extension;
- the `Collator`, `NumberFormatter`, `Locale` and `IntlDateFormatter` classes;
- the `intl_error_name`, `intl_get_error_code`, `intl_get_error_message` and
  `intl_is_failure` functions;
- the `hex2bin` function, the `CallbackFilterIterator`,
  `RecursiveCallbackFilterIterator` and `SessionHandlerInterface` classes
  introduced in PHP 5.4;
- the `array_column`, `boolval`, `json_last_error_msg` and `hash_pbkdf2`
  functions introduced in PHP 5.5;
- the `password_hash` and `password_*` related functions introduced in PHP 5.5,
  provided by the `ircmaxell/password-compat` package;
- the `hash_equals` and `ldap_escape` functions introduced in PHP 5.6;
- the `*Error` classes, the `error_clear_last`, `preg_replace_callback_array` and
  `intdiv` functions introduced in PHP 7.0;
- the `random_bytes` and `random_int` functions introduced in PHP 7.0,
  provided by the `paragonie/random_compat` package;
- a `Binary` utility class to be used when compatibility with
  `mbstring.func_overload` is required.

It is strongly recommended to upgrade your PHP version and/or install the missing
extensions whenever possible. This polyfill should be used only when there is no
better choice or when portability is a requirement.

Compatibility notes
===================

To write portable code between PHP5 and PHP7, some care must be taken:
- `\*Error` exceptions must be caught before `\Exception`;
- after calling `error_clear_last()`, the result of `$e = error_get_last()` must be
  verified using `isset($e['message'][0])` instead of `null === $e`.

Usage
=====

When using [Composer](https://getcomposer.org/) to manage your dependencies, you
should **not** `require` the `symfony/polyfill` package, but the standalone ones:
- `symfony/polyfill-apcu` for using the `apcu_*` functions,
- `symfony/polyfill-php54` for using the PHP 5.4 functions,
- `symfony/polyfill-php55` for using the PHP 5.5 functions,
- `symfony/polyfill-php56` for using the PHP 5.6 functions,
- `symfony/polyfill-php70` for using the PHP 7.0 functions,
- `symfony/polyfill-iconv` for using the iconv functions,
- `symfony/polyfill-intl-grapheme` for using the `grapheme_*` functions,
- `symfony/polyfill-intl-icu` for using the intl functions and classes,
- `symfony/polyfill-intl-normalizer` for using the intl normalizer,
- `symfony/polyfill-mbstring` for using the mbstring functions,
- `symfony/polyfill-util` for using the polyfill utility helpers,
- `symfony/polyfill-xml` for using the `utf8_encode/decode` functions.

Requiring `symfony/polyfill` directly would prevent Composer from sharing
correctly polyfills in dependency graphs. As such, it would likely install
more code than required.

Design
======

This package is designed for low overhead and high quality polyfilling.

It adds only a few lightweight `require` statements to the bootstrap process
to support all polyfills. Implementations are then loaded on-demand when
needed during code execution.

Polyfills are unit-tested alongside their native implementation so that
feature and behavior parity can be proven and enforced in the long run.

License
=======

This library is released under the [MIT license](LICENSE).
