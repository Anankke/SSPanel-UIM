# Portable UTF-8 | API

The API from the "UTF8"-Class is written as small static methods that will match the default PHP-API e.g.


## Methods

##### access(string $str, int $pos)

Return the character at the specified position: $str[1] like functionality.

```php
UTF8::access('fòô', 1); // 'ô'
```

##### add_bom_to_string(string $str)

Prepends UTF-8 BOM character to the string and returns the whole string.

If BOM already existed there, the Input string is returned.

```php
UTF8::add_bom_to_string('fòô'); // "\xEF\xBB\xBF" . 'fòô'
```

##### bom()

Returns the UTF-8 Byte Order Mark Character.

```php
UTF8::bom(); // "\xEF\xBB\xBF"
```

##### chr(int $code_point) : string

Generates a UTF-8 encoded character from the given code point.

```php
UTF8::chr(666); // 'ʚ'
```

##### chr_map(string|array $callback, string $str) : array

Applies callback to all characters of a string.

```php
UTF8::chr_map(['voku\helper\UTF8', 'strtolower'], 'Κόσμε'); // ['κ','ό', 'σ', 'μ', 'ε']
```

##### chr_size_list(string $str) : array

Generates a UTF-8 encoded character from the given code point.

 1 byte => U+0000  - U+007F
 2 byte => U+0080  - U+07FF
 3 byte => U+0800  - U+FFFF
 4 byte => U+10000 - U+10FFFF

```php
UTF8::chr_size_list('中文空白-test'); // [3, 3, 3, 3, 1, 1, 1, 1, 1]
```

##### chr_to_decimal(string $chr) : int

Get a decimal code representation of a specific character.

```php
UTF8::chr_to_decimal('§'); // 0xa7
```

##### chr_to_hex(string $chr, string $pfix = 'U+')

Get hexadecimal code point (U+xxxx) of a UTF-8 encoded character.

```php
UTF8::chr_to_hex('§'); // 0xa7
```

##### chunk_split(string $body, int $chunklen = 76, string $end = "\r\n") : string

Splits a string into smaller chunks and multiple lines, using the specified line ending character.

```php
UTF8::chunk_split('ABC-ÖÄÜ-中文空白-κόσμε', 3); // "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε"
```

##### clean(string $str, bool $remove_bom = false, bool $normalize_whitespace = false, bool $normalize_msword = false, bool $keep_non_breaking_space = false) : string

Accepts a string and removes all non-UTF-8 characters from it + extras if needed.

```php
UTF8::clean("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - DÃ¼sseldorf'
```

##### cleanup(string $str) : string

Clean-up a and show only printable UTF-8 chars at the end + fix UTF-8 encoding.

```php
UTF8::cleanup("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - Düsseldorf'
```

##### codepoints(mixed $arg, bool $u_style = false) : array

Accepts a string and returns an array of Unicode code points.

```php
UTF8::codepoints('κöñ'); // array(954, 246, 241)
// ... OR ...
UTF8::codepoints('κöñ', true); // array('U+03ba', 'U+00f6', 'U+00f1')
```

##### count_chars(string $str) : array

Returns count of characters used in a string.

```php
UTF8::count_chars('κaκbκc'); // array('κ' => 3, 'a' => 1, 'b' => 1, 'c' => 1)
```

##### encode(string $encoding, string $str, bool $force = true) : string

Encode a string with a new charset-encoding.

INFO:  The different to "UTF8::utf8_encode()" is that this function, try to fix also broken / double encoding,
       so you can call this function also on a UTF-8 String and you don't mess the string.

```php
UTF8::encode('ISO-8859-1', '-ABC-中文空白-'); // '-ABC-????-'
//
UTF8::encode('UTF-8', '-ABC-中文空白-'); // '-ABC-中文空白-'
```

##### file_get_contents(string $filename, int|null $flags = null, resource|null $context = null, int|null $offset = null, int|null $maxlen = null, int $timeout = 10, bool $convertToUtf8 = true) : string

Reads entire file into a string.

WARNING: do not use UTF-8 Option ($convertToUtf8) for binary-files (e.g.: images) !!!

```php
UTF8::file_get_contents('utf16le.txt'); // ...
```

##### file_has_bom(string $file_path) : bool

Checks if a file starts with BOM (Byte Order Mark) character.

```php
UTF8::file_has_bom('utf8_with_bom.txt'); // true
```

##### filter(mixed $var, int $normalization_form = 4, string $leading_combining = '◌') : mixed

Normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
UTF8::filter(array("\xE9", 'à', 'a')); // array('é', 'à', 'a')
```

##### filter_input(int $type, string $var, int $filter = FILTER_DEFAULT, null|array $option = null) : string

"filter_input()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
// _GET['foo'] = 'bar';
UTF8::filter_input(INPUT_GET, 'foo', FILTER_SANITIZE_STRING)); // 'bar'
```

##### filter_input_array(int $type, mixed $definition = null, bool $add_empty = true) : mixed

"filter_input_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
// _GET['foo'] = 'bar';
UTF8::filter_input_array(INPUT_GET, array('foo' => 'FILTER_SANITIZE_STRING')); // array('bar')
```

##### filter_var(string $var, int $filter = FILTER_DEFAULT, array $option = null) : string

"filter_var()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
UTF8::filter_var('-ABC-中文空白-', FILTER_VALIDATE_URL); // false
```

##### filter_var_array(array $data, mixed $definition = null, bool $add_empty = true) : mixed

"filter_var_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
$filters = [ 
  'name'  => ['filter'  => FILTER_CALLBACK, 'options' => ['voku\helper\UTF8', 'ucwords']],
  'age'   => ['filter'  => FILTER_VALIDATE_INT, 'options' => ['min_range' => 1, 'max_range' => 120]],
  'email' => FILTER_VALIDATE_EMAIL,
];

$data = [
  'name' => 'κόσμε', 
  'age' => '18', 
  'email' => 'foo@bar.de'
];

UTF8::filter_var_array($data, $filters, true); // ['name' => 'Κόσμε', 'age' => 18, 'email' => 'foo@bar.de']
```

##### fits_inside(string $str, int $box_size) : bool

Check if the number of unicode characters are not more than the specified integer.

```php
UTF8::fits_inside('κόσμε', 6); // false
```

##### fix_simple_utf8(string $str) : string

Try to fix simple broken UTF-8 strings.

INFO: Take a look at "UTF8::fix_utf8()" if you need a more advanced fix for broken UTF-8 strings.

```php
UTF8::fix_simple_utf8('DÃ¼sseldorf'); // 'Düsseldorf'
```

##### fix_utf8(string|string[] $str) : mixed

Fix a double (or multiple) encoded UTF8 string.

```php
UTF8::fix_utf8('FÃÂÂÂÂ©dÃÂÂÂÂ©ration'); // 'Fédération'
```

##### getCharDirection(string $char) : string ('RTL' or 'LTR')

Get character of a specific character.

```php
UTF8::getCharDirection('ا'); // 'RTL'
```

##### getCharDirection(string $char) : string ('RTL' or 'LTR')

Get character of a specific character.

```php
UTF8::getCharDirection('ا'); // 'RTL'
```

##### hex_to_int(string $str) : int

Converts hexadecimal U+xxxx code point representation to integer.

INFO: opposite to UTF8::int_to_hex()

```php
UTF8::hex_to_int('U+00f1'); // 241
```

##### html_encode(string $str, bool $keepAsciiChars = false, string $encoding = 'UTF-8') : string

Converts a UTF-8 string to a series of HTML numbered entities.

INFO: opposite to UTF8::html_decode()

```php
UTF8::html_encode('中文空白'); // '&#20013;&#25991;&#31354;&#30333;'
```

##### html_entity_decode(string $str, int $flags = null, string $encoding = 'UTF-8') : string

UTF-8 version of html_entity_decode()

The reason we are not using html_entity_decode() by itself is because
while it is not technically correct to leave out the semicolon
at the end of an entity most browsers will still interpret the entity
correctly. html_entity_decode() does not convert entities without
semicolons, so we are left with our own little solution here. Bummer.

Convert all HTML entities to their applicable characters

INFO: opposite to UTF8::html_encode()

```php
UTF8::html_encode('&#20013;&#25991;&#31354;&#30333;'); // '中文空白' 
```

##### htmlentities(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true) : string

Convert all applicable characters to HTML entities: UTF-8 version of htmlentities()

```php
UTF8::htmlentities('<白-öäü>'); // '&lt;&#30333;-&ouml;&auml;&uuml;&gt;'
```

##### htmlspecialchars(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true) : string

Convert only special characters to HTML entities: UTF-8 version of htmlspecialchars()

INFO: Take a look at "UTF8::htmlentities()"

```php
UTF8::htmlspecialchars('<白-öäü>'); // '&lt;白-öäü&gt;'
```

##### int_to_hex(int $int, string $pfix = 'U+') : str

Converts Integer to hexadecimal U+xxxx code point representation.

INFO: opposite to UTF8::hex_to_int()

```php
UTF8::int_to_hex(241); // 'U+00f1'
```

... TODO