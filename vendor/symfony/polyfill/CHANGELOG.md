* v1.2.0

 * bug #61 Normalizer::decompose() should reorder "recursive" combining chars (nicolas-grekas)
 * bug #59 Normalizer::recompose() should reset the last combining class on ASCII (nicolas-grekas)
 * bug #59 Normalizer::isNormalized() should fail with Normalizer::NONE (nicolas-grekas)
 * bug #59 Normalizer::isNormalized() and ::normalize() should check for multibyte string function overload (nicolas-grekas)
 * feature #44/#53 allow paragonie/random_compat 2.0 (ickbinhier)
 * feature #51 Use plain PHP for data maps to benefit from OPcache on PHP 5.6+ (nicolas-grekas)
 * bug #49 Fix hex2bin return null (fuhry, binwiederhier)

* v1.1.1

 * bug #40 [Apcu] Load APCUIterator only when APCIterator exists (nicolas-grekas)
 * bug #37 [Iconv] Fix wrong use in bootstrap.php (tucksaun)
 * bug #31 Fix class_uses polyfill (WouterJ)

* v1.1.0

 * feature #22 [APCu] A new polyfill for the legacy APC users (nicolas-grekas)
 * bug #28 [Php70] Workaround https://bugs.php.net/63206 (nicolas-grekas)

* v1.0.1

 * bug #14 ldap_escape does not encode leading/trailing spaces. (ChadSikorra)
 * bug #17 Fix #16 - gzopen() / gzopen64() - 32 bit builds of Ubuntu 14.04 (fisharebest)

* v1.0.0

 * Hello symfony/polyfill
