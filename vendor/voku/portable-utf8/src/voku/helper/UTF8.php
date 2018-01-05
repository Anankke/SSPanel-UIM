<?php

namespace voku\helper;

use Symfony\Polyfill\Intl\Grapheme\Grapheme;
use Symfony\Polyfill\Xml\Xml;

/**
 * UTF8-Helper-Class
 *
 * @package voku\helper
 */
class UTF8
{
  /**
   * @var array
   */
  protected static $win1252ToUtf8 = array(
      128 => "\xe2\x82\xac", // EURO SIGN
      130 => "\xe2\x80\x9a", // SINGLE LOW-9 QUOTATION MARK
      131 => "\xc6\x92", // LATIN SMALL LETTER F WITH HOOK
      132 => "\xe2\x80\x9e", // DOUBLE LOW-9 QUOTATION MARK
      133 => "\xe2\x80\xa6", // HORIZONTAL ELLIPSIS
      134 => "\xe2\x80\xa0", // DAGGER
      135 => "\xe2\x80\xa1", // DOUBLE DAGGER
      136 => "\xcb\x86", // MODIFIER LETTER CIRCUMFLEX ACCENT
      137 => "\xe2\x80\xb0", // PER MILLE SIGN
      138 => "\xc5\xa0", // LATIN CAPITAL LETTER S WITH CARON
      139 => "\xe2\x80\xb9", // SINGLE LEFT-POINTING ANGLE QUOTE
      140 => "\xc5\x92", // LATIN CAPITAL LIGATURE OE
      142 => "\xc5\xbd", // LATIN CAPITAL LETTER Z WITH CARON
      145 => "\xe2\x80\x98", // LEFT SINGLE QUOTATION MARK
      146 => "\xe2\x80\x99", // RIGHT SINGLE QUOTATION MARK
      147 => "\xe2\x80\x9c", // LEFT DOUBLE QUOTATION MARK
      148 => "\xe2\x80\x9d", // RIGHT DOUBLE QUOTATION MARK
      149 => "\xe2\x80\xa2", // BULLET
      150 => "\xe2\x80\x93", // EN DASH
      151 => "\xe2\x80\x94", // EM DASH
      152 => "\xcb\x9c", // SMALL TILDE
      153 => "\xe2\x84\xa2", // TRADE MARK SIGN
      154 => "\xc5\xa1", // LATIN SMALL LETTER S WITH CARON
      155 => "\xe2\x80\xba", // SINGLE RIGHT-POINTING ANGLE QUOTE
      156 => "\xc5\x93", // LATIN SMALL LIGATURE OE
      158 => "\xc5\xbe", // LATIN SMALL LETTER Z WITH CARON
      159 => "\xc5\xb8", // LATIN CAPITAL LETTER Y WITH DIAERESIS
  );

  /**
   * @var array
   */
  protected static $cp1252ToUtf8 = array(
      '' => '€',
      '' => '‚',
      '' => 'ƒ',
      '' => '„',
      '' => '…',
      '' => '†',
      '' => '‡',
      '' => 'ˆ',
      '' => '‰',
      '' => 'Š',
      '' => '‹',
      '' => 'Œ',
      '' => 'Ž',
      '' => '‘',
      '' => '’',
      '' => '“',
      '' => '”',
      '' => '•',
      '' => '–',
      '' => '—',
      '' => '˜',
      '' => '™',
      '' => 'š',
      '' => '›',
      '' => 'œ',
      '' => 'ž',
      '' => 'Ÿ',
  );

  /**
   * Bom => Byte-Length
   *
   * INFO: https://en.wikipedia.org/wiki/Byte_order_mark
   *
   * @var array
   */
  protected static $bom = array(
      "\xef\xbb\xbf"     => 3, // UTF-8 BOM
      'ï»¿'              => 6, // UTF-8 BOM as "WINDOWS-1252" (one char has [maybe] more then one byte ...)
      "\x00\x00\xfe\xff" => 4, // UTF-32 (BE) BOM
      "\xff\xfe\x00\x00" => 4, // UTF-32 (LE) BOM
      "\xfe\xff"         => 2, // UTF-16 (BE) BOM
      'þÿ'               => 4, // UTF-16 (BE) BOM as "WINDOWS-1252"
      "\xff\xfe"         => 2, // UTF-16 (LE) BOM
      'ÿþ'               => 4, // UTF-16 (LE) BOM as "WINDOWS-1252"
  );

  /**
   * Numeric code point => UTF-8 Character
   *
   * url: http://www.w3schools.com/charsets/ref_utf_punctuation.asp
   *
   * @var array
   */
  protected static $whitespace = array(
    // NUL Byte
    0     => "\x0",
    // Tab
    9     => "\x9",
    // New Line
    10    => "\xa",
    // Vertical Tab
    11    => "\xb",
    // Carriage Return
    13    => "\xd",
    // Ordinary Space
    32    => "\x20",
    // NO-BREAK SPACE
    160   => "\xc2\xa0",
    // OGHAM SPACE MARK
    5760  => "\xe1\x9a\x80",
    // MONGOLIAN VOWEL SEPARATOR
    6158  => "\xe1\xa0\x8e",
    // EN QUAD
    8192  => "\xe2\x80\x80",
    // EM QUAD
    8193  => "\xe2\x80\x81",
    // EN SPACE
    8194  => "\xe2\x80\x82",
    // EM SPACE
    8195  => "\xe2\x80\x83",
    // THREE-PER-EM SPACE
    8196  => "\xe2\x80\x84",
    // FOUR-PER-EM SPACE
    8197  => "\xe2\x80\x85",
    // SIX-PER-EM SPACE
    8198  => "\xe2\x80\x86",
    // FIGURE SPACE
    8199  => "\xe2\x80\x87",
    // PUNCTUATION SPACE
    8200  => "\xe2\x80\x88",
    // THIN SPACE
    8201  => "\xe2\x80\x89",
    //HAIR SPACE
    8202  => "\xe2\x80\x8a",
    // LINE SEPARATOR
    8232  => "\xe2\x80\xa8",
    // PARAGRAPH SEPARATOR
    8233  => "\xe2\x80\xa9",
    // NARROW NO-BREAK SPACE
    8239  => "\xe2\x80\xaf",
    // MEDIUM MATHEMATICAL SPACE
    8287  => "\xe2\x81\x9f",
    // IDEOGRAPHIC SPACE
    12288 => "\xe3\x80\x80",
  );

  /**
   * @var array
   */
  protected static $whitespaceTable = array(
      'SPACE'                     => "\x20",
      'NO-BREAK SPACE'            => "\xc2\xa0",
      'OGHAM SPACE MARK'          => "\xe1\x9a\x80",
      'EN QUAD'                   => "\xe2\x80\x80",
      'EM QUAD'                   => "\xe2\x80\x81",
      'EN SPACE'                  => "\xe2\x80\x82",
      'EM SPACE'                  => "\xe2\x80\x83",
      'THREE-PER-EM SPACE'        => "\xe2\x80\x84",
      'FOUR-PER-EM SPACE'         => "\xe2\x80\x85",
      'SIX-PER-EM SPACE'          => "\xe2\x80\x86",
      'FIGURE SPACE'              => "\xe2\x80\x87",
      'PUNCTUATION SPACE'         => "\xe2\x80\x88",
      'THIN SPACE'                => "\xe2\x80\x89",
      'HAIR SPACE'                => "\xe2\x80\x8a",
      'LINE SEPARATOR'            => "\xe2\x80\xa8",
      'PARAGRAPH SEPARATOR'       => "\xe2\x80\xa9",
      'ZERO WIDTH SPACE'          => "\xe2\x80\x8b",
      'NARROW NO-BREAK SPACE'     => "\xe2\x80\xaf",
      'MEDIUM MATHEMATICAL SPACE' => "\xe2\x81\x9f",
      'IDEOGRAPHIC SPACE'         => "\xe3\x80\x80",
  );

  /**
   * bidirectional text chars
   *
   * url: https://www.w3.org/International/questions/qa-bidi-unicode-controls
   *
   * @var array
   */
  protected static $bidiUniCodeControlsTable = array(
    // LEFT-TO-RIGHT EMBEDDING (use -> dir = "ltr")
    8234 => "\xE2\x80\xAA",
    // RIGHT-TO-LEFT EMBEDDING (use -> dir = "rtl")
    8235 => "\xE2\x80\xAB",
    // POP DIRECTIONAL FORMATTING // (use -> </bdo>)
    8236 => "\xE2\x80\xAC",
    // LEFT-TO-RIGHT OVERRIDE // (use -> <bdo dir = "ltr">)
    8237 => "\xE2\x80\xAD",
    // RIGHT-TO-LEFT OVERRIDE // (use -> <bdo dir = "rtl">)
    8238 => "\xE2\x80\xAE",
    // LEFT-TO-RIGHT ISOLATE // (use -> dir = "ltr")
    8294 => "\xE2\x81\xA6",
    // RIGHT-TO-LEFT ISOLATE // (use -> dir = "rtl")
    8295 => "\xE2\x81\xA7",
    // FIRST STRONG ISOLATE // (use -> dir = "auto")
    8296 => "\xE2\x81\xA8",
    // POP DIRECTIONAL ISOLATE
    8297 => "\xE2\x81\xA9",
  );

  /**
   * @var array
   */
  protected static $commonCaseFold = array(
      'ſ'            => 's',
      "\xCD\x85"     => 'ι',
      'ς'            => 'σ',
      "\xCF\x90"     => 'β',
      "\xCF\x91"     => 'θ',
      "\xCF\x95"     => 'φ',
      "\xCF\x96"     => 'π',
      "\xCF\xB0"     => 'κ',
      "\xCF\xB1"     => 'ρ',
      "\xCF\xB5"     => 'ε',
      "\xE1\xBA\x9B" => "\xE1\xB9\xA1",
      "\xE1\xBE\xBE" => 'ι',
  );

  /**
   * @var array
   */
  protected static $brokenUtf8ToUtf8 = array(
      "\xc2\x80" => "\xe2\x82\xac", // EURO SIGN
      "\xc2\x82" => "\xe2\x80\x9a", // SINGLE LOW-9 QUOTATION MARK
      "\xc2\x83" => "\xc6\x92", // LATIN SMALL LETTER F WITH HOOK
      "\xc2\x84" => "\xe2\x80\x9e", // DOUBLE LOW-9 QUOTATION MARK
      "\xc2\x85" => "\xe2\x80\xa6", // HORIZONTAL ELLIPSIS
      "\xc2\x86" => "\xe2\x80\xa0", // DAGGER
      "\xc2\x87" => "\xe2\x80\xa1", // DOUBLE DAGGER
      "\xc2\x88" => "\xcb\x86", // MODIFIER LETTER CIRCUMFLEX ACCENT
      "\xc2\x89" => "\xe2\x80\xb0", // PER MILLE SIGN
      "\xc2\x8a" => "\xc5\xa0", // LATIN CAPITAL LETTER S WITH CARON
      "\xc2\x8b" => "\xe2\x80\xb9", // SINGLE LEFT-POINTING ANGLE QUOTE
      "\xc2\x8c" => "\xc5\x92", // LATIN CAPITAL LIGATURE OE
      "\xc2\x8e" => "\xc5\xbd", // LATIN CAPITAL LETTER Z WITH CARON
      "\xc2\x91" => "\xe2\x80\x98", // LEFT SINGLE QUOTATION MARK
      "\xc2\x92" => "\xe2\x80\x99", // RIGHT SINGLE QUOTATION MARK
      "\xc2\x93" => "\xe2\x80\x9c", // LEFT DOUBLE QUOTATION MARK
      "\xc2\x94" => "\xe2\x80\x9d", // RIGHT DOUBLE QUOTATION MARK
      "\xc2\x95" => "\xe2\x80\xa2", // BULLET
      "\xc2\x96" => "\xe2\x80\x93", // EN DASH
      "\xc2\x97" => "\xe2\x80\x94", // EM DASH
      "\xc2\x98" => "\xcb\x9c", // SMALL TILDE
      "\xc2\x99" => "\xe2\x84\xa2", // TRADE MARK SIGN
      "\xc2\x9a" => "\xc5\xa1", // LATIN SMALL LETTER S WITH CARON
      "\xc2\x9b" => "\xe2\x80\xba", // SINGLE RIGHT-POINTING ANGLE QUOTE
      "\xc2\x9c" => "\xc5\x93", // LATIN SMALL LIGATURE OE
      "\xc2\x9e" => "\xc5\xbe", // LATIN SMALL LETTER Z WITH CARON
      "\xc2\x9f" => "\xc5\xb8", // LATIN CAPITAL LETTER Y WITH DIAERESIS
      'Ã¼'       => 'ü',
      'Ã¤'       => 'ä',
      'Ã¶'       => 'ö',
      'Ã–'       => 'Ö',
      'ÃŸ'       => 'ß',
      'Ã '       => 'à',
      'Ã¡'       => 'á',
      'Ã¢'       => 'â',
      'Ã£'       => 'ã',
      'Ã¹'       => 'ù',
      'Ãº'       => 'ú',
      'Ã»'       => 'û',
      'Ã™'       => 'Ù',
      'Ãš'       => 'Ú',
      'Ã›'       => 'Û',
      'Ãœ'       => 'Ü',
      'Ã²'       => 'ò',
      'Ã³'       => 'ó',
      'Ã´'       => 'ô',
      'Ã¨'       => 'è',
      'Ã©'       => 'é',
      'Ãª'       => 'ê',
      'Ã«'       => 'ë',
      'Ã€'       => 'À',
      'Ã'       => 'Á',
      'Ã‚'       => 'Â',
      'Ãƒ'       => 'Ã',
      'Ã„'       => 'Ä',
      'Ã…'       => 'Å',
      'Ã‡'       => 'Ç',
      'Ãˆ'       => 'È',
      'Ã‰'       => 'É',
      'ÃŠ'       => 'Ê',
      'Ã‹'       => 'Ë',
      'ÃŒ'       => 'Ì',
      'Ã'       => 'Í',
      'ÃŽ'       => 'Î',
      'Ã'       => 'Ï',
      'Ã‘'       => 'Ñ',
      'Ã’'       => 'Ò',
      'Ã“'       => 'Ó',
      'Ã”'       => 'Ô',
      'Ã•'       => 'Õ',
      'Ã˜'       => 'Ø',
      'Ã¥'       => 'å',
      'Ã¦'       => 'æ',
      'Ã§'       => 'ç',
      'Ã¬'       => 'ì',
      'Ã­'       => 'í',
      'Ã®'       => 'î',
      'Ã¯'       => 'ï',
      'Ã°'       => 'ð',
      'Ã±'       => 'ñ',
      'Ãµ'       => 'õ',
      'Ã¸'       => 'ø',
      'Ã½'       => 'ý',
      'Ã¿'       => 'ÿ',
      'â‚¬'      => '€',
  );

  /**
   * @var array
   */
  protected static $utf8ToWin1252 = array(
      "\xe2\x82\xac" => "\x80", // EURO SIGN
      "\xe2\x80\x9a" => "\x82", // SINGLE LOW-9 QUOTATION MARK
      "\xc6\x92"     => "\x83", // LATIN SMALL LETTER F WITH HOOK
      "\xe2\x80\x9e" => "\x84", // DOUBLE LOW-9 QUOTATION MARK
      "\xe2\x80\xa6" => "\x85", // HORIZONTAL ELLIPSIS
      "\xe2\x80\xa0" => "\x86", // DAGGER
      "\xe2\x80\xa1" => "\x87", // DOUBLE DAGGER
      "\xcb\x86"     => "\x88", // MODIFIER LETTER CIRCUMFLEX ACCENT
      "\xe2\x80\xb0" => "\x89", // PER MILLE SIGN
      "\xc5\xa0"     => "\x8a", // LATIN CAPITAL LETTER S WITH CARON
      "\xe2\x80\xb9" => "\x8b", // SINGLE LEFT-POINTING ANGLE QUOTE
      "\xc5\x92"     => "\x8c", // LATIN CAPITAL LIGATURE OE
      "\xc5\xbd"     => "\x8e", // LATIN CAPITAL LETTER Z WITH CARON
      "\xe2\x80\x98" => "\x91", // LEFT SINGLE QUOTATION MARK
      "\xe2\x80\x99" => "\x92", // RIGHT SINGLE QUOTATION MARK
      "\xe2\x80\x9c" => "\x93", // LEFT DOUBLE QUOTATION MARK
      "\xe2\x80\x9d" => "\x94", // RIGHT DOUBLE QUOTATION MARK
      "\xe2\x80\xa2" => "\x95", // BULLET
      "\xe2\x80\x93" => "\x96", // EN DASH
      "\xe2\x80\x94" => "\x97", // EM DASH
      "\xcb\x9c"     => "\x98", // SMALL TILDE
      "\xe2\x84\xa2" => "\x99", // TRADE MARK SIGN
      "\xc5\xa1"     => "\x9a", // LATIN SMALL LETTER S WITH CARON
      "\xe2\x80\xba" => "\x9b", // SINGLE RIGHT-POINTING ANGLE QUOTE
      "\xc5\x93"     => "\x9c", // LATIN SMALL LIGATURE OE
      "\xc5\xbe"     => "\x9e", // LATIN SMALL LETTER Z WITH CARON
      "\xc5\xb8"     => "\x9f", // LATIN CAPITAL LETTER Y WITH DIAERESIS
  );

  /**
   * @var array
   */
  protected static $utf8MSWord = array(
      "\xc2\xab"     => '"', // « (U+00AB) in UTF-8
      "\xc2\xbb"     => '"', // » (U+00BB) in UTF-8
      "\xe2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
      "\xe2\x80\x99" => "'", // ’ (U+2019) in UTF-8
      "\xe2\x80\x9a" => "'", // ‚ (U+201A) in UTF-8
      "\xe2\x80\x9b" => "'", // ‛ (U+201B) in UTF-8
      "\xe2\x80\x9c" => '"', // “ (U+201C) in UTF-8
      "\xe2\x80\x9d" => '"', // ” (U+201D) in UTF-8
      "\xe2\x80\x9e" => '"', // „ (U+201E) in UTF-8
      "\xe2\x80\x9f" => '"', // ‟ (U+201F) in UTF-8
      "\xe2\x80\xb9" => "'", // ‹ (U+2039) in UTF-8
      "\xe2\x80\xba" => "'", // › (U+203A) in UTF-8
      "\xe2\x80\x93" => '-', // – (U+2013) in UTF-8
      "\xe2\x80\x94" => '-', // — (U+2014) in UTF-8
      "\xe2\x80\xa6" => '...' // … (U+2026) in UTF-8
  );

  protected static $iconvEncoding = array(
      'ANSI_X3.4-1968',
      'ANSI_X3.4-1986',
      'ASCII',
      'CP367',
      'IBM367',
      'ISO-IR-6',
      'ISO646-US',
      'ISO_646.IRV:1991',
      'US',
      'US-ASCII',
      'CSASCII',
      'UTF-8',
      'ISO-10646-UCS-2',
      'UCS-2',
      'CSUNICODE',
      'UCS-2BE',
      'UNICODE-1-1',
      'UNICODEBIG',
      'CSUNICODE11',
      'UCS-2LE',
      'UNICODELITTLE',
      'ISO-10646-UCS-4',
      'UCS-4',
      'CSUCS4',
      'UCS-4BE',
      'UCS-4LE',
      'UTF-16',
      'UTF-16BE',
      'UTF-16LE',
      'UTF-32',
      'UTF-32BE',
      'UTF-32LE',
      'UNICODE-1-1-UTF-7',
      'UTF-7',
      'CSUNICODE11UTF7',
      'UCS-2-INTERNAL',
      'UCS-2-SWAPPED',
      'UCS-4-INTERNAL',
      'UCS-4-SWAPPED',
      'C99',
      'JAVA',
      'CP819',
      'IBM819',
      'ISO-8859-1',
      'ISO-IR-100',
      'ISO8859-1',
      'ISO_8859-1',
      'ISO_8859-1:1987',
      'L1',
      'LATIN1',
      'CSISOLATIN1',
      'ISO-8859-2',
      'ISO-IR-101',
      'ISO8859-2',
      'ISO_8859-2',
      'ISO_8859-2:1987',
      'L2',
      'LATIN2',
      'CSISOLATIN2',
      'ISO-8859-3',
      'ISO-IR-109',
      'ISO8859-3',
      'ISO_8859-3',
      'ISO_8859-3:1988',
      'L3',
      'LATIN3',
      'CSISOLATIN3',
      'ISO-8859-4',
      'ISO-IR-110',
      'ISO8859-4',
      'ISO_8859-4',
      'ISO_8859-4:1988',
      'L4',
      'LATIN4',
      'CSISOLATIN4',
      'CYRILLIC',
      'ISO-8859-5',
      'ISO-IR-144',
      'ISO8859-5',
      'ISO_8859-5',
      'ISO_8859-5:1988',
      'CSISOLATINCYRILLIC',
      'ARABIC',
      'ASMO-708',
      'ECMA-114',
      'ISO-8859-6',
      'ISO-IR-127',
      'ISO8859-6',
      'ISO_8859-6',
      'ISO_8859-6:1987',
      'CSISOLATINARABIC',
      'ECMA-118',
      'ELOT_928',
      'GREEK',
      'GREEK8',
      'ISO-8859-7',
      'ISO-IR-126',
      'ISO8859-7',
      'ISO_8859-7',
      'ISO_8859-7:1987',
      'ISO_8859-7:2003',
      'CSISOLATINGREEK',
      'HEBREW',
      'ISO-8859-8',
      'ISO-IR-138',
      'ISO8859-8',
      'ISO_8859-8',
      'ISO_8859-8:1988',
      'CSISOLATINHEBREW',
      'ISO-8859-9',
      'ISO-IR-148',
      'ISO8859-9',
      'ISO_8859-9',
      'ISO_8859-9:1989',
      'L5',
      'LATIN5',
      'CSISOLATIN5',
      'ISO-8859-10',
      'ISO-IR-157',
      'ISO8859-10',
      'ISO_8859-10',
      'ISO_8859-10:1992',
      'L6',
      'LATIN6',
      'CSISOLATIN6',
      'ISO-8859-11',
      'ISO8859-11',
      'ISO_8859-11',
      'ISO-8859-13',
      'ISO-IR-179',
      'ISO8859-13',
      'ISO_8859-13',
      'L7',
      'LATIN7',
      'ISO-8859-14',
      'ISO-CELTIC',
      'ISO-IR-199',
      'ISO8859-14',
      'ISO_8859-14',
      'ISO_8859-14:1998',
      'L8',
      'LATIN8',
      'ISO-8859-15',
      'ISO-IR-203',
      'ISO8859-15',
      'ISO_8859-15',
      'ISO_8859-15:1998',
      'LATIN-9',
      'ISO-8859-16',
      'ISO-IR-226',
      'ISO8859-16',
      'ISO_8859-16',
      'ISO_8859-16:2001',
      'L10',
      'LATIN10',
      'KOI8-R',
      'CSKOI8R',
      'KOI8-U',
      'KOI8-RU',
      'CP1250',
      'MS-EE',
      'WINDOWS-1250',
      'CP1251',
      'MS-CYRL',
      'WINDOWS-1251',
      'CP1252',
      'MS-ANSI',
      'WINDOWS-1252',
      'CP1253',
      'MS-GREEK',
      'WINDOWS-1253',
      'CP1254',
      'MS-TURK',
      'WINDOWS-1254',
      'CP1255',
      'MS-HEBR',
      'WINDOWS-1255',
      'CP1256',
      'MS-ARAB',
      'WINDOWS-1256',
      'CP1257',
      'WINBALTRIM',
      'WINDOWS-1257',
      'CP1258',
      'WINDOWS-1258',
      '850',
      'CP850',
      'IBM850',
      'CSPC850MULTILINGUAL',
      '862',
      'CP862',
      'IBM862',
      'CSPC862LATINHEBREW',
      '866',
      'CP866',
      'IBM866',
      'CSIBM866',
      'MAC',
      'MACINTOSH',
      'MACROMAN',
      'CSMACINTOSH',
      'MACCENTRALEUROPE',
      'MACICELAND',
      'MACCROATIAN',
      'MACROMANIA',
      'MACCYRILLIC',
      'MACUKRAINE',
      'MACGREEK',
      'MACTURKISH',
      'MACHEBREW',
      'MACARABIC',
      'MACTHAI',
      'HP-ROMAN8',
      'R8',
      'ROMAN8',
      'CSHPROMAN8',
      'NEXTSTEP',
      'ARMSCII-8',
      'GEORGIAN-ACADEMY',
      'GEORGIAN-PS',
      'KOI8-T',
      'CP154',
      'CYRILLIC-ASIAN',
      'PT154',
      'PTCP154',
      'CSPTCP154',
      'KZ-1048',
      'RK1048',
      'STRK1048-2002',
      'CSKZ1048',
      'MULELAO-1',
      'CP1133',
      'IBM-CP1133',
      'ISO-IR-166',
      'TIS-620',
      'TIS620',
      'TIS620-0',
      'TIS620.2529-1',
      'TIS620.2533-0',
      'TIS620.2533-1',
      'CP874',
      'WINDOWS-874',
      'VISCII',
      'VISCII1.1-1',
      'CSVISCII',
      'TCVN',
      'TCVN-5712',
      'TCVN5712-1',
      'TCVN5712-1:1993',
      'ISO-IR-14',
      'ISO646-JP',
      'JIS_C6220-1969-RO',
      'JP',
      'CSISO14JISC6220RO',
      'JISX0201-1976',
      'JIS_X0201',
      'X0201',
      'CSHALFWIDTHKATAKANA',
      'ISO-IR-87',
      'JIS0208',
      'JIS_C6226-1983',
      'JIS_X0208',
      'JIS_X0208-1983',
      'JIS_X0208-1990',
      'X0208',
      'CSISO87JISX0208',
      'ISO-IR-159',
      'JIS_X0212',
      'JIS_X0212-1990',
      'JIS_X0212.1990-0',
      'X0212',
      'CSISO159JISX02121990',
      'CN',
      'GB_1988-80',
      'ISO-IR-57',
      'ISO646-CN',
      'CSISO57GB1988',
      'CHINESE',
      'GB_2312-80',
      'ISO-IR-58',
      'CSISO58GB231280',
      'CN-GB-ISOIR165',
      'ISO-IR-165',
      'ISO-IR-149',
      'KOREAN',
      'KSC_5601',
      'KS_C_5601-1987',
      'KS_C_5601-1989',
      'CSKSC56011987',
      'EUC-JP',
      'EUCJP',
      'EXTENDED_UNIX_CODE_PACKED_FORMAT_FOR_JAPANESE',
      'CSEUCPKDFMTJAPANESE',
      'MS_KANJI',
      'SHIFT-JIS',
      'SHIFT_JIS',
      'SJIS',
      'CSSHIFTJIS',
      'CP932',
      'ISO-2022-JP',
      'CSISO2022JP',
      'ISO-2022-JP-1',
      'ISO-2022-JP-2',
      'CSISO2022JP2',
      'CN-GB',
      'EUC-CN',
      'EUCCN',
      'GB2312',
      'CSGB2312',
      'GBK',
      'CP936',
      'MS936',
      'WINDOWS-936',
      'GB18030',
      'ISO-2022-CN',
      'CSISO2022CN',
      'ISO-2022-CN-EXT',
      'HZ',
      'HZ-GB-2312',
      'EUC-TW',
      'EUCTW',
      'CSEUCTW',
      'BIG-5',
      'BIG-FIVE',
      'BIG5',
      'BIGFIVE',
      'CN-BIG5',
      'CSBIG5',
      'CP950',
      'BIG5-HKSCS:1999',
      'BIG5-HKSCS:2001',
      'BIG5-HKSCS',
      'BIG5-HKSCS:2004',
      'BIG5HKSCS',
      'EUC-KR',
      'EUCKR',
      'CSEUCKR',
      'CP949',
      'UHC',
      'CP1361',
      'JOHAB',
      'ISO-2022-KR',
      'CSISO2022KR',
      'CP856',
      'CP922',
      'CP943',
      'CP1046',
      'CP1124',
      'CP1129',
      'CP1161',
      'IBM-1161',
      'IBM1161',
      'CSIBM1161',
      'CP1162',
      'IBM-1162',
      'IBM1162',
      'CSIBM1162',
      'CP1163',
      'IBM-1163',
      'IBM1163',
      'CSIBM1163',
      'DEC-KANJI',
      'DEC-HANYU',
      '437',
      'CP437',
      'IBM437',
      'CSPC8CODEPAGE437',
      'CP737',
      'CP775',
      'IBM775',
      'CSPC775BALTIC',
      '852',
      'CP852',
      'IBM852',
      'CSPCP852',
      'CP853',
      '855',
      'CP855',
      'IBM855',
      'CSIBM855',
      '857',
      'CP857',
      'IBM857',
      'CSIBM857',
      'CP858',
      '860',
      'CP860',
      'IBM860',
      'CSIBM860',
      '861',
      'CP-IS',
      'CP861',
      'IBM861',
      'CSIBM861',
      '863',
      'CP863',
      'IBM863',
      'CSIBM863',
      'CP864',
      'IBM864',
      'CSIBM864',
      '865',
      'CP865',
      'IBM865',
      'CSIBM865',
      '869',
      'CP-GR',
      'CP869',
      'IBM869',
      'CSIBM869',
      'CP1125',
      'EUC-JISX0213',
      'SHIFT_JISX0213',
      'ISO-2022-JP-3',
      'BIG5-2003',
      'ISO-IR-230',
      'TDS565',
      'ATARI',
      'ATARIST',
      'RISCOS-LATIN1',
  );

  /**
   * @var array
   */
  private static $support = array();

  /**
   * __construct()
   */
  public function __construct()
  {
    self::checkForSupport();
  }

  /**
   * Return the character at the specified position: $str[1] like functionality.
   *
   * @param    string $str A UTF-8 string.
   * @param    int    $pos The position of character to return.
   *
   * @return   string Single Multi-Byte character.
   */
  public static function access($str, $pos)
  {
    return self::substr($str, $pos, 1);
  }

  /**
   * Prepends UTF-8 BOM character to the string and returns the whole string.
   *
   * INFO: If BOM already existed there, the Input string is returned.
   *
   * @param    string $str The input string
   *
   * @return   string The output string that contains BOM
   */
  public static function add_bom_to_string($str)
  {
    if (self::string_has_bom($str) === false) {
      $str = self::bom() . $str;
    }

    return $str;
  }

  /**
   * Returns the UTF-8 Byte Order Mark Character.
   *
   * @return string UTF-8 Byte Order Mark
   */
  public static function bom()
  {
    return "\xEF\xBB\xBF";
  }

  /**
   * @alias of UTF8::chr_map()
   *
   * @param string|array $callback
   * @param string       $str
   *
   * @return array
   */
  public static function callback($callback, $str)
  {
    return self::chr_map($callback, $str);
  }

  /**
   * Returns an array of all lower and upper case UTF-8 encoded characters.
   *
   * @return   string An array with lower case chars as keys and upper chars as values.
   */
  protected static function case_table()
  {
    static $case = array(

      // lower => upper
      "\xf0\x90\x91\x8f" => "\xf0\x90\x90\xa7",
      "\xf0\x90\x91\x8e" => "\xf0\x90\x90\xa6",
      "\xf0\x90\x91\x8d" => "\xf0\x90\x90\xa5",
      "\xf0\x90\x91\x8c" => "\xf0\x90\x90\xa4",
      "\xf0\x90\x91\x8b" => "\xf0\x90\x90\xa3",
      "\xf0\x90\x91\x8a" => "\xf0\x90\x90\xa2",
      "\xf0\x90\x91\x89" => "\xf0\x90\x90\xa1",
      "\xf0\x90\x91\x88" => "\xf0\x90\x90\xa0",
      "\xf0\x90\x91\x87" => "\xf0\x90\x90\x9f",
      "\xf0\x90\x91\x86" => "\xf0\x90\x90\x9e",
      "\xf0\x90\x91\x85" => "\xf0\x90\x90\x9d",
      "\xf0\x90\x91\x84" => "\xf0\x90\x90\x9c",
      "\xf0\x90\x91\x83" => "\xf0\x90\x90\x9b",
      "\xf0\x90\x91\x82" => "\xf0\x90\x90\x9a",
      "\xf0\x90\x91\x81" => "\xf0\x90\x90\x99",
      "\xf0\x90\x91\x80" => "\xf0\x90\x90\x98",
      "\xf0\x90\x90\xbf" => "\xf0\x90\x90\x97",
      "\xf0\x90\x90\xbe" => "\xf0\x90\x90\x96",
      "\xf0\x90\x90\xbd" => "\xf0\x90\x90\x95",
      "\xf0\x90\x90\xbc" => "\xf0\x90\x90\x94",
      "\xf0\x90\x90\xbb" => "\xf0\x90\x90\x93",
      "\xf0\x90\x90\xba" => "\xf0\x90\x90\x92",
      "\xf0\x90\x90\xb9" => "\xf0\x90\x90\x91",
      "\xf0\x90\x90\xb8" => "\xf0\x90\x90\x90",
      "\xf0\x90\x90\xb7" => "\xf0\x90\x90\x8f",
      "\xf0\x90\x90\xb6" => "\xf0\x90\x90\x8e",
      "\xf0\x90\x90\xb5" => "\xf0\x90\x90\x8d",
      "\xf0\x90\x90\xb4" => "\xf0\x90\x90\x8c",
      "\xf0\x90\x90\xb3" => "\xf0\x90\x90\x8b",
      "\xf0\x90\x90\xb2" => "\xf0\x90\x90\x8a",
      "\xf0\x90\x90\xb1" => "\xf0\x90\x90\x89",
      "\xf0\x90\x90\xb0" => "\xf0\x90\x90\x88",
      "\xf0\x90\x90\xaf" => "\xf0\x90\x90\x87",
      "\xf0\x90\x90\xae" => "\xf0\x90\x90\x86",
      "\xf0\x90\x90\xad" => "\xf0\x90\x90\x85",
      "\xf0\x90\x90\xac" => "\xf0\x90\x90\x84",
      "\xf0\x90\x90\xab" => "\xf0\x90\x90\x83",
      "\xf0\x90\x90\xaa" => "\xf0\x90\x90\x82",
      "\xf0\x90\x90\xa9" => "\xf0\x90\x90\x81",
      "\xf0\x90\x90\xa8" => "\xf0\x90\x90\x80",
      "\xef\xbd\x9a"     => "\xef\xbc\xba",
      "\xef\xbd\x99"     => "\xef\xbc\xb9",
      "\xef\xbd\x98"     => "\xef\xbc\xb8",
      "\xef\xbd\x97"     => "\xef\xbc\xb7",
      "\xef\xbd\x96"     => "\xef\xbc\xb6",
      "\xef\xbd\x95"     => "\xef\xbc\xb5",
      "\xef\xbd\x94"     => "\xef\xbc\xb4",
      "\xef\xbd\x93"     => "\xef\xbc\xb3",
      "\xef\xbd\x92"     => "\xef\xbc\xb2",
      "\xef\xbd\x91"     => "\xef\xbc\xb1",
      "\xef\xbd\x90"     => "\xef\xbc\xb0",
      "\xef\xbd\x8f"     => "\xef\xbc\xaf",
      "\xef\xbd\x8e"     => "\xef\xbc\xae",
      "\xef\xbd\x8d"     => "\xef\xbc\xad",
      "\xef\xbd\x8c"     => "\xef\xbc\xac",
      "\xef\xbd\x8b"     => "\xef\xbc\xab",
      "\xef\xbd\x8a"     => "\xef\xbc\xaa",
      "\xef\xbd\x89"     => "\xef\xbc\xa9",
      "\xef\xbd\x88"     => "\xef\xbc\xa8",
      "\xef\xbd\x87"     => "\xef\xbc\xa7",
      "\xef\xbd\x86"     => "\xef\xbc\xa6",
      "\xef\xbd\x85"     => "\xef\xbc\xa5",
      "\xef\xbd\x84"     => "\xef\xbc\xa4",
      "\xef\xbd\x83"     => "\xef\xbc\xa3",
      "\xef\xbd\x82"     => "\xef\xbc\xa2",
      "\xef\xbd\x81"     => "\xef\xbc\xa1",
      "\xea\x9e\x8c"     => "\xea\x9e\x8b",
      "\xea\x9e\x87"     => "\xea\x9e\x86",
      "\xea\x9e\x85"     => "\xea\x9e\x84",
      "\xea\x9e\x83"     => "\xea\x9e\x82",
      "\xea\x9e\x81"     => "\xea\x9e\x80",
      "\xea\x9d\xbf"     => "\xea\x9d\xbe",
      "\xea\x9d\xbc"     => "\xea\x9d\xbb",
      "\xea\x9d\xba"     => "\xea\x9d\xb9",
      "\xea\x9d\xaf"     => "\xea\x9d\xae",
      "\xea\x9d\xad"     => "\xea\x9d\xac",
      "\xea\x9d\xab"     => "\xea\x9d\xaa",
      "\xea\x9d\xa9"     => "\xea\x9d\xa8",
      "\xea\x9d\xa7"     => "\xea\x9d\xa6",
      "\xea\x9d\xa5"     => "\xea\x9d\xa4",
      "\xea\x9d\xa3"     => "\xea\x9d\xa2",
      "\xea\x9d\xa1"     => "\xea\x9d\xa0",
      "\xea\x9d\x9f"     => "\xea\x9d\x9e",
      "\xea\x9d\x9d"     => "\xea\x9d\x9c",
      "\xea\x9d\x9b"     => "\xea\x9d\x9a",
      "\xea\x9d\x99"     => "\xea\x9d\x98",
      "\xea\x9d\x97"     => "\xea\x9d\x96",
      "\xea\x9d\x95"     => "\xea\x9d\x94",
      "\xea\x9d\x93"     => "\xea\x9d\x92",
      "\xea\x9d\x91"     => "\xea\x9d\x90",
      "\xea\x9d\x8f"     => "\xea\x9d\x8e",
      "\xea\x9d\x8d"     => "\xea\x9d\x8c",
      "\xea\x9d\x8b"     => "\xea\x9d\x8a",
      "\xea\x9d\x89"     => "\xea\x9d\x88",
      "\xea\x9d\x87"     => "\xea\x9d\x86",
      "\xea\x9d\x85"     => "\xea\x9d\x84",
      "\xea\x9d\x83"     => "\xea\x9d\x82",
      "\xea\x9d\x81"     => "\xea\x9d\x80",
      "\xea\x9c\xbf"     => "\xea\x9c\xbe",
      "\xea\x9c\xbd"     => "\xea\x9c\xbc",
      "\xea\x9c\xbb"     => "\xea\x9c\xba",
      "\xea\x9c\xb9"     => "\xea\x9c\xb8",
      "\xea\x9c\xb7"     => "\xea\x9c\xb6",
      "\xea\x9c\xb5"     => "\xea\x9c\xb4",
      "\xea\x9c\xb3"     => "\xea\x9c\xb2",
      "\xea\x9c\xaf"     => "\xea\x9c\xae",
      "\xea\x9c\xad"     => "\xea\x9c\xac",
      "\xea\x9c\xab"     => "\xea\x9c\xaa",
      "\xea\x9c\xa9"     => "\xea\x9c\xa8",
      "\xea\x9c\xa7"     => "\xea\x9c\xa6",
      "\xea\x9c\xa5"     => "\xea\x9c\xa4",
      "\xea\x9c\xa3"     => "\xea\x9c\xa2",
      "\xea\x9a\x97"     => "\xea\x9a\x96",
      "\xea\x9a\x95"     => "\xea\x9a\x94",
      "\xea\x9a\x93"     => "\xea\x9a\x92",
      "\xea\x9a\x91"     => "\xea\x9a\x90",
      "\xea\x9a\x8f"     => "\xea\x9a\x8e",
      "\xea\x9a\x8d"     => "\xea\x9a\x8c",
      "\xea\x9a\x8b"     => "\xea\x9a\x8a",
      "\xea\x9a\x89"     => "\xea\x9a\x88",
      "\xea\x9a\x87"     => "\xea\x9a\x86",
      "\xea\x9a\x85"     => "\xea\x9a\x84",
      "\xea\x9a\x83"     => "\xea\x9a\x82",
      "\xea\x9a\x81"     => "\xea\x9a\x80",
      "\xea\x99\xad"     => "\xea\x99\xac",
      "\xea\x99\xab"     => "\xea\x99\xaa",
      "\xea\x99\xa9"     => "\xea\x99\xa8",
      "\xea\x99\xa7"     => "\xea\x99\xa6",
      "\xea\x99\xa5"     => "\xea\x99\xa4",
      "\xea\x99\xa3"     => "\xea\x99\xa2",
      "\xea\x99\x9f"     => "\xea\x99\x9e",
      "\xea\x99\x9d"     => "\xea\x99\x9c",
      "\xea\x99\x9b"     => "\xea\x99\x9a",
      "\xea\x99\x99"     => "\xea\x99\x98",
      "\xea\x99\x97"     => "\xea\x99\x96",
      "\xea\x99\x95"     => "\xea\x99\x94",
      "\xea\x99\x93"     => "\xea\x99\x92",
      "\xea\x99\x91"     => "\xea\x99\x90",
      "\xea\x99\x8f"     => "\xea\x99\x8e",
      "\xea\x99\x8d"     => "\xea\x99\x8c",
      "\xea\x99\x8b"     => "\xea\x99\x8a",
      "\xea\x99\x89"     => "\xea\x99\x88",
      "\xea\x99\x87"     => "\xea\x99\x86",
      "\xea\x99\x85"     => "\xea\x99\x84",
      "\xea\x99\x83"     => "\xea\x99\x82",
      "\xea\x99\x81"     => "\xea\x99\x80",
      "\xe2\xb4\xa5"     => "\xe1\x83\x85",
      "\xe2\xb4\xa4"     => "\xe1\x83\x84",
      "\xe2\xb4\xa3"     => "\xe1\x83\x83",
      "\xe2\xb4\xa2"     => "\xe1\x83\x82",
      "\xe2\xb4\xa1"     => "\xe1\x83\x81",
      "\xe2\xb4\xa0"     => "\xe1\x83\x80",
      "\xe2\xb4\x9f"     => "\xe1\x82\xbf",
      "\xe2\xb4\x9e"     => "\xe1\x82\xbe",
      "\xe2\xb4\x9d"     => "\xe1\x82\xbd",
      "\xe2\xb4\x9c"     => "\xe1\x82\xbc",
      "\xe2\xb4\x9b"     => "\xe1\x82\xbb",
      "\xe2\xb4\x9a"     => "\xe1\x82\xba",
      "\xe2\xb4\x99"     => "\xe1\x82\xb9",
      "\xe2\xb4\x98"     => "\xe1\x82\xb8",
      "\xe2\xb4\x97"     => "\xe1\x82\xb7",
      "\xe2\xb4\x96"     => "\xe1\x82\xb6",
      "\xe2\xb4\x95"     => "\xe1\x82\xb5",
      "\xe2\xb4\x94"     => "\xe1\x82\xb4",
      "\xe2\xb4\x93"     => "\xe1\x82\xb3",
      "\xe2\xb4\x92"     => "\xe1\x82\xb2",
      "\xe2\xb4\x91"     => "\xe1\x82\xb1",
      "\xe2\xb4\x90"     => "\xe1\x82\xb0",
      "\xe2\xb4\x8f"     => "\xe1\x82\xaf",
      "\xe2\xb4\x8e"     => "\xe1\x82\xae",
      "\xe2\xb4\x8d"     => "\xe1\x82\xad",
      "\xe2\xb4\x8c"     => "\xe1\x82\xac",
      "\xe2\xb4\x8b"     => "\xe1\x82\xab",
      "\xe2\xb4\x8a"     => "\xe1\x82\xaa",
      "\xe2\xb4\x89"     => "\xe1\x82\xa9",
      "\xe2\xb4\x88"     => "\xe1\x82\xa8",
      "\xe2\xb4\x87"     => "\xe1\x82\xa7",
      "\xe2\xb4\x86"     => "\xe1\x82\xa6",
      "\xe2\xb4\x85"     => "\xe1\x82\xa5",
      "\xe2\xb4\x84"     => "\xe1\x82\xa4",
      "\xe2\xb4\x83"     => "\xe1\x82\xa3",
      "\xe2\xb4\x82"     => "\xe1\x82\xa2",
      "\xe2\xb4\x81"     => "\xe1\x82\xa1",
      "\xe2\xb4\x80"     => "\xe1\x82\xa0",
      "\xe2\xb3\xae"     => "\xe2\xb3\xad",
      "\xe2\xb3\xac"     => "\xe2\xb3\xab",
      "\xe2\xb3\xa3"     => "\xe2\xb3\xa2",
      "\xe2\xb3\xa1"     => "\xe2\xb3\xa0",
      "\xe2\xb3\x9f"     => "\xe2\xb3\x9e",
      "\xe2\xb3\x9d"     => "\xe2\xb3\x9c",
      "\xe2\xb3\x9b"     => "\xe2\xb3\x9a",
      "\xe2\xb3\x99"     => "\xe2\xb3\x98",
      "\xe2\xb3\x97"     => "\xe2\xb3\x96",
      "\xe2\xb3\x95"     => "\xe2\xb3\x94",
      "\xe2\xb3\x93"     => "\xe2\xb3\x92",
      "\xe2\xb3\x91"     => "\xe2\xb3\x90",
      "\xe2\xb3\x8f"     => "\xe2\xb3\x8e",
      "\xe2\xb3\x8d"     => "\xe2\xb3\x8c",
      "\xe2\xb3\x8b"     => "\xe2\xb3\x8a",
      "\xe2\xb3\x89"     => "\xe2\xb3\x88",
      "\xe2\xb3\x87"     => "\xe2\xb3\x86",
      "\xe2\xb3\x85"     => "\xe2\xb3\x84",
      "\xe2\xb3\x83"     => "\xe2\xb3\x82",
      "\xe2\xb3\x81"     => "\xe2\xb3\x80",
      "\xe2\xb2\xbf"     => "\xe2\xb2\xbe",
      "\xe2\xb2\xbd"     => "\xe2\xb2\xbc",
      "\xe2\xb2\xbb"     => "\xe2\xb2\xba",
      "\xe2\xb2\xb9"     => "\xe2\xb2\xb8",
      "\xe2\xb2\xb7"     => "\xe2\xb2\xb6",
      "\xe2\xb2\xb5"     => "\xe2\xb2\xb4",
      "\xe2\xb2\xb3"     => "\xe2\xb2\xb2",
      "\xe2\xb2\xb1"     => "\xe2\xb2\xb0",
      "\xe2\xb2\xaf"     => "\xe2\xb2\xae",
      "\xe2\xb2\xad"     => "\xe2\xb2\xac",
      "\xe2\xb2\xab"     => "\xe2\xb2\xaa",
      "\xe2\xb2\xa9"     => "\xe2\xb2\xa8",
      "\xe2\xb2\xa7"     => "\xe2\xb2\xa6",
      "\xe2\xb2\xa5"     => "\xe2\xb2\xa4",
      "\xe2\xb2\xa3"     => "\xe2\xb2\xa2",
      "\xe2\xb2\xa1"     => "\xe2\xb2\xa0",
      "\xe2\xb2\x9f"     => "\xe2\xb2\x9e",
      "\xe2\xb2\x9d"     => "\xe2\xb2\x9c",
      "\xe2\xb2\x9b"     => "\xe2\xb2\x9a",
      "\xe2\xb2\x99"     => "\xe2\xb2\x98",
      "\xe2\xb2\x97"     => "\xe2\xb2\x96",
      "\xe2\xb2\x95"     => "\xe2\xb2\x94",
      "\xe2\xb2\x93"     => "\xe2\xb2\x92",
      "\xe2\xb2\x91"     => "\xe2\xb2\x90",
      "\xe2\xb2\x8f"     => "\xe2\xb2\x8e",
      "\xe2\xb2\x8d"     => "\xe2\xb2\x8c",
      "\xe2\xb2\x8b"     => "\xe2\xb2\x8a",
      "\xe2\xb2\x89"     => "\xe2\xb2\x88",
      "\xe2\xb2\x87"     => "\xe2\xb2\x86",
      "\xe2\xb2\x85"     => "\xe2\xb2\x84",
      "\xe2\xb2\x83"     => "\xe2\xb2\x82",
      "\xe2\xb2\x81"     => "\xe2\xb2\x80",
      "\xe2\xb1\xb6"     => "\xe2\xb1\xb5",
      "\xe2\xb1\xb3"     => "\xe2\xb1\xb2",
      "\xe2\xb1\xac"     => "\xe2\xb1\xab",
      "\xe2\xb1\xaa"     => "\xe2\xb1\xa9",
      "\xe2\xb1\xa8"     => "\xe2\xb1\xa7",
      "\xe2\xb1\xa6"     => "\xc8\xbe",
      "\xe2\xb1\xa5"     => "\xc8\xba",
      "\xe2\xb1\xa1"     => "\xe2\xb1\xa0",
      "\xe2\xb1\x9e"     => "\xe2\xb0\xae",
      "\xe2\xb1\x9d"     => "\xe2\xb0\xad",
      "\xe2\xb1\x9c"     => "\xe2\xb0\xac",
      "\xe2\xb1\x9b"     => "\xe2\xb0\xab",
      "\xe2\xb1\x9a"     => "\xe2\xb0\xaa",
      "\xe2\xb1\x99"     => "\xe2\xb0\xa9",
      "\xe2\xb1\x98"     => "\xe2\xb0\xa8",
      "\xe2\xb1\x97"     => "\xe2\xb0\xa7",
      "\xe2\xb1\x96"     => "\xe2\xb0\xa6",
      "\xe2\xb1\x95"     => "\xe2\xb0\xa5",
      "\xe2\xb1\x94"     => "\xe2\xb0\xa4",
      "\xe2\xb1\x93"     => "\xe2\xb0\xa3",
      "\xe2\xb1\x92"     => "\xe2\xb0\xa2",
      "\xe2\xb1\x91"     => "\xe2\xb0\xa1",
      "\xe2\xb1\x90"     => "\xe2\xb0\xa0",
      "\xe2\xb1\x8f"     => "\xe2\xb0\x9f",
      "\xe2\xb1\x8e"     => "\xe2\xb0\x9e",
      "\xe2\xb1\x8d"     => "\xe2\xb0\x9d",
      "\xe2\xb1\x8c"     => "\xe2\xb0\x9c",
      "\xe2\xb1\x8b"     => "\xe2\xb0\x9b",
      "\xe2\xb1\x8a"     => "\xe2\xb0\x9a",
      "\xe2\xb1\x89"     => "\xe2\xb0\x99",
      "\xe2\xb1\x88"     => "\xe2\xb0\x98",
      "\xe2\xb1\x87"     => "\xe2\xb0\x97",
      "\xe2\xb1\x86"     => "\xe2\xb0\x96",
      "\xe2\xb1\x85"     => "\xe2\xb0\x95",
      "\xe2\xb1\x84"     => "\xe2\xb0\x94",
      "\xe2\xb1\x83"     => "\xe2\xb0\x93",
      "\xe2\xb1\x82"     => "\xe2\xb0\x92",
      "\xe2\xb1\x81"     => "\xe2\xb0\x91",
      "\xe2\xb1\x80"     => "\xe2\xb0\x90",
      "\xe2\xb0\xbf"     => "\xe2\xb0\x8f",
      "\xe2\xb0\xbe"     => "\xe2\xb0\x8e",
      "\xe2\xb0\xbd"     => "\xe2\xb0\x8d",
      "\xe2\xb0\xbc"     => "\xe2\xb0\x8c",
      "\xe2\xb0\xbb"     => "\xe2\xb0\x8b",
      "\xe2\xb0\xba"     => "\xe2\xb0\x8a",
      "\xe2\xb0\xb9"     => "\xe2\xb0\x89",
      "\xe2\xb0\xb8"     => "\xe2\xb0\x88",
      "\xe2\xb0\xb7"     => "\xe2\xb0\x87",
      "\xe2\xb0\xb6"     => "\xe2\xb0\x86",
      "\xe2\xb0\xb5"     => "\xe2\xb0\x85",
      "\xe2\xb0\xb4"     => "\xe2\xb0\x84",
      "\xe2\xb0\xb3"     => "\xe2\xb0\x83",
      "\xe2\xb0\xb2"     => "\xe2\xb0\x82",
      "\xe2\xb0\xb1"     => "\xe2\xb0\x81",
      "\xe2\xb0\xb0"     => "\xe2\xb0\x80",
      "\xe2\x86\x84"     => "\xe2\x86\x83",
      "\xe2\x85\x8e"     => "\xe2\x84\xb2",
      "\xe1\xbf\xb3"     => "\xe1\xbf\xbc",
      "\xe1\xbf\xa5"     => "\xe1\xbf\xac",
      "\xe1\xbf\xa1"     => "\xe1\xbf\xa9",
      "\xe1\xbf\xa0"     => "\xe1\xbf\xa8",
      "\xe1\xbf\x91"     => "\xe1\xbf\x99",
      "\xe1\xbf\x90"     => "\xe1\xbf\x98",
      "\xe1\xbf\x83"     => "\xe1\xbf\x8c",
      "\xe1\xbe\xbe"     => "\xce\x99",
      "\xe1\xbe\xb3"     => "\xe1\xbe\xbc",
      "\xe1\xbe\xb1"     => "\xe1\xbe\xb9",
      "\xe1\xbe\xb0"     => "\xe1\xbe\xb8",
      "\xe1\xbe\xa7"     => "\xe1\xbe\xaf",
      "\xe1\xbe\xa6"     => "\xe1\xbe\xae",
      "\xe1\xbe\xa5"     => "\xe1\xbe\xad",
      "\xe1\xbe\xa4"     => "\xe1\xbe\xac",
      "\xe1\xbe\xa3"     => "\xe1\xbe\xab",
      "\xe1\xbe\xa2"     => "\xe1\xbe\xaa",
      "\xe1\xbe\xa1"     => "\xe1\xbe\xa9",
      "\xe1\xbe\xa0"     => "\xe1\xbe\xa8",
      "\xe1\xbe\x97"     => "\xe1\xbe\x9f",
      "\xe1\xbe\x96"     => "\xe1\xbe\x9e",
      "\xe1\xbe\x95"     => "\xe1\xbe\x9d",
      "\xe1\xbe\x94"     => "\xe1\xbe\x9c",
      "\xe1\xbe\x93"     => "\xe1\xbe\x9b",
      "\xe1\xbe\x92"     => "\xe1\xbe\x9a",
      "\xe1\xbe\x91"     => "\xe1\xbe\x99",
      "\xe1\xbe\x90"     => "\xe1\xbe\x98",
      "\xe1\xbe\x87"     => "\xe1\xbe\x8f",
      "\xe1\xbe\x86"     => "\xe1\xbe\x8e",
      "\xe1\xbe\x85"     => "\xe1\xbe\x8d",
      "\xe1\xbe\x84"     => "\xe1\xbe\x8c",
      "\xe1\xbe\x83"     => "\xe1\xbe\x8b",
      "\xe1\xbe\x82"     => "\xe1\xbe\x8a",
      "\xe1\xbe\x81"     => "\xe1\xbe\x89",
      "\xe1\xbe\x80"     => "\xe1\xbe\x88",
      "\xe1\xbd\xbd"     => "\xe1\xbf\xbb",
      "\xe1\xbd\xbc"     => "\xe1\xbf\xba",
      "\xe1\xbd\xbb"     => "\xe1\xbf\xab",
      "\xe1\xbd\xba"     => "\xe1\xbf\xaa",
      "\xe1\xbd\xb9"     => "\xe1\xbf\xb9",
      "\xe1\xbd\xb8"     => "\xe1\xbf\xb8",
      "\xe1\xbd\xb7"     => "\xe1\xbf\x9b",
      "\xe1\xbd\xb6"     => "\xe1\xbf\x9a",
      "\xe1\xbd\xb5"     => "\xe1\xbf\x8b",
      "\xe1\xbd\xb4"     => "\xe1\xbf\x8a",
      "\xe1\xbd\xb3"     => "\xe1\xbf\x89",
      "\xe1\xbd\xb2"     => "\xe1\xbf\x88",
      "\xe1\xbd\xb1"     => "\xe1\xbe\xbb",
      "\xe1\xbd\xb0"     => "\xe1\xbe\xba",
      "\xe1\xbd\xa7"     => "\xe1\xbd\xaf",
      "\xe1\xbd\xa6"     => "\xe1\xbd\xae",
      "\xe1\xbd\xa5"     => "\xe1\xbd\xad",
      "\xe1\xbd\xa4"     => "\xe1\xbd\xac",
      "\xe1\xbd\xa3"     => "\xe1\xbd\xab",
      "\xe1\xbd\xa2"     => "\xe1\xbd\xaa",
      "\xe1\xbd\xa1"     => "\xe1\xbd\xa9",
      "\xe1\xbd\xa0"     => "\xe1\xbd\xa8",
      "\xe1\xbd\x97"     => "\xe1\xbd\x9f",
      "\xe1\xbd\x95"     => "\xe1\xbd\x9d",
      "\xe1\xbd\x93"     => "\xe1\xbd\x9b",
      "\xe1\xbd\x91"     => "\xe1\xbd\x99",
      "\xe1\xbd\x85"     => "\xe1\xbd\x8d",
      "\xe1\xbd\x84"     => "\xe1\xbd\x8c",
      "\xe1\xbd\x83"     => "\xe1\xbd\x8b",
      "\xe1\xbd\x82"     => "\xe1\xbd\x8a",
      "\xe1\xbd\x81"     => "\xe1\xbd\x89",
      "\xe1\xbd\x80"     => "\xe1\xbd\x88",
      "\xe1\xbc\xb7"     => "\xe1\xbc\xbf",
      "\xe1\xbc\xb6"     => "\xe1\xbc\xbe",
      "\xe1\xbc\xb5"     => "\xe1\xbc\xbd",
      "\xe1\xbc\xb4"     => "\xe1\xbc\xbc",
      "\xe1\xbc\xb3"     => "\xe1\xbc\xbb",
      "\xe1\xbc\xb2"     => "\xe1\xbc\xba",
      "\xe1\xbc\xb1"     => "\xe1\xbc\xb9",
      "\xe1\xbc\xb0"     => "\xe1\xbc\xb8",
      "\xe1\xbc\xa7"     => "\xe1\xbc\xaf",
      "\xe1\xbc\xa6"     => "\xe1\xbc\xae",
      "\xe1\xbc\xa5"     => "\xe1\xbc\xad",
      "\xe1\xbc\xa4"     => "\xe1\xbc\xac",
      "\xe1\xbc\xa3"     => "\xe1\xbc\xab",
      "\xe1\xbc\xa2"     => "\xe1\xbc\xaa",
      "\xe1\xbc\xa1"     => "\xe1\xbc\xa9",
      "\xe1\xbc\xa0"     => "\xe1\xbc\xa8",
      "\xe1\xbc\x95"     => "\xe1\xbc\x9d",
      "\xe1\xbc\x94"     => "\xe1\xbc\x9c",
      "\xe1\xbc\x93"     => "\xe1\xbc\x9b",
      "\xe1\xbc\x92"     => "\xe1\xbc\x9a",
      "\xe1\xbc\x91"     => "\xe1\xbc\x99",
      "\xe1\xbc\x90"     => "\xe1\xbc\x98",
      "\xe1\xbc\x87"     => "\xe1\xbc\x8f",
      "\xe1\xbc\x86"     => "\xe1\xbc\x8e",
      "\xe1\xbc\x85"     => "\xe1\xbc\x8d",
      "\xe1\xbc\x84"     => "\xe1\xbc\x8c",
      "\xe1\xbc\x83"     => "\xe1\xbc\x8b",
      "\xe1\xbc\x82"     => "\xe1\xbc\x8a",
      "\xe1\xbc\x81"     => "\xe1\xbc\x89",
      "\xe1\xbc\x80"     => "\xe1\xbc\x88",
      "\xe1\xbb\xbf"     => "\xe1\xbb\xbe",
      "\xe1\xbb\xbd"     => "\xe1\xbb\xbc",
      "\xe1\xbb\xbb"     => "\xe1\xbb\xba",
      "\xe1\xbb\xb9"     => "\xe1\xbb\xb8",
      "\xe1\xbb\xb7"     => "\xe1\xbb\xb6",
      "\xe1\xbb\xb5"     => "\xe1\xbb\xb4",
      "\xe1\xbb\xb3"     => "\xe1\xbb\xb2",
      "\xe1\xbb\xb1"     => "\xe1\xbb\xb0",
      "\xe1\xbb\xaf"     => "\xe1\xbb\xae",
      "\xe1\xbb\xad"     => "\xe1\xbb\xac",
      "\xe1\xbb\xab"     => "\xe1\xbb\xaa",
      "\xe1\xbb\xa9"     => "\xe1\xbb\xa8",
      "\xe1\xbb\xa7"     => "\xe1\xbb\xa6",
      "\xe1\xbb\xa5"     => "\xe1\xbb\xa4",
      "\xe1\xbb\xa3"     => "\xe1\xbb\xa2",
      "\xe1\xbb\xa1"     => "\xe1\xbb\xa0",
      "\xe1\xbb\x9f"     => "\xe1\xbb\x9e",
      "\xe1\xbb\x9d"     => "\xe1\xbb\x9c",
      "\xe1\xbb\x9b"     => "\xe1\xbb\x9a",
      "\xe1\xbb\x99"     => "\xe1\xbb\x98",
      "\xe1\xbb\x97"     => "\xe1\xbb\x96",
      "\xe1\xbb\x95"     => "\xe1\xbb\x94",
      "\xe1\xbb\x93"     => "\xe1\xbb\x92",
      "\xe1\xbb\x91"     => "\xe1\xbb\x90",
      "\xe1\xbb\x8f"     => "\xe1\xbb\x8e",
      "\xe1\xbb\x8d"     => "\xe1\xbb\x8c",
      "\xe1\xbb\x8b"     => "\xe1\xbb\x8a",
      "\xe1\xbb\x89"     => "\xe1\xbb\x88",
      "\xe1\xbb\x87"     => "\xe1\xbb\x86",
      "\xe1\xbb\x85"     => "\xe1\xbb\x84",
      "\xe1\xbb\x83"     => "\xe1\xbb\x82",
      "\xe1\xbb\x81"     => "\xe1\xbb\x80",
      "\xe1\xba\xbf"     => "\xe1\xba\xbe",
      "\xe1\xba\xbd"     => "\xe1\xba\xbc",
      "\xe1\xba\xbb"     => "\xe1\xba\xba",
      "\xe1\xba\xb9"     => "\xe1\xba\xb8",
      "\xe1\xba\xb7"     => "\xe1\xba\xb6",
      "\xe1\xba\xb5"     => "\xe1\xba\xb4",
      "\xe1\xba\xb3"     => "\xe1\xba\xb2",
      "\xe1\xba\xb1"     => "\xe1\xba\xb0",
      "\xe1\xba\xaf"     => "\xe1\xba\xae",
      "\xe1\xba\xad"     => "\xe1\xba\xac",
      "\xe1\xba\xab"     => "\xe1\xba\xaa",
      "\xe1\xba\xa9"     => "\xe1\xba\xa8",
      "\xe1\xba\xa7"     => "\xe1\xba\xa6",
      "\xe1\xba\xa5"     => "\xe1\xba\xa4",
      "\xe1\xba\xa3"     => "\xe1\xba\xa2",
      "\xe1\xba\xa1"     => "\xe1\xba\xa0",
      "\xe1\xba\x9b"     => "\xe1\xb9\xa0",
      "\xe1\xba\x95"     => "\xe1\xba\x94",
      "\xe1\xba\x93"     => "\xe1\xba\x92",
      "\xe1\xba\x91"     => "\xe1\xba\x90",
      "\xe1\xba\x8f"     => "\xe1\xba\x8e",
      "\xe1\xba\x8d"     => "\xe1\xba\x8c",
      "\xe1\xba\x8b"     => "\xe1\xba\x8a",
      "\xe1\xba\x89"     => "\xe1\xba\x88",
      "\xe1\xba\x87"     => "\xe1\xba\x86",
      "\xe1\xba\x85"     => "\xe1\xba\x84",
      "\xe1\xba\x83"     => "\xe1\xba\x82",
      "\xe1\xba\x81"     => "\xe1\xba\x80",
      "\xe1\xb9\xbf"     => "\xe1\xb9\xbe",
      "\xe1\xb9\xbd"     => "\xe1\xb9\xbc",
      "\xe1\xb9\xbb"     => "\xe1\xb9\xba",
      "\xe1\xb9\xb9"     => "\xe1\xb9\xb8",
      "\xe1\xb9\xb7"     => "\xe1\xb9\xb6",
      "\xe1\xb9\xb5"     => "\xe1\xb9\xb4",
      "\xe1\xb9\xb3"     => "\xe1\xb9\xb2",
      "\xe1\xb9\xb1"     => "\xe1\xb9\xb0",
      "\xe1\xb9\xaf"     => "\xe1\xb9\xae",
      "\xe1\xb9\xad"     => "\xe1\xb9\xac",
      "\xe1\xb9\xab"     => "\xe1\xb9\xaa",
      "\xe1\xb9\xa9"     => "\xe1\xb9\xa8",
      "\xe1\xb9\xa7"     => "\xe1\xb9\xa6",
      "\xe1\xb9\xa5"     => "\xe1\xb9\xa4",
      "\xe1\xb9\xa3"     => "\xe1\xb9\xa2",
      "\xe1\xb9\xa1"     => "\xe1\xb9\xa0",
      "\xe1\xb9\x9f"     => "\xe1\xb9\x9e",
      "\xe1\xb9\x9d"     => "\xe1\xb9\x9c",
      "\xe1\xb9\x9b"     => "\xe1\xb9\x9a",
      "\xe1\xb9\x99"     => "\xe1\xb9\x98",
      "\xe1\xb9\x97"     => "\xe1\xb9\x96",
      "\xe1\xb9\x95"     => "\xe1\xb9\x94",
      "\xe1\xb9\x93"     => "\xe1\xb9\x92",
      "\xe1\xb9\x91"     => "\xe1\xb9\x90",
      "\xe1\xb9\x8f"     => "\xe1\xb9\x8e",
      "\xe1\xb9\x8d"     => "\xe1\xb9\x8c",
      "\xe1\xb9\x8b"     => "\xe1\xb9\x8a",
      "\xe1\xb9\x89"     => "\xe1\xb9\x88",
      "\xe1\xb9\x87"     => "\xe1\xb9\x86",
      "\xe1\xb9\x85"     => "\xe1\xb9\x84",
      "\xe1\xb9\x83"     => "\xe1\xb9\x82",
      "\xe1\xb9\x81"     => "\xe1\xb9\x80",
      "\xe1\xb8\xbf"     => "\xe1\xb8\xbe",
      "\xe1\xb8\xbd"     => "\xe1\xb8\xbc",
      "\xe1\xb8\xbb"     => "\xe1\xb8\xba",
      "\xe1\xb8\xb9"     => "\xe1\xb8\xb8",
      "\xe1\xb8\xb7"     => "\xe1\xb8\xb6",
      "\xe1\xb8\xb5"     => "\xe1\xb8\xb4",
      "\xe1\xb8\xb3"     => "\xe1\xb8\xb2",
      "\xe1\xb8\xb1"     => "\xe1\xb8\xb0",
      "\xe1\xb8\xaf"     => "\xe1\xb8\xae",
      "\xe1\xb8\xad"     => "\xe1\xb8\xac",
      "\xe1\xb8\xab"     => "\xe1\xb8\xaa",
      "\xe1\xb8\xa9"     => "\xe1\xb8\xa8",
      "\xe1\xb8\xa7"     => "\xe1\xb8\xa6",
      "\xe1\xb8\xa5"     => "\xe1\xb8\xa4",
      "\xe1\xb8\xa3"     => "\xe1\xb8\xa2",
      "\xe1\xb8\xa1"     => "\xe1\xb8\xa0",
      "\xe1\xb8\x9f"     => "\xe1\xb8\x9e",
      "\xe1\xb8\x9d"     => "\xe1\xb8\x9c",
      "\xe1\xb8\x9b"     => "\xe1\xb8\x9a",
      "\xe1\xb8\x99"     => "\xe1\xb8\x98",
      "\xe1\xb8\x97"     => "\xe1\xb8\x96",
      "\xe1\xb8\x95"     => "\xe1\xb8\x94",
      "\xe1\xb8\x93"     => "\xe1\xb8\x92",
      "\xe1\xb8\x91"     => "\xe1\xb8\x90",
      "\xe1\xb8\x8f"     => "\xe1\xb8\x8e",
      "\xe1\xb8\x8d"     => "\xe1\xb8\x8c",
      "\xe1\xb8\x8b"     => "\xe1\xb8\x8a",
      "\xe1\xb8\x89"     => "\xe1\xb8\x88",
      "\xe1\xb8\x87"     => "\xe1\xb8\x86",
      "\xe1\xb8\x85"     => "\xe1\xb8\x84",
      "\xe1\xb8\x83"     => "\xe1\xb8\x82",
      "\xe1\xb8\x81"     => "\xe1\xb8\x80",
      "\xe1\xb5\xbd"     => "\xe2\xb1\xa3",
      "\xe1\xb5\xb9"     => "\xea\x9d\xbd",
      "\xd6\x86"         => "\xd5\x96",
      "\xd6\x85"         => "\xd5\x95",
      "\xd6\x84"         => "\xd5\x94",
      "\xd6\x83"         => "\xd5\x93",
      "\xd6\x82"         => "\xd5\x92",
      "\xd6\x81"         => "\xd5\x91",
      "\xd6\x80"         => "\xd5\x90",
      "\xd5\xbf"         => "\xd5\x8f",
      "\xd5\xbe"         => "\xd5\x8e",
      "\xd5\xbd"         => "\xd5\x8d",
      "\xd5\xbc"         => "\xd5\x8c",
      "\xd5\xbb"         => "\xd5\x8b",
      "\xd5\xba"         => "\xd5\x8a",
      "\xd5\xb9"         => "\xd5\x89",
      "\xd5\xb8"         => "\xd5\x88",
      "\xd5\xb7"         => "\xd5\x87",
      "\xd5\xb6"         => "\xd5\x86",
      "\xd5\xb5"         => "\xd5\x85",
      "\xd5\xb4"         => "\xd5\x84",
      "\xd5\xb3"         => "\xd5\x83",
      "\xd5\xb2"         => "\xd5\x82",
      "\xd5\xb1"         => "\xd5\x81",
      "\xd5\xb0"         => "\xd5\x80",
      "\xd5\xaf"         => "\xd4\xbf",
      "\xd5\xae"         => "\xd4\xbe",
      "\xd5\xad"         => "\xd4\xbd",
      "\xd5\xac"         => "\xd4\xbc",
      "\xd5\xab"         => "\xd4\xbb",
      "\xd5\xaa"         => "\xd4\xba",
      "\xd5\xa9"         => "\xd4\xb9",
      "\xd5\xa8"         => "\xd4\xb8",
      "\xd5\xa7"         => "\xd4\xb7",
      "\xd5\xa6"         => "\xd4\xb6",
      "\xd5\xa5"         => "\xd4\xb5",
      "\xd5\xa4"         => "\xd4\xb4",
      "\xd5\xa3"         => "\xd4\xb3",
      "\xd5\xa2"         => "\xd4\xb2",
      "\xd5\xa1"         => "\xd4\xb1",
      "\xd4\xa5"         => "\xd4\xa4",
      "\xd4\xa3"         => "\xd4\xa2",
      "\xd4\xa1"         => "\xd4\xa0",
      "\xd4\x9f"         => "\xd4\x9e",
      "\xd4\x9d"         => "\xd4\x9c",
      "\xd4\x9b"         => "\xd4\x9a",
      "\xd4\x99"         => "\xd4\x98",
      "\xd4\x97"         => "\xd4\x96",
      "\xd4\x95"         => "\xd4\x94",
      "\xd4\x93"         => "\xd4\x92",
      "\xd4\x91"         => "\xd4\x90",
      "\xd4\x8f"         => "\xd4\x8e",
      "\xd4\x8d"         => "\xd4\x8c",
      "\xd4\x8b"         => "\xd4\x8a",
      "\xd4\x89"         => "\xd4\x88",
      "\xd4\x87"         => "\xd4\x86",
      "\xd4\x85"         => "\xd4\x84",
      "\xd4\x83"         => "\xd4\x82",
      "\xd4\x81"         => "\xd4\x80",
      "\xd3\xbf"         => "\xd3\xbe",
      "\xd3\xbd"         => "\xd3\xbc",
      "\xd3\xbb"         => "\xd3\xba",
      "\xd3\xb9"         => "\xd3\xb8",
      "\xd3\xb7"         => "\xd3\xb6",
      "\xd3\xb5"         => "\xd3\xb4",
      "\xd3\xb3"         => "\xd3\xb2",
      "\xd3\xb1"         => "\xd3\xb0",
      "\xd3\xaf"         => "\xd3\xae",
      "\xd3\xad"         => "\xd3\xac",
      "\xd3\xab"         => "\xd3\xaa",
      "\xd3\xa9"         => "\xd3\xa8",
      "\xd3\xa7"         => "\xd3\xa6",
      "\xd3\xa5"         => "\xd3\xa4",
      "\xd3\xa3"         => "\xd3\xa2",
      "\xd3\xa1"         => "\xd3\xa0",
      "\xd3\x9f"         => "\xd3\x9e",
      "\xd3\x9d"         => "\xd3\x9c",
      "\xd3\x9b"         => "\xd3\x9a",
      "\xd3\x99"         => "\xd3\x98",
      "\xd3\x97"         => "\xd3\x96",
      "\xd3\x95"         => "\xd3\x94",
      "\xd3\x93"         => "\xd3\x92",
      "\xd3\x91"         => "\xd3\x90",
      "\xd3\x8f"         => "\xd3\x80",
      "\xd3\x8e"         => "\xd3\x8d",
      "\xd3\x8c"         => "\xd3\x8b",
      "\xd3\x8a"         => "\xd3\x89",
      "\xd3\x88"         => "\xd3\x87",
      "\xd3\x86"         => "\xd3\x85",
      "\xd3\x84"         => "\xd3\x83",
      "\xd3\x82"         => "\xd3\x81",
      "\xd2\xbf"         => "\xd2\xbe",
      "\xd2\xbd"         => "\xd2\xbc",
      "\xd2\xbb"         => "\xd2\xba",
      "\xd2\xb9"         => "\xd2\xb8",
      "\xd2\xb7"         => "\xd2\xb6",
      "\xd2\xb5"         => "\xd2\xb4",
      "\xd2\xb3"         => "\xd2\xb2",
      "\xd2\xb1"         => "\xd2\xb0",
      "\xd2\xaf"         => "\xd2\xae",
      "\xd2\xad"         => "\xd2\xac",
      "\xd2\xab"         => "\xd2\xaa",
      "\xd2\xa9"         => "\xd2\xa8",
      "\xd2\xa7"         => "\xd2\xa6",
      "\xd2\xa5"         => "\xd2\xa4",
      "\xd2\xa3"         => "\xd2\xa2",
      "\xd2\xa1"         => "\xd2\xa0",
      "\xd2\x9f"         => "\xd2\x9e",
      "\xd2\x9d"         => "\xd2\x9c",
      "\xd2\x9b"         => "\xd2\x9a",
      "\xd2\x99"         => "\xd2\x98",
      "\xd2\x97"         => "\xd2\x96",
      "\xd2\x95"         => "\xd2\x94",
      "\xd2\x93"         => "\xd2\x92",
      "\xd2\x91"         => "\xd2\x90",
      "\xd2\x8f"         => "\xd2\x8e",
      "\xd2\x8d"         => "\xd2\x8c",
      "\xd2\x8b"         => "\xd2\x8a",
      "\xd2\x81"         => "\xd2\x80",
      "\xd1\xbf"         => "\xd1\xbe",
      "\xd1\xbd"         => "\xd1\xbc",
      "\xd1\xbb"         => "\xd1\xba",
      "\xd1\xb9"         => "\xd1\xb8",
      "\xd1\xb7"         => "\xd1\xb6",
      "\xd1\xb5"         => "\xd1\xb4",
      "\xd1\xb3"         => "\xd1\xb2",
      "\xd1\xb1"         => "\xd1\xb0",
      "\xd1\xaf"         => "\xd1\xae",
      "\xd1\xad"         => "\xd1\xac",
      "\xd1\xab"         => "\xd1\xaa",
      "\xd1\xa9"         => "\xd1\xa8",
      "\xd1\xa7"         => "\xd1\xa6",
      "\xd1\xa5"         => "\xd1\xa4",
      "\xd1\xa3"         => "\xd1\xa2",
      "\xd1\xa1"         => "\xd1\xa0",
      "\xd1\x9f"         => "\xd0\x8f",
      "\xd1\x9e"         => "\xd0\x8e",
      "\xd1\x9d"         => "\xd0\x8d",
      "\xd1\x9c"         => "\xd0\x8c",
      "\xd1\x9b"         => "\xd0\x8b",
      "\xd1\x9a"         => "\xd0\x8a",
      "\xd1\x99"         => "\xd0\x89",
      "\xd1\x98"         => "\xd0\x88",
      "\xd1\x97"         => "\xd0\x87",
      "\xd1\x96"         => "\xd0\x86",
      "\xd1\x95"         => "\xd0\x85",
      "\xd1\x94"         => "\xd0\x84",
      "\xd1\x93"         => "\xd0\x83",
      "\xd1\x92"         => "\xd0\x82",
      "\xd1\x91"         => "\xd0\x81",
      "\xd1\x90"         => "\xd0\x80",
      "\xd1\x8f"         => "\xd0\xaf",
      "\xd1\x8e"         => "\xd0\xae",
      "\xd1\x8d"         => "\xd0\xad",
      "\xd1\x8c"         => "\xd0\xac",
      "\xd1\x8b"         => "\xd0\xab",
      "\xd1\x8a"         => "\xd0\xaa",
      "\xd1\x89"         => "\xd0\xa9",
      "\xd1\x88"         => "\xd0\xa8",
      "\xd1\x87"         => "\xd0\xa7",
      "\xd1\x86"         => "\xd0\xa6",
      "\xd1\x85"         => "\xd0\xa5",
      "\xd1\x84"         => "\xd0\xa4",
      "\xd1\x83"         => "\xd0\xa3",
      "\xd1\x82"         => "\xd0\xa2",
      "\xd1\x81"         => "\xd0\xa1",
      "\xd1\x80"         => "\xd0\xa0",
      "\xd0\xbf"         => "\xd0\x9f",
      "\xd0\xbe"         => "\xd0\x9e",
      "\xd0\xbd"         => "\xd0\x9d",
      "\xd0\xbc"         => "\xd0\x9c",
      "\xd0\xbb"         => "\xd0\x9b",
      "\xd0\xba"         => "\xd0\x9a",
      "\xd0\xb9"         => "\xd0\x99",
      "\xd0\xb8"         => "\xd0\x98",
      "\xd0\xb7"         => "\xd0\x97",
      "\xd0\xb6"         => "\xd0\x96",
      "\xd0\xb5"         => "\xd0\x95",
      "\xd0\xb4"         => "\xd0\x94",
      "\xd0\xb3"         => "\xd0\x93",
      "\xd0\xb2"         => "\xd0\x92",
      "\xd0\xb1"         => "\xd0\x91",
      "\xd0\xb0"         => "\xd0\x90",
      "\xcf\xbb"         => "\xcf\xba",
      "\xcf\xb8"         => "\xcf\xb7",
      "\xcf\xb5"         => "\xce\x95",
      "\xcf\xb2"         => "\xcf\xb9",
      "\xcf\xb1"         => "\xce\xa1",
      "\xcf\xb0"         => "\xce\x9a",
      "\xcf\xaf"         => "\xcf\xae",
      "\xcf\xad"         => "\xcf\xac",
      "\xcf\xab"         => "\xcf\xaa",
      "\xcf\xa9"         => "\xcf\xa8",
      "\xcf\xa7"         => "\xcf\xa6",
      "\xcf\xa5"         => "\xcf\xa4",
      "\xcf\xa3"         => "\xcf\xa2",
      "\xcf\xa1"         => "\xcf\xa0",
      "\xcf\x9f"         => "\xcf\x9e",
      "\xcf\x9d"         => "\xcf\x9c",
      "\xcf\x9b"         => "\xcf\x9a",
      "\xcf\x99"         => "\xcf\x98",
      "\xcf\x97"         => "\xcf\x8f",
      "\xcf\x96"         => "\xce\xa0",
      "\xcf\x95"         => "\xce\xa6",
      "\xcf\x91"         => "\xce\x98",
      "\xcf\x90"         => "\xce\x92",
      "\xcf\x8e"         => "\xce\x8f",
      "\xcf\x8d"         => "\xce\x8e",
      "\xcf\x8c"         => "\xce\x8c",
      "\xcf\x8b"         => "\xce\xab",
      "\xcf\x8a"         => "\xce\xaa",
      "\xcf\x89"         => "\xce\xa9",
      "\xcf\x88"         => "\xce\xa8",
      "\xcf\x87"         => "\xce\xa7",
      "\xcf\x86"         => "\xce\xa6",
      "\xcf\x85"         => "\xce\xa5",
      "\xcf\x84"         => "\xce\xa4",
      "\xcf\x83"         => "\xce\xa3",
      "\xcf\x82"         => "\xce\xa3",
      "\xcf\x81"         => "\xce\xa1",
      "\xcf\x80"         => "\xce\xa0",
      "\xce\xbf"         => "\xce\x9f",
      "\xce\xbe"         => "\xce\x9e",
      "\xce\xbd"         => "\xce\x9d",
      "\xce\xbc"         => "\xce\x9c",
      "\xce\xbb"         => "\xce\x9b",
      "\xce\xba"         => "\xce\x9a",
      "\xce\xb9"         => "\xce\x99",
      "\xce\xb8"         => "\xce\x98",
      "\xce\xb7"         => "\xce\x97",
      "\xce\xb6"         => "\xce\x96",
      "\xce\xb5"         => "\xce\x95",
      "\xce\xb4"         => "\xce\x94",
      "\xce\xb3"         => "\xce\x93",
      "\xce\xb2"         => "\xce\x92",
      "\xce\xb1"         => "\xce\x91",
      "\xce\xaf"         => "\xce\x8a",
      "\xce\xae"         => "\xce\x89",
      "\xce\xad"         => "\xce\x88",
      "\xce\xac"         => "\xce\x86",
      "\xcd\xbd"         => "\xcf\xbf",
      "\xcd\xbc"         => "\xcf\xbe",
      "\xcd\xbb"         => "\xcf\xbd",
      "\xcd\xb7"         => "\xcd\xb6",
      "\xcd\xb3"         => "\xcd\xb2",
      "\xcd\xb1"         => "\xcd\xb0",
      "\xca\x92"         => "\xc6\xb7",
      "\xca\x8c"         => "\xc9\x85",
      "\xca\x8b"         => "\xc6\xb2",
      "\xca\x8a"         => "\xc6\xb1",
      "\xca\x89"         => "\xc9\x84",
      "\xca\x88"         => "\xc6\xae",
      "\xca\x83"         => "\xc6\xa9",
      "\xca\x80"         => "\xc6\xa6",
      "\xc9\xbd"         => "\xe2\xb1\xa4",
      "\xc9\xb5"         => "\xc6\x9f",
      "\xc9\xb2"         => "\xc6\x9d",
      "\xc9\xb1"         => "\xe2\xb1\xae",
      "\xc9\xaf"         => "\xc6\x9c",
      "\xc9\xab"         => "\xe2\xb1\xa2",
      "\xc9\xa9"         => "\xc6\x96",
      "\xc9\xa8"         => "\xc6\x97",
      "\xc9\xa5"         => "\xea\x9e\x8d",
      "\xc9\xa3"         => "\xc6\x94",
      "\xc9\xa0"         => "\xc6\x93",
      "\xc9\x9b"         => "\xc6\x90",
      "\xc9\x99"         => "\xc6\x8f",
      "\xc9\x97"         => "\xc6\x8a",
      "\xc9\x96"         => "\xc6\x89",
      "\xc9\x94"         => "\xc6\x86",
      "\xc9\x93"         => "\xc6\x81",
      "\xc9\x92"         => "\xe2\xb1\xb0",
      "\xc9\x91"         => "\xe2\xb1\xad",
      "\xc9\x90"         => "\xe2\xb1\xaf",
      "\xc9\x8f"         => "\xc9\x8e",
      "\xc9\x8d"         => "\xc9\x8c",
      "\xc9\x8b"         => "\xc9\x8a",
      "\xc9\x89"         => "\xc9\x88",
      "\xc9\x87"         => "\xc9\x86",
      "\xc9\x82"         => "\xc9\x81",
      "\xc9\x80"         => "\xe2\xb1\xbf",
      "\xc8\xbf"         => "\xe2\xb1\xbe",
      "\xc8\xbc"         => "\xc8\xbb",
      "\xc8\xb3"         => "\xc8\xb2",
      "\xc8\xb1"         => "\xc8\xb0",
      "\xc8\xaf"         => "\xc8\xae",
      "\xc8\xad"         => "\xc8\xac",
      "\xc8\xab"         => "\xc8\xaa",
      "\xc8\xa9"         => "\xc8\xa8",
      "\xc8\xa7"         => "\xc8\xa6",
      "\xc8\xa5"         => "\xc8\xa4",
      "\xc8\xa3"         => "\xc8\xa2",
      "\xc8\x9f"         => "\xc8\x9e",
      "\xc8\x9d"         => "\xc8\x9c",
      "\xc8\x9b"         => "\xc8\x9a",
      "\xc8\x99"         => "\xc8\x98",
      "\xc8\x97"         => "\xc8\x96",
      "\xc8\x95"         => "\xc8\x94",
      "\xc8\x93"         => "\xc8\x92",
      "\xc8\x91"         => "\xc8\x90",
      "\xc8\x8f"         => "\xc8\x8e",
      "\xc8\x8d"         => "\xc8\x8c",
      "\xc8\x8b"         => "\xc8\x8a",
      "\xc8\x89"         => "\xc8\x88",
      "\xc8\x87"         => "\xc8\x86",
      "\xc8\x85"         => "\xc8\x84",
      "\xc8\x83"         => "\xc8\x82",
      "\xc8\x81"         => "\xc8\x80",
      "\xc7\xbf"         => "\xc7\xbe",
      "\xc7\xbd"         => "\xc7\xbc",
      "\xc7\xbb"         => "\xc7\xba",
      "\xc7\xb9"         => "\xc7\xb8",
      "\xc7\xb5"         => "\xc7\xb4",
      "\xc7\xb3"         => "\xc7\xb2",
      "\xc7\xaf"         => "\xc7\xae",
      "\xc7\xad"         => "\xc7\xac",
      "\xc7\xab"         => "\xc7\xaa",
      "\xc7\xa9"         => "\xc7\xa8",
      "\xc7\xa7"         => "\xc7\xa6",
      "\xc7\xa5"         => "\xc7\xa4",
      "\xc7\xa3"         => "\xc7\xa2",
      "\xc7\xa1"         => "\xc7\xa0",
      "\xc7\x9f"         => "\xc7\x9e",
      "\xc7\x9d"         => "\xc6\x8e",
      "\xc7\x9c"         => "\xc7\x9b",
      "\xc7\x9a"         => "\xc7\x99",
      "\xc7\x98"         => "\xc7\x97",
      "\xc7\x96"         => "\xc7\x95",
      "\xc7\x94"         => "\xc7\x93",
      "\xc7\x92"         => "\xc7\x91",
      "\xc7\x90"         => "\xc7\x8f",
      "\xc7\x8e"         => "\xc7\x8d",
      "\xc7\x8c"         => "\xc7\x8b",
      "\xc7\x89"         => "\xc7\x88",
      "\xc7\x86"         => "\xc7\x85",
      "\xc6\xbf"         => "\xc7\xb7",
      "\xc6\xbd"         => "\xc6\xbc",
      "\xc6\xb9"         => "\xc6\xb8",
      "\xc6\xb6"         => "\xc6\xb5",
      "\xc6\xb4"         => "\xc6\xb3",
      "\xc6\xb0"         => "\xc6\xaf",
      "\xc6\xad"         => "\xc6\xac",
      "\xc6\xa8"         => "\xc6\xa7",
      "\xc6\xa5"         => "\xc6\xa4",
      "\xc6\xa3"         => "\xc6\xa2",
      "\xc6\xa1"         => "\xc6\xa0",
      "\xc6\x9e"         => "\xc8\xa0",
      "\xc6\x9a"         => "\xc8\xbd",
      "\xc6\x99"         => "\xc6\x98",
      "\xc6\x95"         => "\xc7\xb6",
      "\xc6\x92"         => "\xc6\x91",
      "\xc6\x8c"         => "\xc6\x8b",
      "\xc6\x88"         => "\xc6\x87",
      "\xc6\x85"         => "\xc6\x84",
      "\xc6\x83"         => "\xc6\x82",
      "\xc6\x80"         => "\xc9\x83",
      "\xc5\xbf"         => "\x53",
      "\xc5\xbe"         => "\xc5\xbd",
      "\xc5\xbc"         => "\xc5\xbb",
      "\xc5\xba"         => "\xc5\xb9",
      "\xc5\xb7"         => "\xc5\xb6",
      "\xc5\xb5"         => "\xc5\xb4",
      "\xc5\xb3"         => "\xc5\xb2",
      "\xc5\xb1"         => "\xc5\xb0",
      "\xc5\xaf"         => "\xc5\xae",
      "\xc5\xad"         => "\xc5\xac",
      "\xc5\xab"         => "\xc5\xaa",
      "\xc5\xa9"         => "\xc5\xa8",
      "\xc5\xa7"         => "\xc5\xa6",
      "\xc5\xa5"         => "\xc5\xa4",
      "\xc5\xa3"         => "\xc5\xa2",
      "\xc5\xa1"         => "\xc5\xa0",
      "\xc5\x9f"         => "\xc5\x9e",
      "\xc5\x9d"         => "\xc5\x9c",
      "\xc5\x9b"         => "\xc5\x9a",
      "\xc5\x99"         => "\xc5\x98",
      "\xc5\x97"         => "\xc5\x96",
      "\xc5\x95"         => "\xc5\x94",
      "\xc5\x93"         => "\xc5\x92",
      "\xc5\x91"         => "\xc5\x90",
      "\xc5\x8f"         => "\xc5\x8e",
      "\xc5\x8d"         => "\xc5\x8c",
      "\xc5\x8b"         => "\xc5\x8a",
      "\xc5\x88"         => "\xc5\x87",
      "\xc5\x86"         => "\xc5\x85",
      "\xc5\x84"         => "\xc5\x83",
      "\xc5\x82"         => "\xc5\x81",
      "\xc5\x80"         => "\xc4\xbf",
      "\xc4\xbe"         => "\xc4\xbd",
      "\xc4\xbc"         => "\xc4\xbb",
      "\xc4\xba"         => "\xc4\xb9",
      "\xc4\xb7"         => "\xc4\xb6",
      "\xc4\xb5"         => "\xc4\xb4",
      "\xc4\xb3"         => "\xc4\xb2",
      "\xc4\xb1"         => "\x49",
      "\xc4\xaf"         => "\xc4\xae",
      "\xc4\xad"         => "\xc4\xac",
      "\xc4\xab"         => "\xc4\xaa",
      "\xc4\xa9"         => "\xc4\xa8",
      "\xc4\xa7"         => "\xc4\xa6",
      "\xc4\xa5"         => "\xc4\xa4",
      "\xc4\xa3"         => "\xc4\xa2",
      "\xc4\xa1"         => "\xc4\xa0",
      "\xc4\x9f"         => "\xc4\x9e",
      "\xc4\x9d"         => "\xc4\x9c",
      "\xc4\x9b"         => "\xc4\x9a",
      "\xc4\x99"         => "\xc4\x98",
      "\xc4\x97"         => "\xc4\x96",
      "\xc4\x95"         => "\xc4\x94",
      "\xc4\x93"         => "\xc4\x92",
      "\xc4\x91"         => "\xc4\x90",
      "\xc4\x8f"         => "\xc4\x8e",
      "\xc4\x8d"         => "\xc4\x8c",
      "\xc4\x8b"         => "\xc4\x8a",
      "\xc4\x89"         => "\xc4\x88",
      "\xc4\x87"         => "\xc4\x86",
      "\xc4\x85"         => "\xc4\x84",
      "\xc4\x83"         => "\xc4\x82",
      "\xc4\x81"         => "\xc4\x80",
      "\xc3\xbf"         => "\xc5\xb8",
      "\xc3\xbe"         => "\xc3\x9e",
      "\xc3\xbd"         => "\xc3\x9d",
      "\xc3\xbc"         => "\xc3\x9c",
      "\xc3\xbb"         => "\xc3\x9b",
      "\xc3\xba"         => "\xc3\x9a",
      "\xc3\xb9"         => "\xc3\x99",
      "\xc3\xb8"         => "\xc3\x98",
      "\xc3\xb6"         => "\xc3\x96",
      "\xc3\xb5"         => "\xc3\x95",
      "\xc3\xb4"         => "\xc3\x94",
      "\xc3\xb3"         => "\xc3\x93",
      "\xc3\xb2"         => "\xc3\x92",
      "\xc3\xb1"         => "\xc3\x91",
      "\xc3\xb0"         => "\xc3\x90",
      "\xc3\xaf"         => "\xc3\x8f",
      "\xc3\xae"         => "\xc3\x8e",
      "\xc3\xad"         => "\xc3\x8d",
      "\xc3\xac"         => "\xc3\x8c",
      "\xc3\xab"         => "\xc3\x8b",
      "\xc3\xaa"         => "\xc3\x8a",
      "\xc3\xa9"         => "\xc3\x89",
      "\xc3\xa8"         => "\xc3\x88",
      "\xc3\xa7"         => "\xc3\x87",
      "\xc3\xa6"         => "\xc3\x86",
      "\xc3\xa5"         => "\xc3\x85",
      "\xc3\xa4"         => "\xc3\x84",
      "\xc3\xa3"         => "\xc3\x83",
      "\xc3\xa2"         => "\xc3\x82",
      "\xc3\xa1"         => "\xc3\x81",
      "\xc3\xa0"         => "\xc3\x80",
      "\xc2\xb5"         => "\xce\x9c",
      "\x7a"             => "\x5a",
      "\x79"             => "\x59",
      "\x78"             => "\x58",
      "\x77"             => "\x57",
      "\x76"             => "\x56",
      "\x75"             => "\x55",
      "\x74"             => "\x54",
      "\x73"             => "\x53",
      "\x72"             => "\x52",
      "\x71"             => "\x51",
      "\x70"             => "\x50",
      "\x6f"             => "\x4f",
      "\x6e"             => "\x4e",
      "\x6d"             => "\x4d",
      "\x6c"             => "\x4c",
      "\x6b"             => "\x4b",
      "\x6a"             => "\x4a",
      "\x69"             => "\x49",
      "\x68"             => "\x48",
      "\x67"             => "\x47",
      "\x66"             => "\x46",
      "\x65"             => "\x45",
      "\x64"             => "\x44",
      "\x63"             => "\x43",
      "\x62"             => "\x42",
      "\x61"             => "\x41",

    );

    return $case;
  }

  /**
   * This method will auto-detect your server environment for UTF-8 support.
   *
   * INFO: You don't need to run it manually, it will be triggered if it's needed.
   */
  public static function checkForSupport()
  {
    if (!isset(self::$support['mbstring'])) {

      self::$support['mbstring'] = self::mbstring_loaded();
      self::$support['iconv'] = self::iconv_loaded();
      self::$support['intl'] = self::intl_loaded();
      self::$support['intlChar'] = self::intlChar_loaded();
      self::$support['pcre_utf8'] = self::pcre_utf8_support();
    }
  }

  /**
   * Generates a UTF-8 encoded character from the given code point.
   *
   * @param    int $code_point The code point for which to generate a character.
   *
   * @return   string Multi-Byte character, returns empty string on failure to encode.
   */
  public static function chr($code_point)
  {
    self::checkForSupport();

    $i = (int)$code_point;

    if (self::$support['intlChar'] === true) {
      return \IntlChar::chr($code_point);
    }

    if ($i !== $code_point) {
      $i = (int)self::hex_to_int($code_point);
    }

    if (!$i) {
      return '';
    }

    return self::html_entity_decode("&#{$i};", ENT_QUOTES);
  }

  /**
   * Applies callback to all characters of a string.
   *
   * @param  string|array $callback The callback function.
   * @param  string       $str      UTF-8 string to run callback on.
   *
   * @return array The outcome of callback.
   */
  public static function chr_map($callback, $str)
  {
    $chars = self::split($str);

    return array_map($callback, $chars);
  }

  /**
   * Generates an array of byte length of each character of a Unicode string.
   *
   * 1 byte => U+0000  - U+007F
   * 2 byte => U+0080  - U+07FF
   * 3 byte => U+0800  - U+FFFF
   * 4 byte => U+10000 - U+10FFFF
   *
   * @param    string $str The original Unicode string.
   *
   * @return   array An array of byte lengths of each character.
   */
  public static function chr_size_list($str)
  {
    if (!$str) {
      return array();
    }

    return array_map('strlen', self::split($str));
  }

  /**
   * Get a decimal code representation of a specific character.
   *
   * @param   string $char The input character
   *
   * @return  int
   */
  public static function chr_to_decimal($char)
  {
    $char = (string)$char;
    $code = self::ord($char[0]);
    $bytes = 1;

    if (!($code & 0x80)) {
      // 0xxxxxxx
      return $code;
    }

    if (($code & 0xe0) === 0xc0) {
      // 110xxxxx
      $bytes = 2;
      $code &= ~0xc0;
    } elseif (($code & 0xf0) === 0xe0) {
      // 1110xxxx
      $bytes = 3;
      $code &= ~0xe0;
    } elseif (($code & 0xf8) === 0xf0) {
      // 11110xxx
      $bytes = 4;
      $code &= ~0xf0;
    }

    for ($i = 2; $i <= $bytes; $i++) {
      // 10xxxxxx
      $code = ($code << 6) + (self::ord($char[$i - 1]) & ~0x80);
    }

    return $code;
  }

  /**
   * Get hexadecimal code point (U+xxxx) of a UTF-8 encoded character.
   *
   * @param    string $char The input character
   * @param    string $pfix
   *
   * @return   string The code point encoded as U+xxxx
   */
  public static function chr_to_hex($char, $pfix = 'U+')
  {
    return self::int_to_hex(self::ord($char), $pfix);
  }

  /**
   * Splits a string into smaller chunks and multiple lines, using the specified line ending character.
   *
   * @param    string $body     The original string to be split.
   * @param    int    $chunklen The maximum character length of a chunk.
   * @param    string $end      The character(s) to be inserted at the end of each chunk.
   *
   * @return   string The chunked string
   */
  public static function chunk_split($body, $chunklen = 76, $end = "\r\n")
  {
    return implode($end, self::split($body, $chunklen));
  }

  /**
   * Accepts a string and removes all non-UTF-8 characters from it + extras if needed.
   *
   * @param string $str                     The string to be sanitized.
   * @param bool   $remove_bom
   * @param bool   $normalize_whitespace
   * @param bool   $normalize_msword        e.g.: "…" => "..."
   * @param bool   $keep_non_breaking_space set true, to keep non-breaking-spaces
   *
   * @return string Clean UTF-8 encoded string
   */
  public static function clean($str, $remove_bom = false, $normalize_whitespace = false, $normalize_msword = false, $keep_non_breaking_space = false)
  {
    // http://stackoverflow.com/questions/1401317/remove-non-utf8-characters-from-string
    // caused connection reset problem on larger strings

    $regx = '/
      (
        (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
        |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
        |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
        |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3
        ){1,100}                      # ...one or more times
      )
    | ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
    | ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
    /x';
    $str = preg_replace($regx, '$1', $str);

    $str = self::replace_diamond_question_mark($str, '');
    $str = self::remove_invisible_characters($str);

    if ($normalize_whitespace === true) {
      $str = self::normalize_whitespace($str, $keep_non_breaking_space);
    }

    if ($normalize_msword === true) {
      $str = self::normalize_msword($str);
    }

    if ($remove_bom === true) {
      $str = self::removeBOM($str);
    }

    return $str;
  }

  /**
   * Clean-up a and show only printable UTF-8 chars at the end  + fix UTF-8 encoding.
   *
   * @param string $str
   *
   * @return string
   */
  public static function cleanup($str)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    // fixed ISO <-> UTF-8 Errors
    $str = self::fix_simple_utf8($str);

    // remove all none UTF-8 symbols
    // && remove diamond question mark (�)
    // && remove remove invisible characters (e.g. "\0")
    // && remove BOM
    // && normalize whitespace chars (but keep non-breaking-spaces)
    $str = self::clean($str, true, true, false, true);

    return (string)$str;
  }

  /**
   * Accepts a string or a array of strings and returns an array of Unicode code points.
   *
   * @param    string|string[] $arg     A UTF-8 encoded string or an array of such strings.
   * @param    bool            $u_style If True, will return code points in U+xxxx format,
   *                                    default, code points will be returned as integers.
   *
   * @return   array The array of code points
   */
  public static function codepoints($arg, $u_style = false)
  {
    if (is_string($arg)) {
      $arg = self::split($arg);
    }

    $arg = array_map(
        array(
            '\\voku\\helper\\UTF8',
            'ord',
        ),
        $arg
    );

    if ($u_style) {
      $arg = array_map(
          array(
              '\\voku\\helper\\UTF8',
              'int_to_hex',
          ),
          $arg
      );
    }

    return $arg;
  }

  /**
   * Returns count of characters used in a string.
   *
   * @param    string $str The input string.
   *
   * @return   array An associative array of Character as keys and
   *           their count as values.
   */
  public static function count_chars($str)
  {
    return array_count_values(self::split($str));
  }

  /**
   * Get a UTF-8 character from its decimal code representation.
   *
   * @param   int $code Code.
   *
   * @return  string
   */
  public static function decimal_to_chr($code)
  {
    self::checkForSupport();

    return \mb_convert_encoding(
        '&#x' . dechex($code) . ';',
        'UTF-8',
        'HTML-ENTITIES'
    );
  }

  /**
   * Encode a string with a new charset-encoding.
   *
   * INFO:  The different to "UTF8::utf8_encode()" is that this function, try to fix also broken / double encoding,
   *        so you can call this function also on a UTF-8 String and you don't mess the string.
   *
   * @param string $encoding e.g. 'UTF-8', 'ISO-8859-1', etc.
   * @param string $str      the string
   * @param bool   $force    force the new encoding (we try to fix broken / double encoding for UTF-8)<br />
   *                         otherwise we auto-detect the current string-encoding
   *
   * @return string
   */
  public static function encode($encoding, $str, $force = true)
  {
    $str = (string)$str;
    $encoding = (string)$encoding;

    if (!isset($str[0], $encoding[0])) {
      return $str;
    }

    $encoding = self::normalizeEncoding($encoding);
    $encodingDetected = self::str_detect_encoding($str);

    if (
        $encodingDetected
        &&
        (
            $force === true
            ||
            $encodingDetected !== $encoding
        )
    ) {
      self::checkForSupport();

      if (
          $encoding === 'UTF-8'
          &&
          (
              $force === true
              || $encodingDetected === 'UTF-8'
              || $encodingDetected === 'WINDOWS-1252'
              || $encodingDetected === 'ISO-8859-1'
          )
      ) {
        return self::to_utf8($str);
      }

      if (
          $encoding === 'ISO-8859-1'
          &&
          (
              $force === true
              || $encodingDetected === 'ISO-8859-1'
              || $encodingDetected === 'UTF-8'
          )
      ) {
        return self::to_win1252($str);
      }

      $strEncoded = \mb_convert_encoding(
          $str,
          $encoding,
          $encodingDetected
      );

      if ($strEncoded) {
        return $strEncoded;
      }
    }

    return $str;
  }

  /**
   * Callback function for preg_replace_callback use.
   *
   * @internal used for "UTF8::html_entity_decode()"
   *
   * @param  array $matches PREG matches
   *
   * @return string
   */
  protected static function html_entity_decode_callback($matches)
  {
    self::checkForSupport();

    $return = \mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');

    if ($return === "'") {
      return '&#x27;';
    }

    return $return;
  }

  /**
   * Reads entire file into a string.
   *
   * WARNING: do not use UTF-8 Option ($convertToUtf8) for binary-files (e.g.: images) !!!
   *
   * @link http://php.net/manual/en/function.file-get-contents.php
   *
   * @param string        $filename      <p>
   *                                     Name of the file to read.
   *                                     </p>
   * @param int|null      $flags         [optional] <p>
   *                                     Prior to PHP 6, this parameter is called
   *                                     use_include_path and is a bool.
   *                                     As of PHP 5 the FILE_USE_INCLUDE_PATH can be used
   *                                     to trigger include path
   *                                     search.
   *                                     </p>
   *                                     <p>
   *                                     The value of flags can be any combination of
   *                                     the following flags (with some restrictions), joined with the
   *                                     binary OR (|)
   *                                     operator.
   *                                     </p>
   *                                     <p>
   *                                     <table>
   *                                     Available flags
   *                                     <tr valign="top">
   *                                     <td>Flag</td>
   *                                     <td>Description</td>
   *                                     </tr>
   *                                     <tr valign="top">
   *                                     <td>
   *                                     FILE_USE_INCLUDE_PATH
   *                                     </td>
   *                                     <td>
   *                                     Search for filename in the include directory.
   *                                     See include_path for more
   *                                     information.
   *                                     </td>
   *                                     </tr>
   *                                     <tr valign="top">
   *                                     <td>
   *                                     FILE_TEXT
   *                                     </td>
   *                                     <td>
   *                                     As of PHP 6, the default encoding of the read
   *                                     data is UTF-8. You can specify a different encoding by creating a
   *                                     custom context or by changing the default using
   *                                     stream_default_encoding. This flag cannot be
   *                                     used with FILE_BINARY.
   *                                     </td>
   *                                     </tr>
   *                                     <tr valign="top">
   *                                     <td>
   *                                     FILE_BINARY
   *                                     </td>
   *                                     <td>
   *                                     With this flag, the file is read in binary mode. This is the default
   *                                     setting and cannot be used with FILE_TEXT.
   *                                     </td>
   *                                     </tr>
   *                                     </table>
   *                                     </p>
   * @param resource|null $context       [optional] <p>
   *                                     A valid context resource created with
   *                                     stream_context_create. If you don't need to use a
   *                                     custom context, you can skip this parameter by &null;.
   *                                     </p>
   * @param int|null      $offset        [optional] <p>
   *                                     The offset where the reading starts.
   *                                     </p>
   * @param int|null      $maxlen        [optional] <p>
   *                                     Maximum length of data read. The default is to read until end
   *                                     of file is reached.
   *                                     </p>
   * @param int           $timeout
   *
   * @param boolean       $convertToUtf8 WARNING: maybe you can't use this option for images or pdf, because they used
   *                                     non default utf-8 chars
   *
   * @return string The function returns the read data or false on failure.
   */
  public static function file_get_contents($filename, $flags = null, $context = null, $offset = null, $maxlen = null, $timeout = 10, $convertToUtf8 = true)
  {
    // init
    $timeout = (int)$timeout;
    $filename = filter_var($filename, FILTER_SANITIZE_STRING);

    if ($timeout && $context === null) {
      $context = stream_context_create(
          array(
              'http' =>
                  array(
                      'timeout' => $timeout,
                  ),
          )
      );
    }

    if (is_int($maxlen)) {
      $data = file_get_contents($filename, $flags, $context, $offset, $maxlen);
    } else {
      $data = file_get_contents($filename, $flags, $context, $offset);
    }

    // return false on error
    if ($data === false) {
      return false;
    }

    if ($convertToUtf8 === true) {
      self::checkForSupport();

      $data = self::encode('UTF-8', $data, false);
      $data = self::cleanup($data);
    }

    // clean utf-8 string
    return $data;
  }

  /**
   * Checks if a file starts with BOM (Byte Order Mark) character.
   *
   * @param    string $file_path Path to a valid file.
   *
   * @return   bool True if the file has BOM at the start, False otherwise.
   */
  public static function file_has_bom($file_path)
  {
    return self::string_has_bom(file_get_contents($file_path));
  }

  /**
   * Normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * @param mixed  $var
   * @param int    $normalization_form
   * @param string $leading_combining
   *
   * @return mixed
   */
  public static function filter($var, $normalization_form = 4 /* n::NFC */, $leading_combining = '◌')
  {
    switch (gettype($var)) {
      case 'array':
        foreach ($var as $k => $v) {
          /** @noinspection AlterInForeachInspection */
          $var[$k] = self::filter($v, $normalization_form, $leading_combining);
        }
        break;
      case 'object':
        foreach ($var as $k => $v) {
          $var->{$k} = self::filter($v, $normalization_form, $leading_combining);
        }
        break;
      case 'string':
        if (false !== strpos($var, "\r")) {
          // Workaround https://bugs.php.net/65732
          $var = str_replace(array("\r\n", "\r"), "\n", $var);
        }
        if (preg_match('/[\x80-\xFF]/', $var)) {
          if (\Normalizer::isNormalized($var, $normalization_form)) {
            $n = '-';
          } else {
            $n = \Normalizer::normalize($var, $normalization_form);

            if (isset($n[0])) {
              $var = $n;
            } else {
              $var = self::encode('UTF-8', $var);
            }

          }
          if ($var[0] >= "\x80" && isset($n[0], $leading_combining[0]) && preg_match('/^\p{Mn}/u', $var)) {
            // Prevent leading combining chars
            // for NFC-safe concatenations.
            $var = $leading_combining . $var;
          }
        }
        break;
    }

    return $var;
  }

  /**
   * "filter_input()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * @param int    $type
   * @param string $var
   * @param int    $filter
   * @param mixed  $option
   *
   * @return mixed
   */
  public static function filter_input($type, $var, $filter = FILTER_DEFAULT, $option = null)
  {
    if (4 > func_num_args()) {
      $var = filter_input($type, $var, $filter);
    } else {
      $var = filter_input($type, $var, $filter, $option);
    }

    return self::filter($var);
  }

  /**
   * "filter_input_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * @param int   $type
   * @param mixed $definition
   * @param bool  $add_empty
   *
   * @return mixed
   */
  public static function filter_input_array($type, $definition = null, $add_empty = true)
  {
    if (2 > func_num_args()) {
      $a = filter_input_array($type);
    } else {
      $a = filter_input_array($type, $definition, $add_empty);
    }

    return self::filter($a);
  }

  /**
   * "filter_var()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * @param mixed $var
   * @param int   $filter
   * @param mixed $option
   *
   * @return mixed
   */
  public static function filter_var($var, $filter = FILTER_DEFAULT, $option = null)
  {
    if (3 > func_num_args()) {
      $var = filter_var($var, $filter);
    } else {
      $var = filter_var($var, $filter, $option);
    }

    return self::filter($var);
  }

  /**
   * "filter_var_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.
   *
   * @param array $data
   * @param mixed $definition
   * @param bool  $add_empty
   *
   * @return mixed
   */
  public static function filter_var_array($data, $definition = null, $add_empty = true)
  {
    if (2 > func_num_args()) {
      $a = filter_var_array($data);
    } else {
      $a = filter_var_array($data, $definition, $add_empty);
    }

    return self::filter($a);
  }

  /**
   * Check if the number of unicode characters are not more than the specified integer.
   *
   * @param    string $str      The original string to be checked.
   * @param    int    $box_size The size in number of chars to be checked against string.
   *
   * @return   bool true if string is less than or equal to $box_size, false otherwise.
   */
  public static function fits_inside($str, $box_size)
  {
    return (self::strlen($str) <= $box_size);
  }

  /**
   * Try to fix simple broken UTF-8 strings.
   *
   * INFO: Take a look at "UTF8::fix_utf8()" if you need a more advanced fix for broken UTF-8 strings.
   *
   * @param string $str
   *
   * @return string
   */
  public static function fix_simple_utf8($str)
  {
    static $brokenUtf8ToUtf8Keys = null;
    static $brokenUtf8ToUtf8Values = null;

    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    if ($brokenUtf8ToUtf8Keys === null) {
      $brokenUtf8ToUtf8Keys = array_keys(self::$brokenUtf8ToUtf8);
      $brokenUtf8ToUtf8Values = array_values(self::$brokenUtf8ToUtf8);
    }

    return str_replace($brokenUtf8ToUtf8Keys, $brokenUtf8ToUtf8Values, $str);
  }

  /**
   * Fix a double (or multiple) encoded UTF8 string.
   *
   * @param string|string[] $str You can use a string or an array of strings.
   *
   * @return mixed
   */
  public static function fix_utf8($str)
  {
    if (is_array($str)) {

      foreach ($str as $k => $v) {
        /** @noinspection AlterInForeachInspection */
        $str[$k] = self::fix_utf8($v);
      }

      return $str;
    }

    $last = '';
    while ($last !== $str) {
      $last = $str;
      $str = self::to_utf8(self::utf8_decode($str));
    }

    return $str;
  }

  /**
   * Get character of a specific character.
   *
   * @param   string $char Character.
   *
   * @return  string 'RTL' or 'LTR'
   */
  public static function getCharDirection($char)
  {
    // init
    self::checkForSupport();

    if (self::$support['intlChar'] === true) {
      $tmpReturn = \IntlChar::charDirection($char);

      // from "IntlChar"-Class
      $charDirection = array(
          'RTL' => array(1, 13, 14, 15, 21),
          'LTR' => array(0, 11, 12, 20),
      );

      if (in_array($tmpReturn, $charDirection['LTR'], true)) {
        return 'LTR';
      } elseif (in_array($tmpReturn, $charDirection['RTL'], true)) {
        return 'RTL';
      }
    }

    $c = static::chr_to_decimal($char);

    if (!(0x5be <= $c && 0x10b7f >= $c)) {
      return 'LTR';
    }

    if (0x85e >= $c) {

      if (0x5be === $c ||
          0x5c0 === $c ||
          0x5c3 === $c ||
          0x5c6 === $c ||
          (0x5d0 <= $c && 0x5ea >= $c) ||
          (0x5f0 <= $c && 0x5f4 >= $c) ||
          0x608 === $c ||
          0x60b === $c ||
          0x60d === $c ||
          0x61b === $c ||
          (0x61e <= $c && 0x64a >= $c) ||
          (0x66d <= $c && 0x66f >= $c) ||
          (0x671 <= $c && 0x6d5 >= $c) ||
          (0x6e5 <= $c && 0x6e6 >= $c) ||
          (0x6ee <= $c && 0x6ef >= $c) ||
          (0x6fa <= $c && 0x70d >= $c) ||
          0x710 === $c ||
          (0x712 <= $c && 0x72f >= $c) ||
          (0x74d <= $c && 0x7a5 >= $c) ||
          0x7b1 === $c ||
          (0x7c0 <= $c && 0x7ea >= $c) ||
          (0x7f4 <= $c && 0x7f5 >= $c) ||
          0x7fa === $c ||
          (0x800 <= $c && 0x815 >= $c) ||
          0x81a === $c ||
          0x824 === $c ||
          0x828 === $c ||
          (0x830 <= $c && 0x83e >= $c) ||
          (0x840 <= $c && 0x858 >= $c) ||
          0x85e === $c
      ) {
        return 'RTL';
      }

    } elseif (0x200f === $c) {

      return 'RTL';

    } elseif (0xfb1d <= $c) {

      if (0xfb1d === $c ||
          (0xfb1f <= $c && 0xfb28 >= $c) ||
          (0xfb2a <= $c && 0xfb36 >= $c) ||
          (0xfb38 <= $c && 0xfb3c >= $c) ||
          0xfb3e === $c ||
          (0xfb40 <= $c && 0xfb41 >= $c) ||
          (0xfb43 <= $c && 0xfb44 >= $c) ||
          (0xfb46 <= $c && 0xfbc1 >= $c) ||
          (0xfbd3 <= $c && 0xfd3d >= $c) ||
          (0xfd50 <= $c && 0xfd8f >= $c) ||
          (0xfd92 <= $c && 0xfdc7 >= $c) ||
          (0xfdf0 <= $c && 0xfdfc >= $c) ||
          (0xfe70 <= $c && 0xfe74 >= $c) ||
          (0xfe76 <= $c && 0xfefc >= $c) ||
          (0x10800 <= $c && 0x10805 >= $c) ||
          0x10808 === $c ||
          (0x1080a <= $c && 0x10835 >= $c) ||
          (0x10837 <= $c && 0x10838 >= $c) ||
          0x1083c === $c ||
          (0x1083f <= $c && 0x10855 >= $c) ||
          (0x10857 <= $c && 0x1085f >= $c) ||
          (0x10900 <= $c && 0x1091b >= $c) ||
          (0x10920 <= $c && 0x10939 >= $c) ||
          0x1093f === $c ||
          0x10a00 === $c ||
          (0x10a10 <= $c && 0x10a13 >= $c) ||
          (0x10a15 <= $c && 0x10a17 >= $c) ||
          (0x10a19 <= $c && 0x10a33 >= $c) ||
          (0x10a40 <= $c && 0x10a47 >= $c) ||
          (0x10a50 <= $c && 0x10a58 >= $c) ||
          (0x10a60 <= $c && 0x10a7f >= $c) ||
          (0x10b00 <= $c && 0x10b35 >= $c) ||
          (0x10b40 <= $c && 0x10b55 >= $c) ||
          (0x10b58 <= $c && 0x10b72 >= $c) ||
          (0x10b78 <= $c && 0x10b7f >= $c)
      ) {
        return 'RTL';
      }
    }

    return 'LTR';
  }

  /**
   * get data from "/data/*.ser"
   *
   * @param string $file
   *
   * @return bool|string|array|int false on error
   */
  protected static function getData($file)
  {
    $file = __DIR__ . '/data/' . $file . '.php';
    if (file_exists($file)) {
      /** @noinspection PhpIncludeInspection */
      return require $file;
    } else {
      return false;
    }
  }

  /**
   * Creates a random string of UTF-8 characters.
   *
   * WARNING: This method does not create a hash of something, maybe it will be renamed in future.
   *
   * @param    int $len The length of string in characters.
   *
   * @return   string String consisting of random characters.
   *
   * @deprecated
   */
  public static function hash($len = 8)
  {
    static $chars = array();
    static $chars_len = null;

    if ($len <= 0) {
      return '';
    }

    // init
    self::checkForSupport();

    if (!$chars) {
      if (self::$support['pcre_utf8'] === true) {
        $chars = array_map(
            array(
                '\\voku\\helper\\UTF8',
                'chr',
            ),
            range(48, 79)
        );

        $chars = preg_replace('/[^\p{N}\p{Lu}\p{Ll}]/u', '', $chars);

        $chars = array_values(array_filter($chars));
      } else {
        $chars = array_merge(range('0', '9'), range('A', 'Z'), range('a', 'z'));
      }

      $chars_len = count($chars);
    }

    $hash = '';

    for (; $len; --$len) {
      $hash .= $chars[mt_rand() % $chars_len];
    }

    return $hash;
  }

  /**
   * Converts hexadecimal U+xxxx code point representation to integer.
   *
   * INFO: opposite to UTF8::int_to_hex()
   *
   * @param    string $str The hexadecimal code point representation.
   *
   * @return   int The code point, or 0 on failure.
   */
  public static function hex_to_int($str)
  {
    if (preg_match('/^(?:\\\u|U\+|)([a-z0-9]{4,6})$/i', $str, $match)) {
      return intval($match[1], 16);
    }

    return 0;
  }

  /**
   * alias for "UTF8::html_entity_decode()"
   *
   * @param string $str
   * @param int    $flags
   * @param string $encoding
   *
   * @return string
   */
  public static function html_decode($str, $flags = null, $encoding = 'UTF-8')
  {
    return self::html_entity_decode($str, $flags, $encoding);
  }

  /**
   * Converts a UTF-8 string to a series of HTML numbered entities.
   *
   * INFO: opposite to UTF8::html_decode()
   *
   * @param  string $str            The Unicode string to be encoded as numbered entities.
   * @param  bool   $keepAsciiChars Keep ASCII chars.
   * @param  string $encoding
   *
   * @return string HTML numbered entities.
   */
  public static function html_encode($str, $keepAsciiChars = false, $encoding = 'UTF-8')
  {
    # INFO: http://stackoverflow.com/questions/35854535/better-explanation-of-convmap-in-mb-encode-numericentity
    if (function_exists('mb_encode_numericentity')) {

      $startCode = 0x00;
      if ($keepAsciiChars === true) {
        $startCode = 0x80;
      }

      $encoding = self::normalizeEncoding($encoding);

      return mb_encode_numericentity(
          $str,
          array($startCode, 0xffff, 0, 0xffff,),
          $encoding
      );
    }

    return implode(
        array_map(
            function ($data) use ($keepAsciiChars) {
              return UTF8::single_chr_html_encode($data, $keepAsciiChars);
            },
            self::split($str)
        )
    );
  }

  /**
   * UTF-8 version of html_entity_decode()
   *
   * The reason we are not using html_entity_decode() by itself is because
   * while it is not technically correct to leave out the semicolon
   * at the end of an entity most browsers will still interpret the entity
   * correctly. html_entity_decode() does not convert entities without
   * semicolons, so we are left with our own little solution here. Bummer.
   *
   * Convert all HTML entities to their applicable characters
   *
   * INFO: opposite to UTF8::html_encode()
   *
   * @link http://php.net/manual/en/function.html-entity-decode.php
   *
   * @param string $str      <p>
   *                         The input string.
   *                         </p>
   * @param int    $flags    [optional] <p>
   *                         A bitmask of one or more of the following flags, which specify how to handle quotes and
   *                         which document type to use. The default is ENT_COMPAT | ENT_HTML401.
   *                         <table>
   *                         Available <i>flags</i> constants
   *                         <tr valign="top">
   *                         <td>Constant Name</td>
   *                         <td>Description</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_COMPAT</b></td>
   *                         <td>Will convert double-quotes and leave single-quotes alone.</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_QUOTES</b></td>
   *                         <td>Will convert both double and single quotes.</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_NOQUOTES</b></td>
   *                         <td>Will leave both double and single quotes unconverted.</td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_HTML401</b></td>
   *                         <td>
   *                         Handle code as HTML 4.01.
   *                         </td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_XML1</b></td>
   *                         <td>
   *                         Handle code as XML 1.
   *                         </td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_XHTML</b></td>
   *                         <td>
   *                         Handle code as XHTML.
   *                         </td>
   *                         </tr>
   *                         <tr valign="top">
   *                         <td><b>ENT_HTML5</b></td>
   *                         <td>
   *                         Handle code as HTML 5.
   *                         </td>
   *                         </tr>
   *                         </table>
   *                         </p>
   * @param string $encoding [optional] <p>
   *                         Encoding to use.
   *                         </p>
   *
   * @return string the decoded string.
   */
  public static function html_entity_decode($str, $flags = null, $encoding = 'UTF-8')
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    if (strpos($str, '&') === false) {
      return $str;
    }

    $encoding = self::normalizeEncoding($encoding);

    if ($flags === null) {
      if (Bootup::is_php('5.4') === true) {
        $flags = ENT_COMPAT | ENT_HTML5;
      } else {
        $flags = ENT_COMPAT;
      }
    }

    do {
      $str_compare = $str;

      $str = preg_replace_callback("/&#\d{2,5};/", array('\voku\helper\UTF8', 'html_entity_decode_callback'), $str);

      // decode numeric & UTF16 two byte entities
      $str = html_entity_decode(
          preg_replace('/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\d{2,4}(?![0-9;]))))/iS', '$1;', $str),
          $flags,
          $encoding
      );

    } while ($str_compare !== $str);

    return $str;
  }

  /**
   * Convert all applicable characters to HTML entities: UTF-8 version of htmlentities()
   *
   * @link http://php.net/manual/en/function.htmlentities.php
   *
   * @param string $str           <p>
   *                              The input string.
   *                              </p>
   * @param int    $flags         [optional] <p>
   *                              A bitmask of one or more of the following flags, which specify how to handle quotes,
   *                              invalid code unit sequences and the used document type. The default is
   *                              ENT_COMPAT | ENT_HTML401.
   *                              <table>
   *                              Available <i>flags</i> constants
   *                              <tr valign="top">
   *                              <td>Constant Name</td>
   *                              <td>Description</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_COMPAT</b></td>
   *                              <td>Will convert double-quotes and leave single-quotes alone.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_QUOTES</b></td>
   *                              <td>Will convert both double and single quotes.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_NOQUOTES</b></td>
   *                              <td>Will leave both double and single quotes unconverted.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_IGNORE</b></td>
   *                              <td>
   *                              Silently discard invalid code unit sequences instead of returning
   *                              an empty string. Using this flag is discouraged as it
   *                              may have security implications.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_SUBSTITUTE</b></td>
   *                              <td>
   *                              Replace invalid code unit sequences with a Unicode Replacement Character
   *                              U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty string.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_DISALLOWED</b></td>
   *                              <td>
   *                              Replace invalid code points for the given document type with a
   *                              Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
   *                              (otherwise) instead of leaving them as is. This may be useful, for
   *                              instance, to ensure the well-formedness of XML documents with
   *                              embedded external content.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML401</b></td>
   *                              <td>
   *                              Handle code as HTML 4.01.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XML1</b></td>
   *                              <td>
   *                              Handle code as XML 1.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XHTML</b></td>
   *                              <td>
   *                              Handle code as XHTML.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML5</b></td>
   *                              <td>
   *                              Handle code as HTML 5.
   *                              </td>
   *                              </tr>
   *                              </table>
   *                              </p>
   * @param string $encoding      [optional] <p>
   *                              Like <b>htmlspecialchars</b>,
   *                              <b>htmlentities</b> takes an optional third argument
   *                              <i>encoding</i> which defines encoding used in
   *                              conversion.
   *                              Although this argument is technically optional, you are highly
   *                              encouraged to specify the correct value for your code.
   *                              </p>
   * @param bool   $double_encode [optional] <p>
   *                              When <i>double_encode</i> is turned off PHP will not
   *                              encode existing html entities. The default is to convert everything.
   *                              </p>
   *
   *
   * @return string the encoded string.
   * </p>
   * <p>
   * If the input <i>string</i> contains an invalid code unit
   * sequence within the given <i>encoding</i> an empty string
   * will be returned, unless either the <b>ENT_IGNORE</b> or
   * <b>ENT_SUBSTITUTE</b> flags are set.
   */
  public static function htmlentities($str, $flags = ENT_COMPAT, $encoding = 'UTF-8', $double_encode = true)
  {
    $encoding = self::normalizeEncoding($encoding);

    $str = htmlentities($str, $flags, $encoding, $double_encode);

    if ($encoding !== 'UTF-8') {
      return $str;
    }

    $byteLengths = self::chr_size_list($str);
    $search = array();
    $replacements = array();
    foreach ($byteLengths as $counter => $byteLength) {
      if ($byteLength >= 3) {
        $char = self::access($str, $counter);

        if (!isset($replacements[$char])) {
          $search[$char] = $char;
          $replacements[$char] = self::html_encode($char);
        }
      }
    }

    return str_replace($search, $replacements, $str);
  }

  /**
   * Convert only special characters to HTML entities: UTF-8 version of htmlspecialchars()
   *
   * INFO: Take a look at "UTF8::htmlentities()"
   *
   * @link http://php.net/manual/en/function.htmlspecialchars.php
   *
   * @param string $str           <p>
   *                              The string being converted.
   *                              </p>
   * @param int    $flags         [optional] <p>
   *                              A bitmask of one or more of the following flags, which specify how to handle quotes,
   *                              invalid code unit sequences and the used document type. The default is
   *                              ENT_COMPAT | ENT_HTML401.
   *                              <table>
   *                              Available <i>flags</i> constants
   *                              <tr valign="top">
   *                              <td>Constant Name</td>
   *                              <td>Description</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_COMPAT</b></td>
   *                              <td>Will convert double-quotes and leave single-quotes alone.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_QUOTES</b></td>
   *                              <td>Will convert both double and single quotes.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_NOQUOTES</b></td>
   *                              <td>Will leave both double and single quotes unconverted.</td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_IGNORE</b></td>
   *                              <td>
   *                              Silently discard invalid code unit sequences instead of returning
   *                              an empty string. Using this flag is discouraged as it
   *                              may have security implications.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_SUBSTITUTE</b></td>
   *                              <td>
   *                              Replace invalid code unit sequences with a Unicode Replacement Character
   *                              U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty string.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_DISALLOWED</b></td>
   *                              <td>
   *                              Replace invalid code points for the given document type with a
   *                              Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
   *                              (otherwise) instead of leaving them as is. This may be useful, for
   *                              instance, to ensure the well-formedness of XML documents with
   *                              embedded external content.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML401</b></td>
   *                              <td>
   *                              Handle code as HTML 4.01.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XML1</b></td>
   *                              <td>
   *                              Handle code as XML 1.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_XHTML</b></td>
   *                              <td>
   *                              Handle code as XHTML.
   *                              </td>
   *                              </tr>
   *                              <tr valign="top">
   *                              <td><b>ENT_HTML5</b></td>
   *                              <td>
   *                              Handle code as HTML 5.
   *                              </td>
   *                              </tr>
   *                              </table>
   *                              </p>
   * @param string $encoding      [optional] <p>
   *                              Defines encoding used in conversion.
   *                              </p>
   *                              <p>
   *                              For the purposes of this function, the encodings
   *                              ISO-8859-1, ISO-8859-15,
   *                              UTF-8, cp866,
   *                              cp1251, cp1252, and
   *                              KOI8-R are effectively equivalent, provided the
   *                              <i>string</i> itself is valid for the encoding, as
   *                              the characters affected by <b>htmlspecialchars</b> occupy
   *                              the same positions in all of these encodings.
   *                              </p>
   * @param bool   $double_encode [optional] <p>
   *                              When <i>double_encode</i> is turned off PHP will not
   *                              encode existing html entities, the default is to convert everything.
   *                              </p>
   *
   * @return string The converted string.
   * </p>
   * <p>
   * If the input <i>string</i> contains an invalid code unit
   * sequence within the given <i>encoding</i> an empty string
   * will be returned, unless either the <b>ENT_IGNORE</b> or
   * <b>ENT_SUBSTITUTE</b> flags are set.
   */
  public static function htmlspecialchars($str, $flags = ENT_COMPAT, $encoding = 'UTF-8', $double_encode = true)
  {
    $encoding = self::normalizeEncoding($encoding);

    return htmlspecialchars($str, $flags, $encoding, $double_encode);
  }

  /**
   * checks whether iconv is available on the server
   *
   * @return   bool True if available, False otherwise
   */
  public static function iconv_loaded()
  {
    return extension_loaded('iconv') ? true : false;
  }

  /**
   * Converts Integer to hexadecimal U+xxxx code point representation.
   *
   * INFO: opposite to UTF8::hex_to_int()
   *
   * @param    int    $int The integer to be converted to hexadecimal code point.
   * @param    string $pfix
   *
   * @return   string The code point, or empty string on failure.
   */
  public static function int_to_hex($int, $pfix = 'U+')
  {
    if (ctype_digit((string)$int)) {
      $hex = dechex((int)$int);

      $hex = (strlen($hex) < 4 ? substr('0000' . $hex, -4) : $hex);

      return $pfix . $hex;
    }

    return '';
  }

  /**
   * checks whether intl is available on the server
   *
   * @return   bool True if available, False otherwise
   */
  public static function intl_loaded()
  {
    return extension_loaded('intl') ? true : false;
  }

  /**
   * checks whether intl-char is available on the server
   *
   * @return   bool True if available, False otherwise
   */
  public static function intlChar_loaded()
  {
    return Bootup::is_php('7.0') === true and class_exists('IntlChar');
  }

  /**
   * alias for "UTF8::is_ascii()"
   *
   * @param string $str
   *
   * @return boolean
   */
  public static function isAscii($str)
  {
    return self::is_ascii($str);
  }

  /**
   * alias for "UTF8::is_base64()"
   *
   * @param string $str
   *
   * @return bool
   */
  public static function isBase64($str)
  {
    return self::is_base64($str);
  }

  /**
   * alias for "UTF8::is_binary()"
   *
   * @param string $str
   *
   * @return bool
   */
  public static function isBinary($str)
  {
    return self::is_binary($str);
  }

  /**
   * alias for "UTF8::is_bom()"
   *
   * @param string $utf8_chr
   *
   * @return boolean
   */
  public static function isBom($utf8_chr)
  {
    return self::is_bom($utf8_chr);
  }

  /**
   * alias for "UTF8::is_json()"
   *
   * @param string $str
   *
   * @return bool
   */
  public static function isJson($str)
  {
    return self::is_json($str);
  }

  /**
   * alias for "UTF8::is_html()"
   *
   * @param string $str
   *
   * @return boolean
   */
  public static function isHtml($str)
  {
    return self::is_html($str);
  }

  /**
   * alias for "UTF8::is_utf8()"
   *
   * @param string $str
   *
   * @return bool
   */
  public static function isUtf8($str)
  {
    return self::is_utf8($str);
  }

  /**
   * alias for "UTF8::is_utf16()"
   *
   * @param string $str
   *
   * @return bool
   */
  public static function isUtf16($str)
  {
    return self::is_utf16($str);
  }

  /**
   * alias for "UTF8::is_utf32()"
   *
   * @param string $str
   *
   * @return bool
   */
  public static function isUtf32($str)
  {
    return self::is_utf32($str);
  }

  /**
   * Checks if a string is 7 bit ASCII.
   *
   * @param    string $str The string to check.
   *
   * @return   bool <strong>true</strong> if it is ASCII<br />
   *                <strong>false</strong> otherwise
   */
  public static function is_ascii($str)
  {
    return (bool)!preg_match('/[\x80-\xFF]/', $str);
  }

  /**
   * Returns true if the string is base64 encoded, false otherwise.
   *
   * @param string $str
   *
   * @return bool Whether or not $str is base64 encoded
   */
  public static function is_base64($str)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return false;
    }

    if (base64_encode(base64_decode($str, true)) === $str) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Check if the input is binary... (is look like a hack).
   *
   * @param string $input
   *
   * @return bool
   */
  public static function is_binary($input)
  {

    $testLength = strlen($input);

    if (
        preg_match('~^[01]+$~', $input)
        ||
        substr_count($input, "\x00") > 0
        ||
        ($testLength ? substr_count($input, '^ -~') / $testLength > 0.3 : 1 === 0)
    ) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Check if the file is binary.
   *
   * @param string $file
   *
   * @return boolean
   */
  public static function is_binary_file($file)
  {
    try {
      $fp = fopen($file, 'r');
      $block = fread($fp, 512);
      fclose($fp);
    } catch (\Exception $e) {
      $block = '';
    }

    return self::is_binary($block);
  }

  /**
   * Checks if the given string is an "Byte Order Mark".
   *
   * WARNING: Use "UTF8::string_has_bom()" if you will check BOM in a string.
   *
   * @param    string $str The input string.
   *
   * @return   bool True if the $utf8_chr is Byte Order Mark, False otherwise.
   */
  public static function is_bom($str)
  {
    foreach (self::$bom as $bomString => $bomByteLength) {
      if ($str === $bomString) {
        return true;
      }
    }

    return false;
  }

  /**
   * Try to check if "$str" is an json-string.
   *
   * @param string $str
   *
   * @return bool
   */
  public static function is_json($str)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return false;
    }

    if (
        is_object(json_decode($str))
        &&
        json_last_error() === JSON_ERROR_NONE
    ) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Check if string contains any html-tags <lall>.
   *
   * @param string $str
   *
   * @return boolean
   */
  public static function is_html($str)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return false;
    }

    // init
    $matches = array();

    preg_match("/<\/?\w+((\s+\w+(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/", $str, $matches);

    if (count($matches) == 0) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Check if the string is UTF-16.
   *
   * @param string $str
   *
   * @return int|false false if is't not UTF16, 1 for UTF-16LE, 2 for UTF-16BE.
   */
  public static function is_utf16($str)
  {
    if (self::is_binary($str)) {
      self::checkForSupport();

      $maybeUTF16LE = 0;
      $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-16LE');
      if ($test !== false && strlen($test) > 1) {
        $test2 = \mb_convert_encoding($test, 'UTF-16LE', 'UTF-8');
        $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-16LE');
        if ($test3 === $test) {
          $strChars = self::count_chars($str);
          foreach (self::count_chars($test3) as $test3char => $test3charEmpty) {
            if (in_array($test3char, $strChars, true) === true) {
              $maybeUTF16LE++;
            }
          }
        }
      }

      $maybeUTF16BE = 0;
      $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-16BE');
      if ($test !== false && strlen($test) > 1) {
        $test2 = \mb_convert_encoding($test, 'UTF-16BE', 'UTF-8');
        $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-16BE');
        if ($test3 === $test) {
          $strChars = self::count_chars($str);
          foreach (self::count_chars($test3) as $test3char => $test3charEmpty) {
            if (in_array($test3char, $strChars, true) === true) {
              $maybeUTF16BE++;
            }
          }
        }
      }

      if ($maybeUTF16BE !== $maybeUTF16LE) {
        if ($maybeUTF16LE > $maybeUTF16BE) {
          return 1;
        } else {
          return 2;
        }
      }

    }

    return false;
  }

  /**
   * Check if the string is UTF-32.
   *
   * @param string $str
   *
   * @return int|false false if is't not UTF16, 1 for UTF-32LE, 2 for UTF-32BE.
   */
  public static function is_utf32($str)
  {
    if (self::is_binary($str)) {
      self::checkForSupport();

      $maybeUTF32LE = 0;
      $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-32LE');
      if ($test !== false && strlen($test) > 1) {
        $test2 = \mb_convert_encoding($test, 'UTF-32LE', 'UTF-8');
        $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-32LE');
        if ($test3 === $test) {
          $strChars = self::count_chars($str);
          foreach (self::count_chars($test3) as $test3char => $test3charEmpty) {
            if (in_array($test3char, $strChars, true) === true) {
              $maybeUTF32LE++;
            }
          }
        }
      }

      $maybeUTF32BE = 0;
      $test = \mb_convert_encoding($str, 'UTF-8', 'UTF-32BE');
      if ($test !== false && strlen($test) > 1) {
        $test2 = \mb_convert_encoding($test, 'UTF-32BE', 'UTF-8');
        $test3 = \mb_convert_encoding($test2, 'UTF-8', 'UTF-32BE');
        if ($test3 === $test) {
          $strChars = self::count_chars($str);
          foreach (self::count_chars($test3) as $test3char => $test3charEmpty) {
            if (in_array($test3char, $strChars, true) === true) {
              $maybeUTF32BE++;
            }
          }
        }
      }

      if ($maybeUTF32BE !== $maybeUTF32LE) {
        if ($maybeUTF32LE > $maybeUTF32BE) {
          return 1;
        } else {
          return 2;
        }
      }

    }

    return false;
  }

  /**
   * Checks whether the passed string contains only byte sequences that appear valid UTF-8 characters.
   *
   * @see    http://hsivonen.iki.fi/php-utf8/
   *
   * @param    string $str The string to be checked.
   *
   * @return   bool
   */
  public static function is_utf8($str)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return true;
    }

    if (self::pcre_utf8_support() !== true) {

      // If even just the first character can be matched, when the /u
      // modifier is used, then it's valid UTF-8. If the UTF-8 is somehow
      // invalid, nothing at all will match, even if the string contains
      // some valid sequences
      return (preg_match('/^.{1}/us', $str, $ar) === 1);

    } else {

      $mState = 0; // cached expected number of octets after the current octet
      // until the beginning of the next UTF8 character sequence
      $mUcs4 = 0; // cached Unicode character
      $mBytes = 1; // cached expected number of octets in the current sequence
      $len = strlen($str);

      /** @noinspection ForeachInvariantsInspection */
      for ($i = 0; $i < $len; $i++) {
        $in = ord($str[$i]);
        if ($mState === 0) {
          // When mState is zero we expect either a US-ASCII character or a
          // multi-octet sequence.
          if (0 === (0x80 & $in)) {
            // US-ASCII, pass straight through.
            $mBytes = 1;
          } elseif (0xC0 === (0xE0 & $in)) {
            // First octet of 2 octet sequence.
            $mUcs4 = $in;
            $mUcs4 = ($mUcs4 & 0x1F) << 6;
            $mState = 1;
            $mBytes = 2;
          } elseif (0xE0 === (0xF0 & $in)) {
            // First octet of 3 octet sequence.
            $mUcs4 = $in;
            $mUcs4 = ($mUcs4 & 0x0F) << 12;
            $mState = 2;
            $mBytes = 3;
          } elseif (0xF0 === (0xF8 & $in)) {
            // First octet of 4 octet sequence.
            $mUcs4 = $in;
            $mUcs4 = ($mUcs4 & 0x07) << 18;
            $mState = 3;
            $mBytes = 4;
          } elseif (0xF8 === (0xFC & $in)) {
            /* First octet of 5 octet sequence.
            *
            * This is illegal because the encoded codepoint must be either
            * (a) not the shortest form or
            * (b) outside the Unicode range of 0-0x10FFFF.
            * Rather than trying to resynchronize, we will carry on until the end
            * of the sequence and let the later error handling code catch it.
            */
            $mUcs4 = $in;
            $mUcs4 = ($mUcs4 & 0x03) << 24;
            $mState = 4;
            $mBytes = 5;
          } elseif (0xFC === (0xFE & $in)) {
            // First octet of 6 octet sequence, see comments for 5 octet sequence.
            $mUcs4 = $in;
            $mUcs4 = ($mUcs4 & 1) << 30;
            $mState = 5;
            $mBytes = 6;
          } else {
            /* Current octet is neither in the US-ASCII range nor a legal first
             * octet of a multi-octet sequence.
             */
            return false;
          }
        } else {
          // When mState is non-zero, we expect a continuation of the multi-octet
          // sequence
          if (0x80 === (0xC0 & $in)) {
            // Legal continuation.
            $shift = ($mState - 1) * 6;
            $tmp = $in;
            $tmp = ($tmp & 0x0000003F) << $shift;
            $mUcs4 |= $tmp;
            /**
             * End of the multi-octet sequence. mUcs4 now contains the final
             * Unicode code point to be output
             */
            if (0 === --$mState) {
              /*
              * Check for illegal sequences and code points.
              */
              // From Unicode 3.1, non-shortest form is illegal
              if (
                  (2 === $mBytes && $mUcs4 < 0x0080) ||
                  (3 === $mBytes && $mUcs4 < 0x0800) ||
                  (4 === $mBytes && $mUcs4 < 0x10000) ||
                  (4 < $mBytes) ||
                  // From Unicode 3.2, surrogate characters are illegal.
                  (($mUcs4 & 0xFFFFF800) === 0xD800) ||
                  // Code points outside the Unicode range are illegal.
                  ($mUcs4 > 0x10FFFF)
              ) {
                return false;
              }
              // initialize UTF8 cache
              $mState = 0;
              $mUcs4 = 0;
              $mBytes = 1;
            }
          } else {
            /**
             *((0xC0 & (*in) != 0x80) && (mState != 0))
             * Incomplete multi-octet sequence.
             */
            return false;
          }
        }
      }

      return true;
    }
  }

  /**
   * (PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
   * Decodes a JSON string
   *
   * @link http://php.net/manual/en/function.json-decode.php
   *
   * @param string $json    <p>
   *                        The <i>json</i> string being decoded.
   *                        </p>
   *                        <p>
   *                        This function only works with UTF-8 encoded strings.
   *                        </p>
   *                        <p>PHP implements a superset of
   *                        JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
   *                        only supports these values when they are nested inside an array or an object.
   *                        </p>
   * @param bool   $assoc   [optional] <p>
   *                        When <b>TRUE</b>, returned objects will be converted into
   *                        associative arrays.
   *                        </p>
   * @param int    $depth   [optional] <p>
   *                        User specified recursion depth.
   *                        </p>
   * @param int    $options [optional] <p>
   *                        Bitmask of JSON decode options. Currently only
   *                        <b>JSON_BIGINT_AS_STRING</b>
   *                        is supported (default is to cast large integers as floats)
   *                        </p>
   *
   * @return mixed the value encoded in <i>json</i> in appropriate
   * PHP type. Values true, false and
   * null (case-insensitive) are returned as <b>TRUE</b>, <b>FALSE</b>
   * and <b>NULL</b> respectively. <b>NULL</b> is returned if the
   * <i>json</i> cannot be decoded or if the encoded
   * data is deeper than the recursion limit.
   */
  public static function json_decode($json, $assoc = false, $depth = 512, $options = 0)
  {
    $json = self::filter($json);

    if (Bootup::is_php('5.4') === true) {
      $json = json_decode($json, $assoc, $depth, $options);
    } else {
      $json = json_decode($json, $assoc, $depth);
    }

    return $json;
  }

  /**
   * (PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
   * Returns the JSON representation of a value
   *
   * @link http://php.net/manual/en/function.json-encode.php
   *
   * @param mixed $value   <p>
   *                       The <i>value</i> being encoded. Can be any type except
   *                       a resource.
   *                       </p>
   *                       <p>
   *                       All string data must be UTF-8 encoded.
   *                       </p>
   *                       <p>PHP implements a superset of
   *                       JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
   *                       only supports these values when they are nested inside an array or an object.
   *                       </p>
   * @param int   $options [optional] <p>
   *                       Bitmask consisting of <b>JSON_HEX_QUOT</b>,
   *                       <b>JSON_HEX_TAG</b>,
   *                       <b>JSON_HEX_AMP</b>,
   *                       <b>JSON_HEX_APOS</b>,
   *                       <b>JSON_NUMERIC_CHECK</b>,
   *                       <b>JSON_PRETTY_PRINT</b>,
   *                       <b>JSON_UNESCAPED_SLASHES</b>,
   *                       <b>JSON_FORCE_OBJECT</b>,
   *                       <b>JSON_UNESCAPED_UNICODE</b>. The behaviour of these
   *                       constants is described on
   *                       the JSON constants page.
   *                       </p>
   * @param int   $depth   [optional] <p>
   *                       Set the maximum depth. Must be greater than zero.
   *                       </p>
   *
   * @return string a JSON encoded string on success or <b>FALSE</b> on failure.
   */
  public static function json_encode($value, $options = 0, $depth = 512)
  {
    $value = self::filter($value);

    if (Bootup::is_php('5.5')) {
      $json = json_encode($value, $options, $depth);
    } else {
      $json = json_encode($value, $options);
    }

    return $json;
  }

  /**
   * Makes string's first char lowercase.
   *
   * @param    string $str The input string
   *
   * @return   string The resulting string
   */
  public static function lcfirst($str)
  {
    return self::strtolower(self::substr($str, 0, 1)) . self::substr($str, 1);
  }

  /**
   * Strip whitespace or other characters from beginning of a UTF-8 string.
   *
   * WARNING: This is much slower then "ltrim()" !!!!
   *
   * @param    string $str   The string to be trimmed
   * @param    string $chars Optional characters to be stripped
   *
   * @return   string The string with unwanted characters stripped from the left
   */
  public static function ltrim($str = '', $chars = INF)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    $chars = INF === $chars ? '\s' : self::rxClass($chars);

    return preg_replace("/^{$chars}+/u", '', $str);
  }

  /**
   * Returns the UTF-8 character with the maximum code point in the given data.
   *
   * @param    mixed $arg A UTF-8 encoded string or an array of such strings.
   *
   * @return   string The character with the highest code point than others.
   */
  public static function max($arg)
  {
    if (is_array($arg)) {
      $arg = implode($arg);
    }

    return self::chr(max(self::codepoints($arg)));
  }

  /**
   * Calculates and returns the maximum number of bytes taken by any
   * UTF-8 encoded character in the given string.
   *
   * @param    string $str The original Unicode string.
   *
   * @return   int An array of byte lengths of each character.
   */
  public static function max_chr_width($str)
  {
    $bytes = self::chr_size_list($str);
    if (count($bytes) > 0) {
      return (int)max($bytes);
    } else {
      return 0;
    }
  }

  /**
   * checks whether mbstring is available on the server
   *
   * @return   bool True if available, False otherwise
   */
  public static function mbstring_loaded()
  {
    $return = extension_loaded('mbstring');

    if ($return === true) {
      \mb_internal_encoding('UTF-8');
    }

    return $return;
  }

  /**
   * Returns the UTF-8 character with the minimum code point in the given data.
   *
   * @param    mixed $arg A UTF-8 encoded string or an array of such strings.
   *
   * @return   string The character with the lowest code point than others.
   */
  public static function min($arg)
  {
    if (is_array($arg)) {
      $arg = implode($arg);
    }

    return self::chr(min(self::codepoints($arg)));
  }

  /**
   * Normalize the encoding-name input.
   *
   * @param string $encoding e.g.: ISO, UTF8, WINDOWS-1251 etc.
   *
   * @return string e.g.: ISO-8859-1, UTF-8, WINDOWS-1251 etc.
   */
  public static function normalizeEncoding($encoding)
  {
    static $staticNormalizeEncodingCache = array();

    if (!$encoding) {
      return $encoding;
    }

    if ('UTF-8' === $encoding) {
      return $encoding;
    }

    if (in_array($encoding, self::$iconvEncoding, true)) {
      return $encoding;
    }

    if (isset($staticNormalizeEncodingCache[$encoding])) {
      return $staticNormalizeEncodingCache[$encoding];
    }

    $encodingOrig = $encoding;
    $encoding = strtoupper($encoding);
    $encodingUpperHelper = preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);

    $equivalences = array(
        'ISO88591'    => 'ISO-8859-1',
        'ISO8859'     => 'ISO-8859-1',
        'ISO'         => 'ISO-8859-1',
        'LATIN1'      => 'ISO-8859-1',
        'LATIN'       => 'ISO-8859-1',
        'UTF16'       => 'UTF-16',
        'UTF32'       => 'UTF-32',
        'UTF8'        => 'UTF-8',
        'UTF'         => 'UTF-8',
        'UTF7'        => 'UTF-7',
        'WIN1252'     => 'ISO-8859-1',
        'WINDOWS1252' => 'ISO-8859-1',
        '8BIT'        => 'CP850',
        'BINARY'      => 'CP850',
    );

    if (!empty($equivalences[$encodingUpperHelper])) {
      $encoding = $equivalences[$encodingUpperHelper];
    }

    $staticNormalizeEncodingCache[$encodingOrig] = $encoding;

    return $encoding;
  }

  /**
   * Normalize MS Word special characters.
   *
   * @param string $str The string to be normalized.
   *
   * @return string
   */
  public static function normalize_msword($str)
  {
    static $utf8MSWordKeys = null;
    static $utf8MSWordValues = null;

    if ($utf8MSWordKeys === null) {
      $utf8MSWordKeys = array_keys(self::$utf8MSWord);
      $utf8MSWordValues = array_values(self::$utf8MSWord);
    }

    return str_replace($utf8MSWordKeys, $utf8MSWordValues, $str);
  }

  /**
   * Normalize the whitespace.
   *
   * @param string $str                     The string to be normalized.
   * @param bool   $keepNonBreakingSpace    Set to true, to keep non-breaking-spaces.
   * @param bool   $keepBidiUnicodeControls Set to true, to keep non-printable (for the web) bidirectional text chars.
   *
   * @return string
   */
  public static function normalize_whitespace($str, $keepNonBreakingSpace = false, $keepBidiUnicodeControls = false)
  {
    static $whitespaces = array();
    static $bidiUniCodeControls = null;

    $cacheKey = (int)$keepNonBreakingSpace;

    if (!isset($whitespaces[$cacheKey])) {

      $whitespaces[$cacheKey] = self::$whitespaceTable;

      if ($keepNonBreakingSpace === true) {
        /** @noinspection OffsetOperationsInspection */
        unset($whitespaces[$cacheKey]['NO-BREAK SPACE']);
      }

      $whitespaces[$cacheKey] = array_values($whitespaces[$cacheKey]);
    }

    if ($keepBidiUnicodeControls === false) {
      if ($bidiUniCodeControls === null) {
        $bidiUniCodeControls = array_values(self::$bidiUniCodeControlsTable);
      }

      $str = str_replace($bidiUniCodeControls, '', $str);
    }

    return str_replace($whitespaces[$cacheKey], ' ', $str);
  }

  /**
   * Format a number with grouped thousands.
   *
   * @param float  $number
   * @param int    $decimals
   * @param string $dec_point
   * @param string $thousands_sep
   *
   * @return string
   */
  public static function number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
  {
    $thousands_sep = (string)$thousands_sep;
    $dec_point = (string)$dec_point;

    if (
        isset($thousands_sep[1], $dec_point[1])
        &&
        Bootup::is_php('5.4') === true
    ) {
      return str_replace(
          array(
              '.',
              ',',
          ),
          array(
              $dec_point,
              $thousands_sep,
          ),
          number_format($number, $decimals, '.', ',')
      );
    }

    return number_format($number, $decimals, $dec_point, $thousands_sep);
  }

  /**
   * Calculates Unicode code point of the given UTF-8 encoded character.
   *
   * @param    string $s The character of which to calculate code point.
   *
   * @return   int Unicode code point of the given character,<br />
   *           0 on invalid UTF-8 byte sequence.
   */
  public static function ord($s)
  {
    if (!$s && $s !== '0') {
      return 0;
    }

    // init
    self::checkForSupport();

    if (self::$support['intlChar'] === true) {
      $tmpReturn = \IntlChar::ord($s);
      if ($tmpReturn) {
        return $tmpReturn;
      }
    }

    $s = unpack('C*', substr($s, 0, 4));
    $a = $s ? $s[1] : 0;

    if (0xF0 <= $a && isset($s[4])) {
      return (($a - 0xF0) << 18) + (($s[2] - 0x80) << 12) + (($s[3] - 0x80) << 6) + $s[4] - 0x80;
    }

    if (0xE0 <= $a && isset($s[3])) {
      return (($a - 0xE0) << 12) + (($s[2] - 0x80) << 6) + $s[3] - 0x80;
    }

    if (0xC0 <= $a && isset($s[2])) {
      return (($a - 0xC0) << 6) + $s[2] - 0x80;
    }

    return $a;
  }

  /**
   * Parses the string into variables.
   *
   * WARNING: This differs from parse_str() by returning the results
   *    instead of placing them in the local scope!
   *
   * @link http://php.net/manual/en/function.parse-str.php
   *
   * @param string $str     <p>
   *                        The input string.
   *                        </p>
   * @param array  $result  <p>
   *                        If the second parameter arr is present,
   *                        variables are stored in this variable as array elements instead.
   *                        </p>
   *
   * @return void
   */
  public static function parse_str($str, &$result)
  {
    // init
    self::checkForSupport();

    $str = self::filter($str);

    \mb_parse_str($str, $result);
  }

  /**
   * checks if \u modifier is available that enables Unicode support in PCRE.
   *
   * @return   bool True if support is available, false otherwise
   */
  public static function pcre_utf8_support()
  {
    /** @noinspection PhpUsageOfSilenceOperatorInspection */
    return (bool)@preg_match('//u', '');
  }

  /**
   * Create an array containing a range of UTF-8 characters.
   *
   * @param    mixed $var1 Numeric or hexadecimal code points, or a UTF-8 character to start from.
   * @param    mixed $var2 Numeric or hexadecimal code points, or a UTF-8 character to end at.
   *
   * @return   array
   */
  public static function range($var1, $var2)
  {
    if (!$var1 || !$var2) {
      return array();
    }

    if (ctype_digit((string)$var1)) {
      $start = (int)$var1;
    } elseif (ctype_xdigit($var1)) {
      $start = (int)self::hex_to_int($var1);
    } else {
      $start = self::ord($var1);
    }

    if (!$start) {
      return array();
    }

    if (ctype_digit((string)$var2)) {
      $end = (int)$var2;
    } elseif (ctype_xdigit($var2)) {
      $end = (int)self::hex_to_int($var2);
    } else {
      $end = self::ord($var2);
    }

    if (!$end) {
      return array();
    }

    return array_map(
        array(
            '\\voku\\helper\\UTF8',
            'chr',
        ),
        range($start, $end)
    );
  }

  /**
   * Remove the BOM from UTF-8 / UTF-16 / UTF-32 strings.
   *
   * @param string $str
   *
   * @return string
   */
  public static function removeBOM($str = '')
  {
    foreach (self::$bom as $bomString => $bomByteLength) {
      if (0 === strpos($str, $bomString)) {
        $str = substr($str, $bomByteLength);
      }
    }

    return $str;
  }

  /**
   * Removes duplicate occurrences of a string in another string.
   *
   * @param    string       $str  The base string
   * @param    string|array $what String to search for in the base string
   *
   * @return   string The result string with removed duplicates
   */
  public static function remove_duplicates($str, $what = ' ')
  {
    if (is_string($what)) {
      $what = array($what);
    }

    if (is_array($what)) {
      foreach ($what as $item) {
        $str = preg_replace('/(' . preg_quote($item, '/') . ')+/', $item, $str);
      }
    }

    return $str;
  }

  /**
   * Remove Invisible Characters
   *
   * This prevents sandwiching null characters
   * between ascii characters, like Java\0script.
   *
   * copy&past from https://github.com/bcit-ci/CodeIgniter/blob/develop/system/core/Common.php
   *
   * @param  string $str
   * @param  bool   $url_encoded
   * @param  string $replacement
   *
   * @return  string
   */
  public static function remove_invisible_characters($str, $url_encoded = true, $replacement = '')
  {
    // init
    $non_displayables = array();

    // every control character except newline (dec 10),
    // carriage return (dec 13) and horizontal tab (dec 09)
    if ($url_encoded) {
      $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
      $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
    }

    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

    do {
      $str = preg_replace($non_displayables, $replacement, $str, -1, $count);
    } while ($count !== 0);

    return $str;
  }

  /**
   * replace diamond question mark (�)
   *
   * @param string $str
   * @param string $unknown
   *
   * @return string
   */
  public static function replace_diamond_question_mark($str, $unknown = '?')
  {
    return str_replace(
        array(
            "\xEF\xBF\xBD",
            '�',
        ),
        array(
            $unknown,
            $unknown,
        ),
        $str
    );
  }

  /**
   * Strip whitespace or other characters from end of a UTF-8 string.
   *
   * WARNING: This is much slower then "rtrim()" !!!!
   *
   * @param    string $str   The string to be trimmed
   * @param    string $chars Optional characters to be stripped
   *
   * @return   string The string with unwanted characters stripped from the right
   */
  public static function rtrim($str = '', $chars = INF)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    $chars = INF === $chars ? '\s' : self::rxClass($chars);

    return preg_replace("/{$chars}+$/u", '', $str);
  }

  /**
   * rxClass
   *
   * @param string $s
   * @param string $class
   *
   * @return string
   */
  protected static function rxClass($s, $class = '')
  {
    static $rxClassCache = array();

    $cacheKey = $s . $class;

    if (isset($rxClassCache[$cacheKey])) {
      return $rxClassCache[$cacheKey];
    }

    $class = array($class);

    /** @noinspection SuspiciousLoopInspection */
    foreach (self::str_split($s) as $s) {
      if ('-' === $s) {
        $class[0] = '-' . $class[0];
      } elseif (!isset($s[2])) {
        $class[0] .= preg_quote($s, '/');
      } elseif (1 === self::strlen($s)) {
        $class[0] .= $s;
      } else {
        $class[] = $s;
      }
    }

    $class[0] = '[' . $class[0] . ']';

    if (1 === count($class)) {
      $return = $class[0];
    } else {
      $return = '(?:' . implode('|', $class) . ')';
    }

    $rxClassCache[$cacheKey] = $return;

    return $return;
  }

  /**
   * Echo native UTF8-Support libs, e.g. for debugging.
   */
  public static function showSupport()
  {
    foreach (self::$support as $utf8Support) {
      echo $utf8Support . "\n<br>";
    }
  }

  /**
   * Converts a UTF-8 character to HTML Numbered Entity like "&#123;".
   *
   * @param    string $char           The Unicode character to be encoded as numbered entity.
   * @param    bool   $keepAsciiChars Keep ASCII chars.
   *
   * @return   string The HTML numbered entity.
   */
  public static function single_chr_html_encode($char, $keepAsciiChars = false)
  {
    if (!$char) {
      return '';
    }

    if (
        $keepAsciiChars === true
        &&
        self::isAscii($char) === true
    ) {
      return $char;
    }

    return '&#' . self::ord($char) . ';';
  }

  /**
   * Convert a string to an array of Unicode characters.
   *
   * @param    string  $str       The string to split into array.
   * @param    int     $length    Max character length of each array element.
   * @param    boolean $cleanUtf8 Clean non UTF-8 chars from the string.
   *
   * @return   array An array containing chunks of the string.
   */
  public static function split($str, $length = 1, $cleanUtf8 = false)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return array();
    }

    // init
    self::checkForSupport();
    $str = (string)$str;
    $ret = array();

    if (self::$support['pcre_utf8'] === true) {

      if ($cleanUtf8 === true) {
        $str = self::clean($str);
      }

      preg_match_all('/./us', $str, $retArray);
      if (isset($retArray[0])) {
        $ret = $retArray[0];
      }
      unset($retArray);

    } else {

      // fallback

      $len = strlen($str);

      /** @noinspection ForeachInvariantsInspection */
      for ($i = 0; $i < $len; $i++) {
        if (($str[$i] & "\x80") === "\x00") {
          $ret[] = $str[$i];
        } elseif ((($str[$i] & "\xE0") === "\xC0") && isset($str[$i + 1])) {
          if (($str[$i + 1] & "\xC0") === "\x80") {
            $ret[] = $str[$i] . $str[$i + 1];

            $i++;
          }
        } elseif ((($str[$i] & "\xF0") === "\xE0") && isset($str[$i + 2])) {
          if ((($str[$i + 1] & "\xC0") === "\x80") && (($str[$i + 2] & "\xC0") === "\x80")) {
            $ret[] = $str[$i] . $str[$i + 1] . $str[$i + 2];

            $i += 2;
          }
        } elseif ((($str[$i] & "\xF8") === "\xF0") && isset($str[$i + 3])) {
          if ((($str[$i + 1] & "\xC0") === "\x80") && (($str[$i + 2] & "\xC0") === "\x80") && (($str[$i + 3] & "\xC0") === "\x80")) {
            $ret[] = $str[$i] . $str[$i + 1] . $str[$i + 2] . $str[$i + 3];

            $i += 3;
          }
        }
      }
    }

    if ($length > 1) {
      $ret = array_chunk($ret, $length);

      $ret = array_map('implode', $ret);
    }

    if (isset($ret[0]) && $ret[0] === '') {
      return array();
    }

    return $ret;
  }

  /**
   * Optimized "\mb_detect_encoding()"-function -> with support for UTF-16 and UTF-32.
   *
   * @param string $str
   *
   * @return false|string The detected string-encoding e.g. UTF-8 or UTF-16BE,<br />
   *                      otherwise it will return false.
   */
  public static function str_detect_encoding($str)
  {

    //
    // 1.) check binary strings (010001001...) like UTF-16 / UTF-32
    //

    if (self::is_binary($str)) {
      if (self::is_utf16($str) === 1) {
        return 'UTF-16LE';
      } elseif (self::is_utf16($str) === 2) {
        return 'UTF-16BE';
      } elseif (self::is_utf32($str) === 1) {
        return 'UTF-32LE';
      } elseif (self::is_utf32($str) === 2) {
        return 'UTF-32BE';
      }
    }

    //
    // 2.) simple check for ASCII chars
    //

    if (self::is_ascii($str) === true) {
      return 'ASCII';
    }

    //
    // 3.) simple check for UTF-8 chars
    //

    if (self::is_utf8($str) === true) {
      return 'UTF-8';
    }

    //
    // 4.) check via "\mb_detect_encoding()"
    //
    // INFO: UTF-16, UTF-32, UCS2 and UCS4, encoding detection will fail always with "\mb_detect_encoding()"

    $detectOrder = array(
        'windows-1251',
        'ISO-8859-1',
        'ASCII',
        'UTF-8',
    );

    self::checkForSupport();

    $encoding = \mb_detect_encoding($str, $detectOrder, true);
    if ($encoding) {
      return $encoding;
    }

    //
    // 5.) check via "iconv()"
    //

    $md5 = md5($str);
    foreach (self::$iconvEncoding as $encodingTmp) {
      # INFO: //IGNORE and //TRANSLIT still throw notice
      /** @noinspection PhpUsageOfSilenceOperatorInspection */
      if (md5(@iconv($encodingTmp, $encodingTmp, $str)) === $md5) {
        return $encodingTmp;
      }
    }

    return false;
  }

  /**
   * Case-insensitive and UTF-8 safe version of <function>str_replace</function>.
   *
   * @link  http://php.net/manual/en/function.str-ireplace.php
   *
   * @param mixed $search  <p>
   *                       Every replacement with search array is
   *                       performed on the result of previous replacement.
   *                       </p>
   * @param mixed $replace <p>
   *                       </p>
   * @param mixed $subject <p>
   *                       If subject is an array, then the search and
   *                       replace is performed with every entry of
   *                       subject, and the return value is an array as
   *                       well.
   *                       </p>
   * @param int   $count   [optional] <p>
   *                       The number of matched and replaced needles will
   *                       be returned in count which is passed by
   *                       reference.
   *                       </p>
   *
   * @return mixed a string or an array of replacements.
   * @since 5.0
   */
  public static function str_ireplace($search, $replace, $subject, &$count = null)
  {
    $search = (array)$search;

    /** @noinspection AlterInForeachInspection */
    foreach ($search as &$s) {
      if ('' === $s .= '') {
        $s = '/^(?<=.)$/';
      } else {
        $s = '/' . preg_quote($s, '/') . '/ui';
      }
    }

    $subject = preg_replace($search, $replace, $subject, -1, $replace);
    $count = $replace;

    return $subject;
  }

  /**
   * Limit the number of characters in a string, but also after the next word.
   *
   * @param  string $str
   * @param  int    $length
   * @param  string $strAddOn
   *
   * @return string
   */
  public static function str_limit_after_word($str, $length = 100, $strAddOn = '...')
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    $length = (int)$length;

    if (self::strlen($str) <= $length) {
      return $str;
    }

    if (self::substr($str, $length - 1, 1) === ' ') {
      return self::substr($str, 0, $length - 1) . $strAddOn;
    }

    $str = self::substr($str, 0, $length);
    $array = explode(' ', $str);
    array_pop($array);
    $new_str = implode(' ', $array);

    if ($new_str === '') {
      $str = self::substr($str, 0, $length - 1) . $strAddOn;
    } else {
      $str = $new_str . $strAddOn;
    }

    return $str;
  }

  /**
   * Pad a UTF-8 string to given length with another string.
   *
   * @param    string $input      The input string
   * @param    int    $pad_length The length of return string
   * @param    string $pad_string String to use for padding the input string
   * @param    int    $pad_type   can be STR_PAD_RIGHT, STR_PAD_LEFT or STR_PAD_BOTH
   *
   * @return   string Returns the padded string
   */
  public static function str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
  {
    $input_length = self::strlen($input);

    if (is_int($pad_length) && ($pad_length > 0) && ($pad_length >= $input_length)) {
      $ps_length = self::strlen($pad_string);

      $diff = $pad_length - $input_length;

      switch ($pad_type) {
        case STR_PAD_LEFT:
          $pre = str_repeat($pad_string, (int)ceil($diff / $ps_length));
          $pre = self::substr($pre, 0, $diff);
          $post = '';
          break;

        case STR_PAD_BOTH:
          $pre = str_repeat($pad_string, (int)ceil($diff / $ps_length / 2));
          $pre = self::substr($pre, 0, (int)$diff / 2);
          $post = str_repeat($pad_string, (int)ceil($diff / $ps_length / 2));
          $post = self::substr($post, 0, (int)ceil($diff / 2));
          break;

        case STR_PAD_RIGHT:
        default:
          $post = str_repeat($pad_string, (int)ceil($diff / $ps_length));
          $post = self::substr($post, 0, $diff);
          $pre = '';
      }

      return $pre . $input . $post;
    }

    return $input;
  }

  /**
   * Repeat a string.
   *
   * @param string $input      <p>
   *                           The string to be repeated.
   *                           </p>
   * @param int    $multiplier <p>
   *                           Number of time the input string should be
   *                           repeated.
   *                           </p>
   *                           <p>
   *                           multiplier has to be greater than or equal to 0.
   *                           If the multiplier is set to 0, the function
   *                           will return an empty string.
   *                           </p>
   *
   * @return string the repeated string.
   */
  public static function str_repeat($input, $multiplier)
  {
    $input = self::filter($input);

    return str_repeat($input, $multiplier);
  }

  /**
   * INFO: this is only a wrapper for "str_replace()"  -> the original functions is already UTF-8 safe
   *
   * (PHP 4, PHP 5)<br/>
   * Replace all occurrences of the search string with the replacement string
   *
   * @link http://php.net/manual/en/function.str-replace.php
   *
   * @param mixed $search  <p>
   *                       The value being searched for, otherwise known as the needle.
   *                       An array may be used to designate multiple needles.
   *                       </p>
   * @param mixed $replace <p>
   *                       The replacement value that replaces found search
   *                       values. An array may be used to designate multiple replacements.
   *                       </p>
   * @param mixed $subject <p>
   *                       The string or array being searched and replaced on,
   *                       otherwise known as the haystack.
   *                       </p>
   *                       <p>
   *                       If subject is an array, then the search and
   *                       replace is performed with every entry of
   *                       subject, and the return value is an array as
   *                       well.
   *                       </p>
   * @param int   $count   [optional] If passed, this will hold the number of matched and replaced needles.
   *
   * @return mixed This function returns a string or an array with the replaced values.
   */
  public static function str_replace($search, $replace, $subject, &$count = null)
  {
    return str_replace($search, $replace, $subject, $count);
  }

  /**
   * Shuffles all the characters in the string.
   *
   * @param    string $str The input string
   *
   * @return   string The shuffled string.
   */
  public static function str_shuffle($str)
  {
    $array = self::split($str);

    shuffle($array);

    return implode('', $array);
  }

  /**
   * Sort all characters according to code points.
   *
   * @param    string $str    A UTF-8 string.
   * @param    bool   $unique Sort unique. If true, repeated characters are ignored.
   * @param    bool   $desc   If true, will sort characters in reverse code point order.
   *
   * @return   string String of sorted characters
   */
  public static function str_sort($str, $unique = false, $desc = false)
  {
    $array = self::codepoints($str);

    if ($unique) {
      $array = array_flip(array_flip($array));
    }

    if ($desc) {
      arsort($array);
    } else {
      asort($array);
    }

    return self::string($array);
  }

  /**
   * Convert a string to an array.
   *
   * @param string $str
   * @param int    $len
   *
   * @return array
   */
  public static function str_split($str, $len = 1)
  {
    // init
    self::checkForSupport();
    $len = (int)$len;

    if ($len < 1) {
      return str_split($str, $len);
    }

    if (self::$support['intl'] === true) {
      $a = array();
      $p = 0;
      $l = strlen($str);
      while ($p < $l) {
        $a[] = \grapheme_extract($str, 1, GRAPHEME_EXTR_COUNT, $p, $p);
      }
    } else {
      preg_match_all('/' . Grapheme::GRAPHEME_CLUSTER_RX . '/u', $str, $a);
      $a = $a[0];
    }

    if ($len === 1) {
      return $a;
    }

    $arrayOutput = array();
    $p = -1;

    /** @noinspection PhpForeachArrayIsUsedAsValueInspection */
    foreach ($a as $l => $a) {
      if ($l % $len) {
        $arrayOutput[$p] .= $a;
      } else {
        $arrayOutput[++$p] = $a;
      }
    }

    return $arrayOutput;
  }

  /**
   * Get a binary representation of a specific character.
   *
   * @param   string $str The input character.
   *
   * @return  string
   */
  public static function str_to_binary($str)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    // init
    $out = null;
    $max = strlen($str);

    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $max; ++$i) {
      $out .= vsprintf('%08b', (array)self::ord($str[$i]));
    }

    return $out;
  }

  /**
   * US-ASCII transliterations of Unicode text.
   *
   * Ported Sean M. Burke's Text::Unidecode Perl module (He did all the hard work!)
   * Warning: you should only pass this well formed UTF-8!
   * Be aware it works by making a copy of the input string which it appends transliterated
   * characters to - it uses a PHP output buffer to do this - it means, memory use will increase,
   * requiring up to the same amount again as the input string
   *
   * @see    http://search.cpan.org/~sburke/Text-Unidecode-0.04/lib/Text/Unidecode.pm
   *
   * @author <hsivonen@iki.fi>
   *
   * @param string $str     UTF-8 string to convert
   * @param string $unknown Character use if character unknown. (default is ?)
   *
   * @return string US-ASCII string
   */
  public static function str_transliterate($str, $unknown = '?')
  {
    static $UTF8_TO_ASCII;

    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    $str = self::clean($str);

    preg_match_all('/.{1}|[^\x00]{1,1}$/us', $str, $ar);
    $chars = $ar[0];
    foreach ($chars as &$c) {

      $ordC0 = ord($c[0]);

      if ($ordC0 >= 0 && $ordC0 <= 127) {
        continue;
      }

      $ordC1 = ord($c[1]);

      // ASCII - next please
      if ($ordC0 >= 192 && $ordC0 <= 223) {
        $ord = ($ordC0 - 192) * 64 + ($ordC1 - 128);
      }

      if ($ordC0 >= 224) {
        $ordC2 = ord($c[2]);

        if ($ordC0 <= 239) {
          $ord = ($ordC0 - 224) * 4096 + ($ordC1 - 128) * 64 + ($ordC2 - 128);
        }

        if ($ordC0 >= 240) {
          $ordC3 = ord($c[3]);

          if ($ordC0 <= 247) {
            $ord = ($ordC0 - 240) * 262144 + ($ordC1 - 128) * 4096 + ($ordC2 - 128) * 64 + ($ordC3 - 128);
          }

          if ($ordC0 >= 248) {
            $ordC4 = ord($c[4]);

            if ($ordC0 <= 251) {
              $ord = ($ordC0 - 248) * 16777216 + ($ordC1 - 128) * 262144 + ($ordC2 - 128) * 4096 + ($ordC3 - 128) * 64 + ($ordC4 - 128);
            }

            if ($ordC0 >= 252) {
              $ordC5 = ord($c[5]);

              if ($ordC0 <= 253) {
                $ord = ($ordC0 - 252) * 1073741824 + ($ordC1 - 128) * 16777216 + ($ordC2 - 128) * 262144 + ($ordC3 - 128) * 4096 + ($ordC4 - 128) * 64 + ($ordC5 - 128);
              }
            }
          }
        }
      }

      if ($ordC0 >= 254 && $ordC0 <= 255) {
        $c = $unknown;
        continue;
      }

      if (!isset($ord)) {
        $c = $unknown;
        continue;
      }

      $bank = $ord >> 8;
      if (!array_key_exists($bank, (array)$UTF8_TO_ASCII)) {
        $bankfile = __DIR__ . '/data/' . sprintf('x%02x', $bank) . '.php';
        if (file_exists($bankfile)) {
          /** @noinspection PhpIncludeInspection */
          require $bankfile;
        } else {
          $UTF8_TO_ASCII[$bank] = array();
        }
      }

      $newchar = $ord & 255;
      if (array_key_exists($newchar, $UTF8_TO_ASCII[$bank])) {
        $c = $UTF8_TO_ASCII[$bank][$newchar];
      } else {
        $c = $unknown;
      }
    }

    return implode('', $chars);
  }

  /**
   * Counts number of words in the UTF-8 string.
   *
   * @param string $str    The input string.
   * @param int    $format <strong>0</strong> => return a number of words<br />
   *                       <strong>1</strong> => return an array of words
   *                       <strong>2</strong> => return an array of words with word-offset as key
   * @param string $charlist
   *
   * @return array|float The number of words in the string
   */
  public static function str_word_count($str, $format = 0, $charlist = '')
  {
    $charlist = self::rxClass($charlist, '\pL');
    $strParts = \preg_split("/({$charlist}+(?:[\p{Pd}’']{$charlist}+)*)/u", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

    $len = count($strParts);

    if ($format === 1) {

      $numberOfWords = array();
      for ($i = 1; $i < $len; $i += 2) {
        $numberOfWords[] = $strParts[$i];
      }

    } elseif ($format === 2) {

      self::checkForSupport();

      $numberOfWords = array();
      $offset = self::strlen($strParts[0]);
      for ($i = 1; $i < $len; $i += 2) {
        $numberOfWords[$offset] = $strParts[$i];
        $offset += self::strlen($strParts[$i]) + self::strlen($strParts[$i + 1]);
      }

    } else {

      $numberOfWords = ($len - 1) / 2;

    }

    return $numberOfWords;
  }

  /**
   * Case-insensitive string comparison.
   *
   * @param string $str1
   * @param string $str2
   *
   * @return int Returns < 0 if str1 is less than str2; > 0 if str1 is greater than str2, and 0 if they are equal.
   */
  public static function strcasecmp($str1, $str2)
  {
    return self::strcmp(self::strtocasefold($str1), self::strtocasefold($str2));
  }

  /**
   * String comparison.
   *
   * @param string $str1
   * @param string $str2
   *
   * @return int  <strong>< 0</strong> if str1 is less than str2<br />
   *              <strong>> 0</strong> if str1 is greater than str2<br />
   *              <strong>0</strong> if they are equal.
   */
  public static function strcmp($str1, $str2)
  {
    return $str1 . '' === $str2 . '' ? 0 : strcmp(
        \Normalizer::normalize($str1, \Normalizer::NFD),
        \Normalizer::normalize($str2, \Normalizer::NFD)
    );
  }

  /**
   * Find length of initial segment not matching mask.
   *
   * @param string $str
   * @param string $charList
   * @param int    $offset
   * @param int    $length
   *
   * @return int|null
   */
  public static function strcspn($str, $charList, $offset = 0, $length = 2147483647)
  {
    if ('' === $charList .= '') {
      return null;
    }

    if ($offset || 2147483647 !== $length) {
      $str = (string)self::substr($str, $offset, $length);
    } else {
      $str = (string)$str;
    }

    if (preg_match('/^(.*?)' . self::rxClass($charList) . '/us', $str, $length)) {
      /** @noinspection OffsetOperationsInspection */
      return self::strlen($length[1]);
    } else {
      return self::strlen($str);
    }
  }

  /**
   * Makes a UTF-8 string from code points.
   *
   * @param    array $array Integer or Hexadecimal codepoints
   *
   * @return   string UTF-8 encoded string
   */
  public static function string($array)
  {
    return implode(
        array_map(
            array(
                '\\voku\\helper\\UTF8',
                'chr',
            ),
            $array
        )
    );
  }

  /**
   * Checks if string starts with "BOM" (Byte Order Mark Character) character.
   *
   * @param    string $str The input string.
   *
   * @return   bool True if the string has BOM at the start, False otherwise.
   */
  public static function string_has_bom($str)
  {
    foreach (self::$bom as $bomString => $bomByteLength) {
      if (0 === strpos($str, $bomString)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Strip HTML and PHP tags from a string.
   *
   * @link http://php.net/manual/en/function.strip-tags.php
   *
   * @param string $str            <p>
   *                               The input string.
   *                               </p>
   * @param string $allowable_tags [optional] <p>
   *                               You can use the optional second parameter to specify tags which should
   *                               not be stripped.
   *                               </p>
   *                               <p>
   *                               HTML comments and PHP tags are also stripped. This is hardcoded and
   *                               can not be changed with allowable_tags.
   *                               </p>
   *
   * @return string the stripped string.
   */
  public static function strip_tags($str, $allowable_tags = null)
  {
    //clean broken utf8
    $str = self::clean($str);

    return strip_tags($str, $allowable_tags);
  }

  /**
   * Finds position of first occurrence of a string within another, case insensitive.
   *
   * @link http://php.net/manual/en/function.mb-stripos.php
   *
   * @param string  $haystack  <p>
   *                           The string from which to get the position of the first occurrence
   *                           of needle
   *                           </p>
   * @param string  $needle    <p>
   *                           The string to find in haystack
   *                           </p>
   * @param int     $offset    [optional] <p>
   *                           The position in haystack
   *                           to start searching
   *                           </p>
   * @param string  $encoding
   * @param boolean $cleanUtf8 Clean non UTF-8 chars from the string
   *
   * @return int Return the numeric position of the first occurrence of
   * needle in the haystack
   * string, or false if needle is not found.
   */
  public static function stripos($haystack, $needle, $offset = null, $encoding = 'UTF-8', $cleanUtf8 = false)
  {
    $haystack = (string)$haystack;
    $needle = (string)$needle;

    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    // init
    self::checkForSupport();

    if ($cleanUtf8 === true) {
      $haystack = self::clean($haystack);
      $needle = self::clean($needle);
    }

    // INFO: this is only a fallback for old versions
    if ($encoding === true || $encoding === false) {
      $encoding = 'UTF-8';
    } else {
      $encoding = self::normalizeEncoding($encoding);
    }

    return \mb_stripos($haystack, $needle, $offset, $encoding);
  }

  /**
   * Returns all of haystack starting from and including the first occurrence of needle to the end.
   *
   * @param string $str
   * @param string $needle
   * @param bool   $before_needle
   *
   * @return false|string
   */
  public static function stristr($str, $needle, $before_needle = false)
  {
    if ('' === $needle .= '') {
      return false;
    }

    // init
    self::checkForSupport();

    return \mb_stristr($str, $needle, $before_needle, 'UTF-8');
  }

  /**
   * Get the string length, not the byte-length!
   *
   * @link     http://php.net/manual/en/function.mb-strlen.php
   *
   * @param string  $str       The string being checked for length.
   * @param string  $encoding  Set the charset for e.g. "\mb_" function
   * @param boolean $cleanUtf8 Clean non UTF-8 chars from the string
   *
   * @return int the number of characters in
   *           string str having character encoding
   *           encoding. A multi-byte character is
   *           counted as 1.
   */
  public static function strlen($str, $encoding = 'UTF-8', $cleanUtf8 = false)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return 0;
    }

    // INFO: this is only a fallback for old versions
    if ($encoding === true || $encoding === false) {
      $encoding = 'UTF-8';
    } else {
      $encoding = self::normalizeEncoding($encoding);
    }

    switch ($encoding) {
      case 'ASCII':
      case 'CP850':
        return strlen($str);
    }

    self::checkForSupport();

    if ($encoding === 'UTF-8' && $cleanUtf8 === true) {
      $str = self::clean($str);
    }

    return \mb_strlen($str, $encoding);
  }

  /**
   * Case insensitive string comparisons using a "natural order" algorithm.
   *
   * @param string $str1
   * @param string $str2
   *
   * @return int <strong>< 0</strong> if str1 is less than str2<br />
   *             <strong>> 0</strong> if str1 is greater than str2<br />
   *             <strong>0</strong> if they are equal
   */
  public static function strnatcasecmp($str1, $str2)
  {
    return self::strnatcmp(self::strtocasefold($str1), self::strtocasefold($str2));
  }

  /**
   * String comparisons using a "natural order" algorithm
   *
   * @link  http://php.net/manual/en/function.strnatcmp.php
   *
   * @param string $str1 <p>
   *                     The first string.
   *                     </p>
   * @param string $str2 <p>
   *                     The second string.
   *                     </p>
   *
   * @return int Similar to other string comparison functions, this one returns &lt; 0 if
   * str1 is less than str2; &gt;
   * 0 if str1 is greater than
   * str2, and 0 if they are equal.
   * @since 4.0
   * @since 5.0
   */
  public static function strnatcmp($str1, $str2)
  {
    return $str1 . '' === $str2 . '' ? 0 : strnatcmp(self::strtonatfold($str1), self::strtonatfold($str2));
  }

  /**
   * Binary safe case-insensitive string comparison of the first n characters
   *
   * @link  http://php.net/manual/en/function.strncasecmp.php
   *
   * @param string $str1 <p>
   *                     The first string.
   *                     </p>
   * @param string $str2 <p>
   *                     The second string.
   *                     </p>
   * @param int    $len  <p>
   *                     The length of strings to be used in the comparison.
   *                     </p>
   *
   * @return int &lt; 0 if <i>str1</i> is less than
   * <i>str2</i>; &gt; 0 if <i>str1</i> is
   * greater than <i>str2</i>, and 0 if they are equal.
   * @since 4.0.4
   * @since 5.0
   */
  public static function strncasecmp($str1, $str2, $len)
  {
    return self::strncmp(self::strtocasefold($str1), self::strtocasefold($str2), $len);
  }

  /**
   * Binary safe string comparison of the first n characters
   *
   * @link  http://php.net/manual/en/function.strncmp.php
   *
   * @param string $str1 <p>
   *                     The first string.
   *                     </p>
   * @param string $str2 <p>
   *                     The second string.
   *                     </p>
   * @param int    $len  <p>
   *                     Number of characters to use in the comparison.
   *                     </p>
   *
   * @return int &lt; 0 if <i>str1</i> is less than
   * <i>str2</i>; &gt; 0 if <i>str1</i>
   * is greater than <i>str2</i>, and 0 if they are
   * equal.
   * @since 4.0
   * @since 5.0
   */
  public static function strncmp($str1, $str2, $len)
  {
    return self::strcmp(self::substr($str1, 0, $len), self::substr($str2, 0, $len));
  }

  /**
   * Search a string for any of a set of characters
   *
   * @link  http://php.net/manual/en/function.strpbrk.php
   *
   * @param string $haystack  <p>
   *                          The string where char_list is looked for.
   *                          </p>
   * @param string $char_list <p>
   *                          This parameter is case sensitive.
   *                          </p>
   *
   * @return string a string starting from the character found, or false if it is
   * not found.
   * @since 5.0
   */
  public static function strpbrk($haystack, $char_list)
  {
    $haystack = (string)$haystack;
    $char_list = (string)$char_list;

    if (!isset($haystack[0], $char_list[0])) {
      return false;
    }

    if (preg_match('/' . self::rxClass($char_list) . '/us', $haystack, $m)) {
      return substr($haystack, strpos($haystack, $m[0]));
    } else {
      return false;
    }
  }

  /**
   * Find position of first occurrence of string in a string.
   *
   * @link http://php.net/manual/en/function.mb-strpos.php
   *
   * @param string  $haystack     <p>
   *                              The string being checked.
   *                              </p>
   * @param string  $needle       <p>
   *                              The position counted from the beginning of haystack.
   *                              </p>
   * @param int     $offset       [optional] <p>
   *                              The search offset. If it is not specified, 0 is used.
   *                              </p>
   * @param string  $encoding
   * @param boolean $cleanUtf8    Clean non UTF-8 chars from the string.
   *
   * @return int The numeric position of the first occurrence of needle in the haystack string.<br />
   *             If needle is not found it returns false.
   */
  public static function strpos($haystack, $needle, $offset = 0, $encoding = 'UTF-8', $cleanUtf8 = false)
  {
    $haystack = (string)$haystack;
    $needle = (string)$needle;

    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    // init
    self::checkForSupport();
    $offset = (int)$offset;

    // iconv and mbstring do not support integer $needle

    if (((int)$needle) === $needle && ($needle >= 0)) {
      $needle = self::chr($needle);
    }

    if ($cleanUtf8 === true) {
      // \mb_strpos returns wrong position if invalid characters are found in $haystack before $needle
      // iconv_strpos is not tolerant to invalid characters

      $needle = self::clean((string)$needle);
      $haystack = self::clean($haystack);
    }

    if (self::$support['mbstring'] === true) {

      // INFO: this is only a fallback for old versions
      if ($encoding === true || $encoding === false) {
        $encoding = 'UTF-8';
      } else {
        $encoding = self::normalizeEncoding($encoding);
      }

      return \mb_strpos($haystack, $needle, $offset, $encoding);
    }

    if (self::$support['iconv'] === true) {
      // ignore invalid negative offset to keep compatility
      // with php < 5.5.35, < 5.6.21, < 7.0.6
      return \grapheme_strpos($haystack, $needle, $offset > 0 ? $offset : 0);
    }

    if ($offset > 0) {
      $haystack = self::substr($haystack, $offset);
    }

    if (($pos = strpos($haystack, $needle)) !== false) {
      $left = substr($haystack, 0, $pos);

      // negative offset not supported in PHP strpos(), ignoring
      return ($offset > 0 ? $offset : 0) + self::strlen($left);
    }

    return false;
  }

  /**
   * Finds the last occurrence of a character in a string within another.
   *
   * @link http://php.net/manual/en/function.mb-strrchr.php
   *
   * @param string $haystack <p>
   *                         The string from which to get the last occurrence
   *                         of needle
   *                         </p>
   * @param string $needle   <p>
   *                         The string to find in haystack
   *                         </p>
   * @param bool   $part     [optional] <p>
   *                         Determines which portion of haystack
   *                         this function returns.
   *                         If set to true, it returns all of haystack
   *                         from the beginning to the last occurrence of needle.
   *                         If set to false, it returns all of haystack
   *                         from the last occurrence of needle to the end,
   *                         </p>
   * @param string $encoding [optional] <p>
   *                         Character encoding name to use.
   *                         If it is omitted, internal character encoding is used.
   *                         </p>
   *
   * @return string the portion of haystack.
   * or false if needle is not found.
   */
  public static function strrchr($haystack, $needle, $part = false, $encoding = 'UTF-8')
  {
    self::checkForSupport();
    $encoding = self::normalizeEncoding($encoding);

    return \mb_strrchr($haystack, $needle, $part, $encoding);
  }

  /**
   * Reverses characters order in the string.
   *
   * @param    string $str The input string
   *
   * @return   string The string with characters in the reverse sequence
   */
  public static function strrev($str)
  {
    return implode(array_reverse(self::split($str)));
  }

  /**
   * Finds the last occurrence of a character in a string within another, case insensitive.
   *
   * @link http://php.net/manual/en/function.mb-strrichr.php
   *
   * @param string $haystack <p>
   *                         The string from which to get the last occurrence
   *                         of needle
   *                         </p>
   * @param string $needle   <p>
   *                         The string to find in haystack
   *                         </p>
   * @param bool   $part     [optional] <p>
   *                         Determines which portion of haystack
   *                         this function returns.
   *                         If set to true, it returns all of haystack
   *                         from the beginning to the last occurrence of needle.
   *                         If set to false, it returns all of haystack
   *                         from the last occurrence of needle to the end,
   *                         </p>
   * @param string $encoding [optional] <p>
   *                         Character encoding name to use.
   *                         If it is omitted, internal character encoding is used.
   *                         </p>
   *
   * @return string the portion of haystack.
   * or false if needle is not found.
   */
  public static function strrichr($haystack, $needle, $part = false, $encoding = 'UTF-8')
  {
    self::checkForSupport();
    $encoding = self::normalizeEncoding($encoding);

    return \mb_strrichr($haystack, $needle, $part, $encoding);
  }

  /**
   * Find position of last occurrence of a case-insensitive string.
   *
   * @param    string $haystack The string to look in
   * @param    string $needle   The string to look for
   * @param    int    $offset   (Optional) Number of characters to ignore in the beginning or end
   *
   * @return   int The position of offset
   */
  public static function strripos($haystack, $needle, $offset = 0)
  {
    return self::strrpos(self::strtolower($haystack), self::strtolower($needle), $offset);
  }

  /**
   * Find position of last occurrence of a string in a string.
   *
   * @link http://php.net/manual/en/function.mb-strrpos.php
   *
   * @param string     $haystack  <p>
   *                              The string being checked, for the last occurrence
   *                              of needle
   *                              </p>
   * @param string|int $needle    <p>
   *                              The string to find in haystack.
   *                              Or a code point as int.
   *                              </p>
   * @param int        $offset    [optional] May be specified to begin searching an arbitrary number of characters into
   *                              the string. Negative values will stop searching at an arbitrary point
   *                              prior to the end of the string.
   * @param boolean    $cleanUtf8 Clean non UTF-8 chars from the string
   *
   * @return int the numeric position of
   * the last occurrence of needle in the
   * haystack string. If
   * needle is not found, it returns false.
   */
  public static function strrpos($haystack, $needle, $offset = null, $cleanUtf8 = false)
  {
    $haystack = (string)$haystack;

    if (((int)$needle) === $needle && ($needle >= 0)) {
      $needle = self::chr($needle);
    }

    $needle = (string)$needle;

    if (!isset($haystack[0], $needle[0])) {
      return false;
    }

    // init
    self::checkForSupport();

    $needle = (string)$needle;
    $offset = (int)$offset;

    if ($cleanUtf8 === true) {
      // \mb_strrpos && iconv_strrpos is not tolerant to invalid characters

      $needle = self::clean($needle);
      $haystack = self::clean($haystack);
    }

    if (self::$support['mbstring'] === true) {
      return \mb_strrpos($haystack, $needle, $offset, 'UTF-8');
    }

    if (self::$support['iconv'] === true) {
      return \grapheme_strrpos($haystack, $needle, $offset);
    }

    // fallback

    if ($offset > 0) {
      $haystack = self::substr($haystack, $offset);
    } elseif ($offset < 0) {
      $haystack = self::substr($haystack, 0, $offset);
    }

    if (($pos = strrpos($haystack, $needle)) !== false) {
      $left = substr($haystack, 0, $pos);

      // negative offset not supported in PHP strpos(), ignoring
      return ($offset > 0 ? $offset : 0) + self::strlen($left);
    }

    return false;
  }

  /**
   * Finds the length of the initial segment of a string consisting entirely of characters contained within a given
   * mask.
   *
   * @param string $str
   * @param string $mask
   * @param int    $offset
   * @param int    $length
   *
   * @return int|null
   */
  public static function strspn($str, $mask, $offset = 0, $length = 2147483647)
  {
    if ($offset || 2147483647 !== $length) {
      $str = self::substr($str, $offset, $length);
    }

    return preg_match('/^' . self::rxClass($mask) . '+/u', $str, $str) ? self::strlen($str[0]) : 0;
  }

  /**
   * Returns part of haystack string from the first occurrence of needle to the end of haystack.
   *
   * @link http://php.net/manual/en/function.grapheme-strstr.php
   *
   * @param string $haystack      <p>
   *                              The input string. Must be valid UTF-8.
   *                              </p>
   * @param string $needle        <p>
   *                              The string to look for. Must be valid UTF-8.
   *                              </p>
   * @param bool   $before_needle [optional] <p>
   *                              If <b>TRUE</b>, grapheme_strstr() returns the part of the
   *                              haystack before the first occurrence of the needle (excluding the needle).
   *                              </p>
   *
   * @return string the portion of string, or FALSE if needle is not found.
   */
  public static function strstr($haystack, $needle, $before_needle = false)
  {
    self::checkForSupport();

    return \grapheme_strstr($haystack, $needle, $before_needle);
  }

  /**
   * Unicode transformation for case-less matching.
   *
   * @link http://unicode.org/reports/tr21/tr21-5.html
   *
   * @param string $str
   * @param bool   $full
   *
   * @return string
   */
  public static function strtocasefold($str, $full = true)
  {
    static $fullCaseFold = null;
    static $commonCaseFoldKeys = null;
    static $commonCaseFoldValues = null;

    if ($commonCaseFoldKeys === null) {
      $commonCaseFoldKeys = array_keys(self::$commonCaseFold);
      $commonCaseFoldValues = array_values(self::$commonCaseFold);
    }

    $str = str_replace($commonCaseFoldKeys, $commonCaseFoldValues, $str);

    if ($full) {

      if ($fullCaseFold === null) {
        $fullCaseFold = self::getData('caseFolding_full');
      }

      /** @noinspection OffsetOperationsInspection */
      $str = str_replace($fullCaseFold[0], $fullCaseFold[1], $str);
    }

    $str = self::clean($str);

    return self::strtolower($str);
  }

  /**
   * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
   * Make a string lowercase.
   *
   * @link http://php.net/manual/en/function.mb-strtolower.php
   *
   * @param string $str <p>
   *                    The string being lowercased.
   *                    </p>
   * @param string $encoding
   *
   * @return string str with all alphabetic characters converted to lowercase.
   */
  public static function strtolower($str, $encoding = 'UTF-8')
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    // init
    self::checkForSupport();
    $encoding = self::normalizeEncoding($encoding);

    return \mb_strtolower($str, $encoding);
  }

  /**
   * Generic case sensitive transformation for collation matching.
   *
   * @param string $s
   *
   * @return string
   */
  protected static function strtonatfold($s)
  {
    return preg_replace('/\p{Mn}+/u', '', \Normalizer::normalize($s, \Normalizer::NFD));
  }

  /**
   * Make a string uppercase.
   *
   * @link http://php.net/manual/en/function.mb-strtoupper.php
   *
   * @param string $str <p>
   *                    The string being uppercased.
   *                    </p>
   * @param string $encoding
   *
   * @return string str with all alphabetic characters converted to uppercase.
   */
  public static function strtoupper($str, $encoding = 'UTF-8')
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    // init
    self::checkForSupport();

    if (self::$support['mbstring'] === true) {
      $encoding = self::normalizeEncoding($encoding);

      return \mb_strtoupper($str, $encoding);
    } else {

      // fallback

      static $caseTableKeys = null;
      static $caseTableValues = null;

      if ($caseTableKeys === null) {
        $caseTable = self::case_table();
        $caseTableKeys = array_keys($caseTable);
        $caseTableValues = array_values($caseTable);
      }

      $str = self::clean($str);

      return str_replace($caseTableKeys, $caseTableValues, $str);
    }
  }

  /**
   * Translate characters or replace sub-strings.
   *
   * @link  http://php.net/manual/en/function.strtr.php
   *
   * @param string       $str  <p>
   *                           The string being translated.
   *                           </p>
   * @param string|array $from <p>
   *                           The string replacing from.
   *                           </p>
   * @param string|array $to   <p>
   *                           The string being translated to to.
   *                           </p>
   *
   * @return string This function returns a copy of str,
   * translating all occurrences of each character in
   * from to the corresponding character in
   * to.
   * @since 4.0
   * @since 5.0
   */
  public static function strtr($str, $from, $to = INF)
  {
    if (INF !== $to) {
      $from = self::str_split($from);
      $to = self::str_split($to);
      $countFrom = count($from);
      $countTo = count($to);

      if ($countFrom > $countTo) {
        $from = array_slice($from, 0, $countTo);
      } elseif ($countFrom < $countTo) {
        $to = array_slice($to, 0, $countFrom);
      }

      $from = array_combine($from, $to);
    }

    return strtr($str, $from);
  }

  /**
   * Return the width of a string.
   *
   * @param string $s
   *
   * @return int
   */
  public static function strwidth($s)
  {
    // init
    self::checkForSupport();

    return \mb_strwidth($s, 'UTF-8');
  }

  /**
   * Get part of a string.
   *
   * @link http://php.net/manual/en/function.mb-substr.php
   *
   * @param string  $str       <p>
   *                           The string being checked.
   *                           </p>
   * @param int     $start     <p>
   *                           The first position used in str.
   *                           </p>
   * @param int     $length    [optional] <p>
   *                           The maximum length of the returned string.
   *                           </p>
   * @param string  $encoding
   * @param boolean $cleanUtf8 Clean non UTF-8 chars from the string
   *
   * @return string mb_substr returns the portion of
   * str specified by the start and length parameters.
   */
  public static function substr($str, $start = 0, $length = null, $encoding = 'UTF-8', $cleanUtf8 = false)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    // init
    self::checkForSupport();

    if ($cleanUtf8 === true) {
      // iconv and mbstring are not tolerant to invalid encoding
      // further, their behaviour is inconsistent with that of PHP's substr

      $str = self::clean($str);
    }

    if ($length === null) {
      $length = (int)self::strlen($str);
    } else {
      $length = (int)$length;
    }

    if (self::$support['mbstring'] === true) {

      // INFO: this is only a fallback for old versions
      if ($encoding === true || $encoding === false) {
        $encoding = 'UTF-8';
      } else {
        $encoding = self::normalizeEncoding($encoding);
      }

      return \mb_substr($str, $start, $length, $encoding);
    }

    if (self::$support['iconv'] === true) {
      return (string)\grapheme_substr($str, $start, $length);
    }

    // fallback

    // split to array, and remove invalid characters
    $array = self::split($str);

    // extract relevant part, and join to make sting again
    return implode(array_slice($array, $start, $length));
  }

  /**
   * Binary safe comparison of two strings from an offset, up to length characters.
   *
   * @param string  $main_str           The main string being compared.
   * @param string  $str                The secondary string being compared.
   * @param int     $offset             The start position for the comparison. If negative, it starts counting from the
   *                                    end of the string.
   * @param int     $length             The length of the comparison. The default value is the largest of the length of
   *                                    the str compared to the length of main_str less the offset.
   * @param boolean $case_insensitivity If case_insensitivity is TRUE, comparison is case insensitive.
   *
   * @return int
   */
  public static function substr_compare($main_str, $str, $offset, $length = 2147483647, $case_insensitivity = false)
  {
    $main_str = self::substr($main_str, $offset, $length);
    $str = self::substr($str, 0, self::strlen($main_str));

    return $case_insensitivity === true ? self::strcasecmp($main_str, $str) : self::strcmp($main_str, $str);
  }

  /**
   * Count the number of substring occurrences
   *
   * @link  http://php.net/manual/en/function.substr-count.php
   *
   * @param string $haystack <p>
   *                         The string to search in
   *                         </p>
   * @param string $needle   <p>
   *                         The substring to search for
   *                         </p>
   * @param int    $offset   [optional] <p>
   *                         The offset where to start counting
   *                         </p>
   * @param int    $length   [optional] <p>
   *                         The maximum length after the specified offset to search for the
   *                         substring. It outputs a warning if the offset plus the length is
   *                         greater than the haystack length.
   *                         </p>
   *
   * @return int This functions returns an integer.
   * @since 4.0
   * @since 5.0
   */
  public static function substr_count($haystack, $needle, $offset = 0, $length = null)
  {
    $haystack = (string)$haystack;
    $needle = (string)$needle;

    if (!isset($haystack[0], $needle[0])) {
      return 0;
    }

    if ($offset || $length) {
      $offset = (int)$offset;
      $length = (int)$length;

      $haystack = self::substr($haystack, $offset, $length);
    }

    self::checkForSupport();

    return \mb_substr_count($haystack, $needle);
  }

  /**
   * Replace text within a portion of a string.
   *
   * source: https://gist.github.com/stemar/8287074
   *
   * @param string|array   $str
   * @param string|array   $replacement
   * @param int|array      $start
   * @param null|int|array $length
   *
   * @return array|string
   */
  public static function substr_replace($str, $replacement, $start, $length = null)
  {
    if (is_array($str)) {
      $num = count($str);

      // $replacement
      if (is_array($replacement)) {
        $replacement = array_slice($replacement, 0, $num);
      } else {
        $replacement = array_pad(array($replacement), $num, $replacement);
      }

      // $start
      if (is_array($start)) {
        $start = array_slice($start, 0, $num);
        foreach ($start as &$valueTmp) {
          $valueTmp = (int)$valueTmp === $valueTmp ? $valueTmp : 0;
        }
        unset($valueTmp);
      } else {
        $start = array_pad(array($start), $num, $start);
      }

      // $length
      if (!isset($length)) {
        $length = array_fill(0, $num, 0);
      } elseif (is_array($length)) {
        $length = array_slice($length, 0, $num);
        foreach ($length as &$valueTmpV2) {
          if (isset($valueTmpV2)) {
            $valueTmpV2 = (int)$valueTmpV2 === $valueTmpV2 ? $valueTmpV2 : $num;
          } else {
            $valueTmpV2 = 0;
          }
        }
        unset($valueTmpV2);
      } else {
        $length = array_pad(array($length), $num, $length);
      }

      // Recursive call
      return array_map(array(__CLASS__, 'substr_replace'), $str, $replacement, $start, $length);
    } else {
      if (is_array($replacement)) {
        if (count($replacement) > 0) {
          $replacement = $replacement[0];
        } else {
          $replacement = '';
        }
      }
    }

    preg_match_all('/./us', (string)$str, $smatches);
    preg_match_all('/./us', (string)$replacement, $rmatches);

    if ($length === null) {
      self::checkForSupport();

      $length = \mb_strlen($str);
    }

    array_splice($smatches[0], $start, $length, $rmatches[0]);

    return implode($smatches[0], null);
  }

  /**
   * Returns a case swapped version of the string.
   *
   * @param string $str
   * @param string $encoding
   *
   * @return string each character's case swapped
   */
  public static function swapCase($str, $encoding = 'UTF-8')
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    $encoding = self::normalizeEncoding($encoding);
    $str = self::clean($str);

    $strSwappedCase = preg_replace_callback(
        '/[\S]/u',
        function ($match) use ($encoding) {
          $marchToUpper = UTF8::strtoupper($match[0], $encoding);

          if ($match[0] === $marchToUpper) {
            return UTF8::strtolower($match[0], $encoding);
          } else {
            return $marchToUpper;
          }
        },
        $str
    );

    return $strSwappedCase;
  }

  /**
   * alias for "UTF8::to_ascii()"
   *
   * @param string $s The input string e.g. a UTF-8 String
   * @param string $subst_chr
   *
   * @return string
   */
  public static function toAscii($s, $subst_chr = '?')
  {
    return self::to_ascii($s, $subst_chr);
  }

  /**
   * alias for "UTF8::to_latin1()"
   *
   * @param $str
   *
   * @return string
   */
  public static function toLatin1($str)
  {
    return self::to_latin1($str);
  }

  /**
   * alias for "UTF8::to_utf8"
   *
   * @param string $str
   *
   * @return string
   */
  public static function toUTF8($str)
  {
    return self::to_utf8($str);
  }

  /**
   * convert to ASCII
   *
   * @param string $s The input string e.g. a UTF-8 String
   * @param string $subst_chr
   *
   * @return string
   */
  public static function to_ascii($s, $subst_chr = '?')
  {
    static $translitExtra = null;

    $s = (string)$s;

    if (!isset($s[0])) {
      return '';
    }

    $s = self::clean($s);

    if (preg_match("/[\x80-\xFF]/", $s)) {
      $s = \Normalizer::normalize($s, \Normalizer::NFKC);

      $glibc = 'glibc' === ICONV_IMPL;

      preg_match_all('/./u', $s, $s);

      /** @noinspection AlterInForeachInspection */
      foreach ($s[0] as &$c) {

        if (!isset($c[1])) {
          continue;
        }

        if ($glibc) {
          $t = iconv('UTF-8', 'ASCII//TRANSLIT', $c);
        } else {
          $t = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $c);

          if ($t !== false && is_string($t)) {
            if (!isset($t[0])) {
              $t = '?';
            } elseif (isset($t[1])) {
              $t = ltrim($t, '\'`"^~');
            }
          }
        }

        if ('?' === $t) {

          if ($translitExtra === null) {
            $translitExtra = (array)self::getData('translit_extra');
          }

          if (isset($translitExtra[$c])) {
            $t = $translitExtra[$c];
          } else {
            $t = \Normalizer::normalize($c, \Normalizer::NFD);

            if ($t[0] < "\x80") {
              $t = $t[0];
            } else {
              $t = $subst_chr;
            }
          }
        }

        if ('?' === $t) {
          $t = self::str_transliterate($c, $subst_chr);
        }

        $c = $t;
      }

      $s = implode('', $s[0]);
    }

    return $s;
  }

  /**
   * alias for "UTF8::to_win1252()"
   *
   * @param   string $str
   *
   * @return  array|string
   */
  public static function to_iso8859($str)
  {
    return self::to_win1252($str);
  }

  /**
   * alias for "UTF8::to_win1252()"
   *
   * @param string|array $str
   *
   * @return string|array
   */
  public static function to_latin1($str)
  {
    return self::to_win1252($str);
  }

  /**
   * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
   *
   * - It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859-1.
   *
   * - It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
   *
   * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
   *    are followed by any of these:  ("group B")
   *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
   * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
   * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB)
   * is also a valid unicode character, and will be left unchanged.
   *
   * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
   * 3) when any of these: ðñòó  are followed by THREE chars from group B.
   *
   * @param string|array $str Any string or array.
   *
   * @return string The same string, but UTF8 encoded.
   */
  public static function to_utf8($str)
  {
    if (is_array($str)) {
      foreach ($str as $k => $v) {
        /** @noinspection AlterInForeachInspection */
        $str[$k] = self::to_utf8($v);
      }

      return $str;
    }

    $str = (string)$str;

    if (!isset($str[0])) {
      return $str;
    }

    $max = strlen($str);
    $buf = '';

    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $max; $i++) {
      $c1 = $str[$i];

      if ($c1 >= "\xc0") { // should be converted to UTF8, if it's not UTF8 already
        $c2 = $i + 1 >= $max ? "\x00" : $str[$i + 1];
        $c3 = $i + 2 >= $max ? "\x00" : $str[$i + 2];
        $c4 = $i + 3 >= $max ? "\x00" : $str[$i + 3];

        if ($c1 >= "\xc0" & $c1 <= "\xdf") { // looks like 2 bytes UTF8

          if ($c2 >= "\x80" && $c2 <= "\xbf") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2;
            $i++;
          } else { // not valid UTF8 - convert it
            $cc1 = (chr(ord($c1) / 64) | "\xc0");
            $cc2 = ($c1 & "\x3f") | "\x80";
            $buf .= $cc1 . $cc2;
          }

        } elseif ($c1 >= "\xe0" & $c1 <= "\xef") { // looks like 3 bytes UTF8

          if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2 . $c3;
            $i += 2;
          } else { // not valid UTF8 - convert it
            $cc1 = (chr(ord($c1) / 64) | "\xc0");
            $cc2 = ($c1 & "\x3f") | "\x80";
            $buf .= $cc1 . $cc2;
          }

        } elseif ($c1 >= "\xf0" & $c1 <= "\xf7") { // looks like 4 bytes UTF8

          if ($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf") { // yeah, almost sure it's UTF8 already
            $buf .= $c1 . $c2 . $c3 . $c4;
            $i += 3;
          } else { // not valid UTF8 - convert it
            $cc1 = (chr(ord($c1) / 64) | "\xc0");
            $cc2 = ($c1 & "\x3f") | "\x80";
            $buf .= $cc1 . $cc2;
          }

        } else { // doesn't look like UTF8, but should be converted
          $cc1 = (chr(ord($c1) / 64) | "\xc0");
          $cc2 = (($c1 & "\x3f") | "\x80");
          $buf .= $cc1 . $cc2;
        }

      } elseif (($c1 & "\xc0") === "\x80") { // needs conversion

        $ordC1 = ord($c1);
        if (isset(self::$win1252ToUtf8[$ordC1])) { // found in Windows-1252 special cases
          $buf .= self::$win1252ToUtf8[$ordC1];
        } else {
          $cc1 = (chr($ordC1 / 64) | "\xc0");
          $cc2 = (($c1 & "\x3f") | "\x80");
          $buf .= $cc1 . $cc2;
        }

      } else { // it doesn't need conversion
        $buf .= $c1;
      }
    }

    self::checkForSupport();

    // decode unicode escape sequences
    $buf = preg_replace_callback(
        '/\\\\u([0-9a-f]{4})/i',
        function ($match) {
          return \mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        },
        $buf
    );

    // decode UTF-8 codepoints
    $buf = preg_replace_callback(
        '/&#\d{2,4};/',
        function ($match) {
          return \mb_convert_encoding($match[0], 'UTF-8', 'HTML-ENTITIES');
        },
        $buf
    );

    return $buf;
  }

  /**
   * Convert a string into "win1252"-encoding.
   *
   * @param  string|array $str
   *
   * @return string|array
   */
  protected static function to_win1252($str)
  {
    if (is_array($str)) {

      foreach ($str as $k => $v) {
        /** @noinspection AlterInForeachInspection */
        $str[$k] = self::to_win1252($v);
      }

      return $str;
    }

    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    return self::utf8_decode($str);
  }

  /**
   * Strip whitespace or other characters from beginning or end of a UTF-8 string.
   *
   * INFO: This is slower then "trim()"
   *
   * We can only use the original-function, if we use <= 7-Bit in the string / chars
   * but the check for ACSII (7-Bit) cost more time, then we can safe here.
   *
   * @param    string $str   The string to be trimmed
   * @param    string $chars Optional characters to be stripped
   *
   * @return   string The trimmed string
   */
  public static function trim($str = '', $chars = INF)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    // Info: http://nadeausoftware.com/articles/2007/9/php_tip_how_strip_punctuation_characters_web_page#Unicodecharactercategories
    if ($chars === INF || !$chars) {
      return preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $str);
    }

    return self::rtrim(self::ltrim($str, $chars), $chars);
  }

  /**
   * Makes string's first char uppercase.
   *
   * @param    string $str The input string
   *
   * @return   string The resulting string
   */
  public static function ucfirst($str)
  {
    return self::strtoupper(self::substr($str, 0, 1)) . self::substr($str, 1);
  }

  /**
   * alias for "UTF8::ucfirst"
   *
   * @param $str
   *
   * @return string
   */
  public static function ucword($str)
  {
    return self::ucfirst($str);
  }

  /**
   * Uppercase for all words in the string.
   *
   * @param  string $str
   * @param array   $exceptions
   *
   * @return string
   */
  public static function ucwords($str, $exceptions = array())
  {
    if (!$str) {
      return '';
    }

    // init
    $words = explode(' ', $str);
    $newwords = array();

    if (count($exceptions) > 0) {
      $useExceptions = true;
    } else {
      $useExceptions = false;
    }

    foreach ($words as $word) {
      if (
          ($useExceptions === false)
          ||
          (
              $useExceptions === true
              &&
              !in_array($word, $exceptions, true)
          )
      ) {
        $word = self::ucfirst($word);
      }
      $newwords[] = $word;
    }

    return self::ucfirst(implode(' ', $newwords));
  }

  /**
   * Multi decode html entity & fix urlencoded-win1252-chars.
   *
   * e.g:
   * 'D&#252;sseldorf'               => 'Düsseldorf'
   * 'D%FCsseldorf'                  => 'Düsseldorf'
   * 'D&#xFC;sseldorf'               => 'Düsseldorf'
   * 'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
   * 'DÃ¼sseldorf'                   => 'Düsseldorf'
   * 'D%C3%BCsseldorf'               => 'Düsseldorf'
   * 'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
   * 'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'
   *
   * @param string $str
   *
   * @return string
   */
  public static function urldecode($str)
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    $str = preg_replace('/%u([0-9a-f]{3,4})/i', '&#x\\1;', urldecode($str));

    $flags = Bootup::is_php('5.4') ? ENT_QUOTES | ENT_HTML5 : ENT_QUOTES;

    $str = self::fix_simple_utf8(
        rawurldecode(
            self::html_entity_decode(
                self::to_utf8($str),
                $flags
            )
        )
    );

    return (string)$str;
  }

  /**
   * Return a array with "urlencoded"-win1252 -> UTF-8
   *
   * @return mixed
   */
  public static function urldecode_fix_win1252_chars()
  {
    static $array = array(
        '%20' => ' ',
        '%21' => '!',
        '%22' => '"',
        '%23' => '#',
        '%24' => '$',
        '%25' => '%',
        '%26' => '&',
        '%27' => "'",
        '%28' => '(',
        '%29' => ')',
        '%2A' => '*',
        '%2B' => '+',
        '%2C' => ',',
        '%2D' => '-',
        '%2E' => '.',
        '%2F' => '/',
        '%30' => '0',
        '%31' => '1',
        '%32' => '2',
        '%33' => '3',
        '%34' => '4',
        '%35' => '5',
        '%36' => '6',
        '%37' => '7',
        '%38' => '8',
        '%39' => '9',
        '%3A' => ':',
        '%3B' => ';',
        '%3C' => '<',
        '%3D' => '=',
        '%3E' => '>',
        '%3F' => '?',
        '%40' => '@',
        '%41' => 'A',
        '%42' => 'B',
        '%43' => 'C',
        '%44' => 'D',
        '%45' => 'E',
        '%46' => 'F',
        '%47' => 'G',
        '%48' => 'H',
        '%49' => 'I',
        '%4A' => 'J',
        '%4B' => 'K',
        '%4C' => 'L',
        '%4D' => 'M',
        '%4E' => 'N',
        '%4F' => 'O',
        '%50' => 'P',
        '%51' => 'Q',
        '%52' => 'R',
        '%53' => 'S',
        '%54' => 'T',
        '%55' => 'U',
        '%56' => 'V',
        '%57' => 'W',
        '%58' => 'X',
        '%59' => 'Y',
        '%5A' => 'Z',
        '%5B' => '[',
        '%5C' => '\\',
        '%5D' => ']',
        '%5E' => '^',
        '%5F' => '_',
        '%60' => '`',
        '%61' => 'a',
        '%62' => 'b',
        '%63' => 'c',
        '%64' => 'd',
        '%65' => 'e',
        '%66' => 'f',
        '%67' => 'g',
        '%68' => 'h',
        '%69' => 'i',
        '%6A' => 'j',
        '%6B' => 'k',
        '%6C' => 'l',
        '%6D' => 'm',
        '%6E' => 'n',
        '%6F' => 'o',
        '%70' => 'p',
        '%71' => 'q',
        '%72' => 'r',
        '%73' => 's',
        '%74' => 't',
        '%75' => 'u',
        '%76' => 'v',
        '%77' => 'w',
        '%78' => 'x',
        '%79' => 'y',
        '%7A' => 'z',
        '%7B' => '{',
        '%7C' => '|',
        '%7D' => '}',
        '%7E' => '~',
        '%7F' => '',
        '%80' => '`',
        '%81' => '',
        '%82' => '‚',
        '%83' => 'ƒ',
        '%84' => '„',
        '%85' => '…',
        '%86' => '†',
        '%87' => '‡',
        '%88' => 'ˆ',
        '%89' => '‰',
        '%8A' => 'Š',
        '%8B' => '‹',
        '%8C' => 'Œ',
        '%8D' => '',
        '%8E' => 'Ž',
        '%8F' => '',
        '%90' => '',
        '%91' => '‘',
        '%92' => '’',
        '%93' => '“',
        '%94' => '”',
        '%95' => '•',
        '%96' => '–',
        '%97' => '—',
        '%98' => '˜',
        '%99' => '™',
        '%9A' => 'š',
        '%9B' => '›',
        '%9C' => 'œ',
        '%9D' => '',
        '%9E' => 'ž',
        '%9F' => 'Ÿ',
        '%A0' => '',
        '%A1' => '¡',
        '%A2' => '¢',
        '%A3' => '£',
        '%A4' => '¤',
        '%A5' => '¥',
        '%A6' => '¦',
        '%A7' => '§',
        '%A8' => '¨',
        '%A9' => '©',
        '%AA' => 'ª',
        '%AB' => '«',
        '%AC' => '¬',
        '%AD' => '',
        '%AE' => '®',
        '%AF' => '¯',
        '%B0' => '°',
        '%B1' => '±',
        '%B2' => '²',
        '%B3' => '³',
        '%B4' => '´',
        '%B5' => 'µ',
        '%B6' => '¶',
        '%B7' => '·',
        '%B8' => '¸',
        '%B9' => '¹',
        '%BA' => 'º',
        '%BB' => '»',
        '%BC' => '¼',
        '%BD' => '½',
        '%BE' => '¾',
        '%BF' => '¿',
        '%C0' => 'À',
        '%C1' => 'Á',
        '%C2' => 'Â',
        '%C3' => 'Ã',
        '%C4' => 'Ä',
        '%C5' => 'Å',
        '%C6' => 'Æ',
        '%C7' => 'Ç',
        '%C8' => 'È',
        '%C9' => 'É',
        '%CA' => 'Ê',
        '%CB' => 'Ë',
        '%CC' => 'Ì',
        '%CD' => 'Í',
        '%CE' => 'Î',
        '%CF' => 'Ï',
        '%D0' => 'Ð',
        '%D1' => 'Ñ',
        '%D2' => 'Ò',
        '%D3' => 'Ó',
        '%D4' => 'Ô',
        '%D5' => 'Õ',
        '%D6' => 'Ö',
        '%D7' => '×',
        '%D8' => 'Ø',
        '%D9' => 'Ù',
        '%DA' => 'Ú',
        '%DB' => 'Û',
        '%DC' => 'Ü',
        '%DD' => 'Ý',
        '%DE' => 'Þ',
        '%DF' => 'ß',
        '%E0' => 'à',
        '%E1' => 'á',
        '%E2' => 'â',
        '%E3' => 'ã',
        '%E4' => 'ä',
        '%E5' => 'å',
        '%E6' => 'æ',
        '%E7' => 'ç',
        '%E8' => 'è',
        '%E9' => 'é',
        '%EA' => 'ê',
        '%EB' => 'ë',
        '%EC' => 'ì',
        '%ED' => 'í',
        '%EE' => 'î',
        '%EF' => 'ï',
        '%F0' => 'ð',
        '%F1' => 'ñ',
        '%F2' => 'ò',
        '%F3' => 'ó',
        '%F4' => 'ô',
        '%F5' => 'õ',
        '%F6' => 'ö',
        '%F7' => '÷',
        '%F8' => 'ø',
        '%F9' => 'ù',
        '%FA' => 'ú',
        '%FB' => 'û',
        '%FC' => 'ü',
        '%FD' => 'ý',
        '%FE' => 'þ',
        '%FF' => 'ÿ',
    );

    return $array;
  }

  /**
   * Decodes an UTF-8 string to ISO-8859-1.
   *
   * @param string $str
   *
   * @return string
   */
  public static function utf8_decode($str)
  {
    static $utf8ToWin1252Keys = null;
    static $utf8ToWin1252Values = null;

    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    // init
    self::checkForSupport();

    $str = self::to_utf8($str);

    if ($utf8ToWin1252Keys === null) {
      $utf8ToWin1252Keys = array_keys(self::$utf8ToWin1252);
      $utf8ToWin1252Values = array_values(self::$utf8ToWin1252);
    }

    return Xml::utf8_decode(str_replace($utf8ToWin1252Keys, $utf8ToWin1252Values, $str));
  }

  /**
   * Encodes an ISO-8859-1 string to UTF-8.
   *
   * @param string $str
   *
   * @return string
   */
  public static function utf8_encode($str)
  {
    $str = \utf8_encode($str);

    if (false === strpos($str, "\xC2")) {
      return $str;
    } else {

      static $cp1252ToUtf8Keys = null;
      static $cp1252ToUtf8Values = null;

      if ($cp1252ToUtf8Keys === null) {
        $cp1252ToUtf8Keys = array_keys(self::$cp1252ToUtf8);
        $cp1252ToUtf8Values = array_values(self::$cp1252ToUtf8);
      }

      return str_replace($cp1252ToUtf8Keys, $cp1252ToUtf8Values, $str);
    }
  }

  /**
   * fix -> utf8-win1252 chars
   *
   * If you received an UTF-8 string that was converted from Windows-1252 as it was ISO-8859-1
   * (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
   * See: http://en.wikipedia.org/wiki/Windows-1252
   *
   * @deprecated use "UTF8::fix_simple_utf8()"
   *
   * @param   string $str
   *
   * @return  string
   */
  public static function utf8_fix_win1252_chars($str)
  {
    return self::fix_simple_utf8($str);
  }

  /**
   * Returns an array with all utf8 whitespace characters.
   *
   * @see   : http://www.bogofilter.org/pipermail/bogofilter/2003-March/001889.html
   *
   * @author: Derek E. derek.isname@gmail.com
   *
   * @return array an array with all known whitespace characters as values and the type of whitespace as keys
   *         as defined in above URL
   */
  public static function whitespace_table()
  {
    return self::$whitespaceTable;
  }

  /**
   * Limit the number of words in a string.
   *
   * @param  string $str
   * @param  int    $words
   * @param  string $strAddOn
   *
   * @return string
   */
  public static function words_limit($str, $words = 100, $strAddOn = '...')
  {
    $str = (string)$str;

    if (!isset($str[0])) {
      return '';
    }

    $words = (int)$words;

    if ($words < 1) {
      return '';
    }

    preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $str, $matches);

    if (
        !isset($matches[0])
        ||
        self::strlen($str) === self::strlen($matches[0])
    ) {
      return $str;
    }

    return self::rtrim($matches[0]) . $strAddOn;
  }

  /**
   * Wraps a string to a given number of characters
   *
   * @link  http://php.net/manual/en/function.wordwrap.php
   *
   * @param string $str   <p>
   *                      The input string.
   *                      </p>
   * @param int    $width [optional] <p>
   *                      The column width.
   *                      </p>
   * @param string $break [optional] <p>
   *                      The line is broken using the optional
   *                      break parameter.
   *                      </p>
   * @param bool   $cut   [optional] <p>
   *                      If the cut is set to true, the string is
   *                      always wrapped at or before the specified width. So if you have
   *                      a word that is larger than the given width, it is broken apart.
   *                      (See second example).
   *                      </p>
   *
   * @return string the given string wrapped at the specified column.
   * @since 4.0.2
   * @since 5.0
   */
  public static function wordwrap($str, $width = 75, $break = "\n", $cut = false)
  {
    $str = (string)$str;
    $break = (string)$break;

    if (!isset($str[0], $break[0])) {
      return '';
    }

    $w = '';
    $strSplit = explode($break, $str);
    $count = count($strSplit);

    if (1 === $count && '' === $strSplit[0]) {
      return '';
    }

    $chars = array();
    /** @noinspection ForeachInvariantsInspection */
    for ($i = 0; $i < $count; ++$i) {

      if ($i) {
        $chars[] = $break;
        $w .= '#';
      }

      $c = $strSplit[$i];
      unset($strSplit[$i]);

      foreach (self::split($c) as $c) {
        $chars[] = $c;
        $w .= ' ' === $c ? ' ' : '?';
      }
    }

    $strReturn = '';
    $j = 0;
    $b = $i = -1;
    $w = wordwrap($w, $width, '#', $cut);

    while (false !== $b = self::strpos($w, '#', $b + 1)) {
      for (++$i; $i < $b; ++$i) {
        $strReturn .= $chars[$j];
        unset($chars[$j++]);
      }

      if ($break === $chars[$j] || ' ' === $chars[$j]) {
        unset($chars[$j++]);
      }

      $strReturn .= $break;
    }

    return $strReturn . implode('', $chars);
  }

  /**
   * Returns an array of Unicode White Space characters.
   *
   * @return   array An array with numeric code point as key and White Space Character as value.
   */
  public static function ws()
  {
    return self::$whitespace;
  }

}
