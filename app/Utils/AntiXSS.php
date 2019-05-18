<?php

namespace App\Utils;

/**
 * Anti XSS library
 *
 * ported from "CodeIgniter"
 *
 * @author      EllisLab Dev Team
 * @author      Lars Moelleken
 * @copyright   Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright   Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
class AntiXSS
{

    /**
     * @var array
     */
    protected static $entitiesFallback = array(
        "\t" => '&Tab;',
        "\n" => '&NewLine;',
        '!' => '&excl;',
        '"' => '&quot;',
        '#' => '&num;',
        '$' => '&dollar;',
        '%' => '&percnt;',
        '&' => '&amp;',
        "'" => '&apos;',
        '(' => '&lpar;',
        ')' => '&rpar;',
        '*' => '&ast;',
        '+' => '&plus;',
        ',' => '&comma;',
        '.' => '&period;',
        '/' => '&sol;',
        ':' => '&colon;',
        ';' => '&semi;',
        '<' => '&lt;',
        '<⃒' => '&nvlt;',
        '=' => '&equals;',
        '=⃥' => '&bne;',
        '>' => '&gt;',
        '>⃒' => '&nvgt',
        '?' => '&quest;',
        '@' => '&commat;',
        '[' => '&lbrack;',
        ']' => '&rsqb;',
        '^' => '&Hat;',
        '_' => '&lowbar;',
        '`' => '&grave;',
        'fj' => '&fjlig;',
        '{' => '&lbrace;',
        '|' => '&vert;',
        '}' => '&rcub;',
        ' ' => '&nbsp;',
        '¡' => '&iexcl;',
        '¢' => '&cent;',
        '£' => '&pound;',
        '¤' => '&curren;',
        '¥' => '&yen;',
        '¦' => '&brvbar;',
        '§' => '&sect;',
        '¨' => '&DoubleDot;',
        '©' => '&copy;',
        'ª' => '&ordf;',
        '«' => '&laquo;',
        '¬' => '&not;',
        '­' => '&shy;',
        '®' => '&reg;',
        '¯' => '&macr;',
        '°' => '&deg;',
        '±' => '&plusmn;',
        '²' => '&sup2;',
        '³' => '&sup3;',
        '´' => '&DiacriticalAcute;',
        'µ' => '&micro;',
        '¶' => '&para;',
        '·' => '&CenterDot;',
        '¸' => '&Cedilla;',
        '¹' => '&sup1;',
        'º' => '&ordm;',
        '»' => '&raquo;',
        '¼' => '&frac14;',
        '½' => '&half;',
        '¾' => '&frac34;',
        '¿' => '&iquest;',
        'À' => '&Agrave;',
        'Á' => '&Aacute;',
        'Â' => '&Acirc;',
        'Ã' => '&Atilde;',
        'Ä' => '&Auml;',
        'Å' => '&Aring;',
        'Æ' => '&AElig;',
        'Ç' => '&Ccedil;',
        'È' => '&Egrave;',
        'É' => '&Eacute;',
        'Ê' => '&Ecirc;',
        'Ë' => '&Euml;',
        'Ì' => '&Igrave;',
        'Í' => '&Iacute;',
        'Î' => '&Icirc;',
        'Ï' => '&Iuml;',
        'Ð' => '&ETH;',
        'Ñ' => '&Ntilde;',
        'Ò' => '&Ograve;',
        'Ó' => '&Oacute;',
        'Ô' => '&Ocirc;',
        'Õ' => '&Otilde;',
        'Ö' => '&Ouml;',
        '×' => '&times;',
        'Ø' => '&Oslash;',
        'Ù' => '&Ugrave;',
        'Ú' => '&Uacute;',
        'Û' => '&Ucirc;',
        'Ü' => '&Uuml;',
        'Ý' => '&Yacute;',
        'Þ' => '&THORN;',
        'ß' => '&szlig;',
        'à' => '&agrave;',
        'á' => '&aacute;',
        'â' => '&acirc;',
        'ã' => '&atilde;',
        'ä' => '&auml;',
        'å' => '&aring;',
        'æ' => '&aelig;',
        'ç' => '&ccedil;',
        'è' => '&egrave;',
        'é' => '&eacute;',
        'ê' => '&ecirc;',
        'ë' => '&euml;',
        'ì' => '&igrave;',
        'í' => '&iacute;',
        'î' => '&icirc;',
        'ï' => '&iuml;',
        'ð' => '&eth;',
        'ñ' => '&ntilde;',
        'ò' => '&ograve;',
        'ó' => '&oacute;',
        'ô' => '&ocirc;',
        'õ' => '&otilde;',
        'ö' => '&ouml;',
        '÷' => '&divide;',
        'ø' => '&oslash;',
        'ù' => '&ugrave;',
        'ú' => '&uacute;',
        'û' => '&ucirc;',
        'ü' => '&uuml;',
        'ý' => '&yacute;',
        'þ' => '&thorn;',
        'ÿ' => '&yuml;',
        'Ā' => '&Amacr;',
        'ā' => '&amacr;',
        'Ă' => '&Abreve;',
        'ă' => '&abreve;',
        'Ą' => '&Aogon;',
        'ą' => '&aogon;',
        'Ć' => '&Cacute;',
        'ć' => '&cacute;',
        'Ĉ' => '&Ccirc;',
        'ĉ' => '&ccirc;',
        'Ċ' => '&Cdot;',
        'ċ' => '&cdot;',
        'Č' => '&Ccaron;',
        'č' => '&ccaron;',
        'Ď' => '&Dcaron;',
        'ď' => '&dcaron;',
        'Đ' => '&Dstrok;',
        'đ' => '&dstrok;',
        'Ē' => '&Emacr;',
        'ē' => '&emacr;',
        'Ė' => '&Edot;',
        'ė' => '&edot;',
        'Ę' => '&Eogon;',
        'ę' => '&eogon;',
        'Ě' => '&Ecaron;',
        'ě' => '&ecaron;',
        'Ĝ' => '&Gcirc;',
        'ĝ' => '&gcirc;',
        'Ğ' => '&Gbreve;',
        'ğ' => '&gbreve;',
        'Ġ' => '&Gdot;',
        'ġ' => '&gdot;',
        'Ģ' => '&Gcedil;',
        'Ĥ' => '&Hcirc;',
        'ĥ' => '&hcirc;',
        'Ħ' => '&Hstrok;',
        'ħ' => '&hstrok;',
        'Ĩ' => '&Itilde;',
        'ĩ' => '&itilde;',
        'Ī' => '&Imacr;',
        'ī' => '&imacr;',
        'Į' => '&Iogon;',
        'į' => '&iogon;',
        'İ' => '&Idot;',
        'ı' => '&inodot;',
        'Ĳ' => '&IJlig;',
        'ĳ' => '&ijlig;',
        'Ĵ' => '&Jcirc;',
        'ĵ' => '&jcirc;',
        'Ķ' => '&Kcedil;',
        'ķ' => '&kcedil;',
        'ĸ' => '&kgreen;',
        'Ĺ' => '&Lacute;',
        'ĺ' => '&lacute;',
        'Ļ' => '&Lcedil;',
        'ļ' => '&lcedil;',
        'Ľ' => '&Lcaron;',
        'ľ' => '&lcaron;',
        'Ŀ' => '&Lmidot;',
        'ŀ' => '&lmidot;',
        'Ł' => '&Lstrok;',
        'ł' => '&lstrok;',
        'Ń' => '&Nacute;',
        'ń' => '&nacute;',
        'Ņ' => '&Ncedil;',
        'ņ' => '&ncedil;',
        'Ň' => '&Ncaron;',
        'ň' => '&ncaron;',
        'ŉ' => '&napos;',
        'Ŋ' => '&ENG;',
        'ŋ' => '&eng;',
        'Ō' => '&Omacr;',
        'ō' => '&omacr;',
        'Ő' => '&Odblac;',
        'ő' => '&odblac;',
        'Œ' => '&OElig;',
        'œ' => '&oelig;',
        'Ŕ' => '&Racute;',
        'ŕ' => '&racute;',
        'Ŗ' => '&Rcedil;',
        'ŗ' => '&rcedil;',
        'Ř' => '&Rcaron;',
        'ř' => '&rcaron;',
        'Ś' => '&Sacute;',
        'ś' => '&sacute;',
        'Ŝ' => '&Scirc;',
        'ŝ' => '&scirc;',
        'Ş' => '&Scedil;',
        'ş' => '&scedil;',
        'Š' => '&Scaron;',
        'š' => '&scaron;',
        'Ţ' => '&Tcedil;',
        'ţ' => '&tcedil;',
        'Ť' => '&Tcaron;',
        'ť' => '&tcaron;',
        'Ŧ' => '&Tstrok;',
        'ŧ' => '&tstrok;',
        'Ũ' => '&Utilde;',
        'ũ' => '&utilde;',
        'Ū' => '&Umacr;',
        'ū' => '&umacr;',
        'Ŭ' => '&Ubreve;',
        'ŭ' => '&ubreve;',
        'Ů' => '&Uring;',
        'ů' => '&uring;',
        'Ű' => '&Udblac;',
        'ű' => '&udblac;',
        'Ų' => '&Uogon;',
        'ų' => '&uogon;',
        'Ŵ' => '&Wcirc;',
        'ŵ' => '&wcirc;',
        'Ŷ' => '&Ycirc;',
        'ŷ' => '&ycirc;',
        'Ÿ' => '&Yuml;',
        'Ź' => '&Zacute;',
        'ź' => '&zacute;',
        'Ż' => '&Zdot;',
        'ż' => '&zdot;',
        'Ž' => '&Zcaron;',
        'ž' => '&zcaron;',
        'ƒ' => '&fnof;',
        'Ƶ' => '&imped;',
        'ǵ' => '&gacute;',
        'ȷ' => '&jmath;',
        'ˆ' => '&circ;',
        'ˇ' => '&Hacek;',
        '˘' => '&Breve;',
        '˙' => '&dot;',
        '˚' => '&ring;',
        '˛' => '&ogon;',
        '˜' => '&DiacriticalTilde;',
        '˝' => '&DiacriticalDoubleAcute;',
        '̑' => '&DownBreve;',
        'Α' => '&Alpha;',
        'Β' => '&Beta;',
        'Γ' => '&Gamma;',
        'Δ' => '&Delta;',
        'Ε' => '&Epsilon;',
        'Ζ' => '&Zeta;',
        'Η' => '&Eta;',
        'Θ' => '&Theta;',
        'Ι' => '&Iota;',
        'Κ' => '&Kappa;',
        'Λ' => '&Lambda;',
        'Μ' => '&Mu;',
        'Ν' => '&Nu;',
        'Ξ' => '&Xi;',
        'Ο' => '&Omicron;',
        'Π' => '&Pi;',
        'Ρ' => '&Rho;',
        'Σ' => '&Sigma;',
        'Τ' => '&Tau;',
        'Υ' => '&Upsilon;',
        'Φ' => '&Phi;',
        'Χ' => '&Chi;',
        'Ψ' => '&Psi;',
        'Ω' => '&Omega;',
        'α' => '&alpha;',
        'β' => '&beta;',
        'γ' => '&gamma;',
        'δ' => '&delta;',
        'ε' => '&epsi;',
        'ζ' => '&zeta;',
        'η' => '&eta;',
        'θ' => '&theta;',
        'ι' => '&iota;',
        'κ' => '&kappa;',
        'λ' => '&lambda;',
        'μ' => '&mu;',
        'ν' => '&nu;',
        'ξ' => '&xi;',
        'ο' => '&omicron;',
        'π' => '&pi;',
        'ρ' => '&rho;',
        'ς' => '&sigmav;',
        'σ' => '&sigma;',
        'τ' => '&tau;',
        'υ' => '&upsi;',
        'φ' => '&phi;',
        'χ' => '&chi;',
        'ψ' => '&psi;',
        'ω' => '&omega;',
        'ϑ' => '&thetasym;',
        'ϒ' => '&upsih;',
        'ϕ' => '&straightphi;',
        'ϖ' => '&piv;',
        'Ϝ' => '&Gammad;',
        'ϝ' => '&gammad;',
        'ϰ' => '&varkappa;',
        'ϱ' => '&rhov;',
        'ϵ' => '&straightepsilon;',
        '϶' => '&backepsilon;',
        'Ё' => '&IOcy;',
        'Ђ' => '&DJcy;',
        'Ѓ' => '&GJcy;',
        'Є' => '&Jukcy;',
        'Ѕ' => '&DScy;',
        'І' => '&Iukcy;',
        'Ї' => '&YIcy;',
        'Ј' => '&Jsercy;',
        'Љ' => '&LJcy;',
        'Њ' => '&NJcy;',
        'Ћ' => '&TSHcy;',
        'Ќ' => '&KJcy;',
        'Ў' => '&Ubrcy;',
        'Џ' => '&DZcy;',
        'А' => '&Acy;',
        'Б' => '&Bcy;',
        'В' => '&Vcy;',
        'Г' => '&Gcy;',
        'Д' => '&Dcy;',
        'Е' => '&IEcy;',
        'Ж' => '&ZHcy;',
        'З' => '&Zcy;',
        'И' => '&Icy;',
        'Й' => '&Jcy;',
        'К' => '&Kcy;',
        'Л' => '&Lcy;',
        'М' => '&Mcy;',
        'Н' => '&Ncy;',
        'О' => '&Ocy;',
        'П' => '&Pcy;',
        'Р' => '&Rcy;',
        'С' => '&Scy;',
        'Т' => '&Tcy;',
        'У' => '&Ucy;',
        'Ф' => '&Fcy;',
        'Х' => '&KHcy;',
        'Ц' => '&TScy;',
        'Ч' => '&CHcy;',
        'Ш' => '&SHcy;',
        'Щ' => '&SHCHcy;',
        'Ъ' => '&HARDcy;',
        'Ы' => '&Ycy;',
        'Ь' => '&SOFTcy;',
        'Э' => '&Ecy;',
        'Ю' => '&YUcy;',
        'Я' => '&YAcy;',
        'а' => '&acy;',
        'б' => '&bcy;',
        'в' => '&vcy;',
        'г' => '&gcy;',
        'д' => '&dcy;',
        'е' => '&iecy;',
        'ж' => '&zhcy;',
        'з' => '&zcy;',
        'и' => '&icy;',
        'й' => '&jcy;',
        'к' => '&kcy;',
        'л' => '&lcy;',
        'м' => '&mcy;',
        'н' => '&ncy;',
        'о' => '&ocy;',
        'п' => '&pcy;',
        'р' => '&rcy;',
        'с' => '&scy;',
        'т' => '&tcy;',
        'у' => '&ucy;',
        'ф' => '&fcy;',
        'х' => '&khcy;',
        'ц' => '&tscy;',
        'ч' => '&chcy;',
        'ш' => '&shcy;',
        'щ' => '&shchcy;',
        'ъ' => '&hardcy;',
        'ы' => '&ycy;',
        'ь' => '&softcy;',
        'э' => '&ecy;',
        'ю' => '&yucy;',
        'я' => '&yacy;',
        'ё' => '&iocy;',
        'ђ' => '&djcy;',
        'ѓ' => '&gjcy;',
        'є' => '&jukcy;',
        'ѕ' => '&dscy;',
        'і' => '&iukcy;',
        'ї' => '&yicy;',
        'ј' => '&jsercy;',
        'љ' => '&ljcy;',
        'њ' => '&njcy;',
        'ћ' => '&tshcy;',
        'ќ' => '&kjcy;',
        'ў' => '&ubrcy;',
        'џ' => '&dzcy;',
        ' ' => '&ensp;',
        ' ' => '&emsp;',
        ' ' => '&emsp13;',
        ' ' => '&emsp14;',
        ' ' => '&numsp;',
        ' ' => '&puncsp;',
        ' ' => '&ThinSpace;',
        ' ' => '&hairsp;',
        '​' => '&ZeroWidthSpace;',
        '‌' => '&zwnj;',
        '‍' => '&zwj;',
        '‎' => '&lrm;',
        '‏' => '&rlm;',
        '‐' => '&hyphen;',
        '–' => '&ndash;',
        '—' => '&mdash;',
        '―' => '&horbar;',
        '‖' => '&Verbar;',
        '‘' => '&OpenCurlyQuote;',
        '’' => '&rsquo;',
        '‚' => '&sbquo;',
        '“' => '&OpenCurlyDoubleQuote;',
        '”' => '&rdquo;',
        '„' => '&bdquo;',
        '†' => '&dagger;',
        '‡' => '&Dagger;',
        '•' => '&bull;',
        '‥' => '&nldr;',
        '…' => '&hellip;',
        '‰' => '&permil;',
        '‱' => '&pertenk;',
        '′' => '&prime;',
        '″' => '&Prime;',
        '‴' => '&tprime;',
        '‵' => '&backprime;',
        '‹' => '&lsaquo;',
        '›' => '&rsaquo;',
        '‾' => '&oline;',
        '⁁' => '&caret;',
        '⁃' => '&hybull;',
        '⁄' => '&frasl;',
        '⁏' => '&bsemi;',
        '⁗' => '&qprime;',
        ' ' => '&MediumSpace;',
        '  ' => '&ThickSpace;',
        '⁠' => '&NoBreak;',
        '⁡' => '&af;',
        '⁢' => '&InvisibleTimes;',
        '⁣' => '&ic;',
        '€' => '&euro;',
        '⃛' => '&TripleDot;',
        '⃜' => '&DotDot;',
        'ℂ' => '&complexes;',
        '℅' => '&incare;',
        'ℊ' => '&gscr;',
        'ℋ' => '&HilbertSpace;',
        'ℌ' => '&Hfr;',
        'ℍ' => '&Hopf;',
        'ℎ' => '&planckh;',
        'ℏ' => '&planck;',
        'ℐ' => '&imagline;',
        'ℑ' => '&Ifr;',
        'ℒ' => '&lagran;',
        'ℓ' => '&ell;',
        'ℕ' => '&naturals;',
        '№' => '&numero;',
        '℗' => '&copysr;',
        '℘' => '&wp;',
        'ℙ' => '&primes;',
        'ℚ' => '&rationals;',
        'ℛ' => '&realine;',
        'ℜ' => '&Rfr;',
        'ℝ' => '&Ropf;',
        '℞' => '&rx;',
        '™' => '&trade;',
        'ℤ' => '&Zopf;',
        '℧' => '&mho;',
        'ℨ' => '&Zfr;',
        '℩' => '&iiota;',
        'ℬ' => '&Bscr;',
        'ℭ' => '&Cfr;',
        'ℯ' => '&escr;',
        'ℰ' => '&expectation;',
        'ℱ' => '&Fouriertrf;',
        'ℳ' => '&Mellintrf;',
        'ℴ' => '&orderof;',
        'ℵ' => '&aleph;',
        'ℶ' => '&beth;',
        'ℷ' => '&gimel;',
        'ℸ' => '&daleth;',
        'ⅅ' => '&CapitalDifferentialD;',
        'ⅆ' => '&DifferentialD;',
        'ⅇ' => '&exponentiale;',
        'ⅈ' => '&ImaginaryI;',
        '⅓' => '&frac13;',
        '⅔' => '&frac23;',
        '⅕' => '&frac15;',
        '⅖' => '&frac25;',
        '⅗' => '&frac35;',
        '⅘' => '&frac45;',
        '⅙' => '&frac16;',
        '⅚' => '&frac56;',
        '⅛' => '&frac18;',
        '⅜' => '&frac38;',
        '⅝' => '&frac58;',
        '⅞' => '&frac78;',
        '←' => '&larr;',
        '↑' => '&uarr;',
        '→' => '&srarr;',
        '↓' => '&darr;',
        '↔' => '&harr;',
        '↕' => '&UpDownArrow;',
        '↖' => '&nwarrow;',
        '↗' => '&UpperRightArrow;',
        '↘' => '&LowerRightArrow;',
        '↙' => '&swarr;',
        '↚' => '&nleftarrow;',
        '↛' => '&nrarr;',
        '↝' => '&rarrw;',
        '↝̸' => '&nrarrw;',
        '↞' => '&Larr;',
        '↟' => '&Uarr;',
        '↠' => '&twoheadrightarrow;',
        '↡' => '&Darr;',
        '↢' => '&larrtl;',
        '↣' => '&rarrtl;',
        '↤' => '&LeftTeeArrow;',
        '↥' => '&UpTeeArrow;',
        '↦' => '&map;',
        '↧' => '&DownTeeArrow;',
        '↩' => '&larrhk;',
        '↪' => '&rarrhk;',
        '↫' => '&larrlp;',
        '↬' => '&looparrowright;',
        '↭' => '&harrw;',
        '↮' => '&nleftrightarrow;',
        '↰' => '&Lsh;',
        '↱' => '&rsh;',
        '↲' => '&ldsh;',
        '↳' => '&rdsh;',
        '↵' => '&crarr;',
        '↶' => '&curvearrowleft;',
        '↷' => '&curarr;',
        '↺' => '&olarr;',
        '↻' => '&orarr;',
        '↼' => '&leftharpoonup;',
        '↽' => '&leftharpoondown;',
        '↾' => '&RightUpVector;',
        '↿' => '&uharl;',
        '⇀' => '&rharu;',
        '⇁' => '&rhard;',
        '⇂' => '&RightDownVector;',
        '⇃' => '&dharl;',
        '⇄' => '&rightleftarrows;',
        '⇅' => '&udarr;',
        '⇆' => '&lrarr;',
        '⇇' => '&llarr;',
        '⇈' => '&upuparrows;',
        '⇉' => '&rrarr;',
        '⇊' => '&downdownarrows;',
        '⇋' => '&leftrightharpoons;',
        '⇌' => '&rightleftharpoons;',
        '⇍' => '&nLeftarrow;',
        '⇎' => '&nhArr;',
        '⇏' => '&nrArr;',
        '⇐' => '&DoubleLeftArrow;',
        '⇑' => '&DoubleUpArrow;',
        '⇒' => '&Implies;',
        '⇓' => '&Downarrow;',
        '⇔' => '&hArr;',
        '⇕' => '&Updownarrow;',
        '⇖' => '&nwArr;',
        '⇗' => '&neArr;',
        '⇘' => '&seArr;',
        '⇙' => '&swArr;',
        '⇚' => '&lAarr;',
        '⇛' => '&rAarr;',
        '⇝' => '&zigrarr;',
        '⇤' => '&LeftArrowBar;',
        '⇥' => '&RightArrowBar;',
        '⇵' => '&DownArrowUpArrow;',
        '⇽' => '&loarr;',
        '⇾' => '&roarr;',
        '⇿' => '&hoarr;',
        '∀' => '&forall;',
        '∁' => '&comp;',
        '∂' => '&part;',
        '∂̸' => '&npart;',
        '∃' => '&Exists;',
        '∄' => '&nexist;',
        '∅' => '&empty;',
        '∇' => '&nabla;',
        '∈' => '&isinv;',
        '∉' => '&notin;',
        '∋' => '&ReverseElement;',
        '∌' => '&notniva;',
        '∏' => '&prod;',
        '∐' => '&Coproduct;',
        '∑' => '&sum;',
        '−' => '&minus;',
        '∓' => '&MinusPlus;',
        '∔' => '&plusdo;',
        '∖' => '&ssetmn;',
        '∗' => '&lowast;',
        '∘' => '&compfn;',
        '√' => '&Sqrt;',
        '∝' => '&prop;',
        '∞' => '&infin;',
        '∟' => '&angrt;',
        '∠' => '&angle;',
        '∠⃒' => '&nang;',
        '∡' => '&angmsd;',
        '∢' => '&angsph;',
        '∣' => '&mid;',
        '∤' => '&nshortmid;',
        '∥' => '&shortparallel;',
        '∦' => '&nparallel;',
        '∧' => '&and;',
        '∨' => '&or;',
        '∩' => '&cap;',
        '∩︀' => '&caps;',
        '∪' => '&cup;',
        '∪︀' => '&cups',
        '∫' => '&Integral;',
        '∬' => '&Int;',
        '∭' => '&tint;',
        '∮' => '&ContourIntegral;',
        '∯' => '&DoubleContourIntegral;',
        '∰' => '&Cconint;',
        '∱' => '&cwint;',
        '∲' => '&cwconint;',
        '∳' => '&awconint;',
        '∴' => '&there4;',
        '∵' => '&Because;',
        '∶' => '&ratio;',
        '∷' => '&Colon;',
        '∸' => '&minusd;',
        '∺' => '&mDDot;',
        '∻' => '&homtht;',
        '∼' => '&sim;',
        '∼⃒' => '&nvsim;',
        '∽' => '&bsim;',
        '∽̱' => '&race;',
        '∾' => '&ac;',
        '∾̳' => '&acE;',
        '∿' => '&acd;',
        '≀' => '&wr;',
        '≁' => '&NotTilde;',
        '≂' => '&esim;',
        '≂̸' => '&nesim;',
        '≃' => '&simeq;',
        '≄' => '&nsime;',
        '≅' => '&TildeFullEqual;',
        '≆' => '&simne;',
        '≇' => '&ncong;',
        '≈' => '&approx;',
        '≉' => '&napprox;',
        '≊' => '&ape;',
        '≋' => '&apid;',
        '≋̸' => '&napid;',
        '≌' => '&bcong;',
        '≍' => '&CupCap;',
        '≍⃒' => '&nvap;',
        '≎' => '&bump;',
        '≎̸' => '&nbump;',
        '≏' => '&HumpEqual;',
        '≏̸' => '&nbumpe;',
        '≐' => '&esdot;',
        '≐̸' => '&nedot;',
        '≑' => '&doteqdot;',
        '≒' => '&fallingdotseq;',
        '≓' => '&risingdotseq;',
        '≔' => '&coloneq;',
        '≕' => '&eqcolon;',
        '≖' => '&ecir;',
        '≗' => '&circeq;',
        '≙' => '&wedgeq;',
        '≚' => '&veeeq;',
        '≜' => '&triangleq;',
        '≟' => '&equest;',
        '≠' => '&NotEqual;',
        '≡' => '&Congruent;',
        '≡⃥' => '&bnequiv;',
        '≢' => '&NotCongruent;',
        '≤' => '&leq;',
        '≤⃒' => '&nvle;',
        '≥' => '&ge;',
        '≥⃒' => '&nvge;',
        '≦' => '&lE;',
        '≦̸' => '&nlE;',
        '≧' => '&geqq;',
        '≧̸' => '&NotGreaterFullEqual;',
        '≨' => '&lneqq;',
        '≨︀' => '&lvertneqq;',
        '≩' => '&gneqq;',
        '≩︀' => '&gvertneqq;',
        '≪' => '&ll;',
        '≪̸' => '&nLtv;',
        '≪⃒' => '&nLt;',
        '≫' => '&gg;',
        '≫̸' => '&NotGreaterGreater;',
        '≫⃒' => '&nGt;',
        '≬' => '&between;',
        '≭' => '&NotCupCap;',
        '≮' => '&NotLess;',
        '≯' => '&ngtr;',
        '≰' => '&NotLessEqual;',
        '≱' => '&ngeq;',
        '≲' => '&LessTilde;',
        '≳' => '&GreaterTilde;',
        '≴' => '&nlsim;',
        '≵' => '&ngsim;',
        '≶' => '&lessgtr;',
        '≷' => '&gl;',
        '≸' => '&ntlg;',
        '≹' => '&NotGreaterLess;',
        '≺' => '&prec;',
        '≻' => '&succ;',
        '≼' => '&PrecedesSlantEqual;',
        '≽' => '&succcurlyeq;',
        '≾' => '&precsim;',
        '≿' => '&SucceedsTilde;',
        '≿̸' => '&NotSucceedsTilde;',
        '⊀' => '&npr;',
        '⊁' => '&NotSucceeds;',
        '⊂' => '&sub;',
        '⊂⃒' => '&vnsub;',
        '⊃' => '&sup;',
        '⊃⃒' => '&nsupset;',
        '⊄' => '&nsub;',
        '⊅' => '&nsup;',
        '⊆' => '&SubsetEqual;',
        '⊇' => '&supe;',
        '⊈' => '&NotSubsetEqual;',
        '⊉' => '&NotSupersetEqual;',
        '⊊' => '&subsetneq;',
        '⊊︀' => '&vsubne;',
        '⊋' => '&supsetneq;',
        '⊋︀' => '&vsupne;',
        '⊍' => '&cupdot;',
        '⊎' => '&UnionPlus;',
        '⊏' => '&sqsub;',
        '⊏̸' => '&NotSquareSubset;',
        '⊐' => '&sqsupset;',
        '⊐̸' => '&NotSquareSuperset;',
        '⊑' => '&SquareSubsetEqual;',
        '⊒' => '&SquareSupersetEqual;',
        '⊓' => '&sqcap;',
        '⊓︀' => '&sqcaps;',
        '⊔' => '&sqcup;',
        '⊔︀' => '&sqcups;',
        '⊕' => '&CirclePlus;',
        '⊖' => '&ominus;',
        '⊗' => '&CircleTimes;',
        '⊘' => '&osol;',
        '⊙' => '&CircleDot;',
        '⊚' => '&ocir;',
        '⊛' => '&oast;',
        '⊝' => '&odash;',
        '⊞' => '&boxplus;',
        '⊟' => '&boxminus;',
        '⊠' => '&timesb;',
        '⊡' => '&sdotb;',
        '⊢' => '&vdash;',
        '⊣' => '&dashv;',
        '⊤' => '&DownTee;',
        '⊥' => '&perp;',
        '⊧' => '&models;',
        '⊨' => '&DoubleRightTee;',
        '⊩' => '&Vdash;',
        '⊪' => '&Vvdash;',
        '⊫' => '&VDash;',
        '⊬' => '&nvdash;',
        '⊭' => '&nvDash;',
        '⊮' => '&nVdash;',
        '⊯' => '&nVDash;',
        '⊰' => '&prurel;',
        '⊲' => '&vartriangleleft;',
        '⊳' => '&vrtri;',
        '⊴' => '&LeftTriangleEqual;',
        '⊴⃒' => '&nvltrie;',
        '⊵' => '&RightTriangleEqual;',
        '⊵⃒' => '&nvrtrie;',
        '⊶' => '&origof;',
        '⊷' => '&imof;',
        '⊸' => '&mumap;',
        '⊹' => '&hercon;',
        '⊺' => '&intcal;',
        '⊻' => '&veebar;',
        '⊽' => '&barvee;',
        '⊾' => '&angrtvb;',
        '⊿' => '&lrtri;',
        '⋀' => '&xwedge;',
        '⋁' => '&xvee;',
        '⋂' => '&bigcap;',
        '⋃' => '&bigcup;',
        '⋄' => '&diamond;',
        '⋅' => '&sdot;',
        '⋆' => '&Star;',
        '⋇' => '&divonx;',
        '⋈' => '&bowtie;',
        '⋉' => '&ltimes;',
        '⋊' => '&rtimes;',
        '⋋' => '&lthree;',
        '⋌' => '&rthree;',
        '⋍' => '&backsimeq;',
        '⋎' => '&curlyvee;',
        '⋏' => '&curlywedge;',
        '⋐' => '&Sub;',
        '⋑' => '&Supset;',
        '⋒' => '&Cap;',
        '⋓' => '&Cup;',
        '⋔' => '&pitchfork;',
        '⋕' => '&epar;',
        '⋖' => '&lessdot;',
        '⋗' => '&gtrdot;',
        '⋘' => '&Ll;',
        '⋘̸' => '&nLl;',
        '⋙' => '&Gg;',
        '⋙̸' => '&nGg;',
        '⋚' => '&lesseqgtr;',
        '⋚︀' => '&lesg;',
        '⋛' => '&gtreqless;',
        '⋛︀' => '&gesl;',
        '⋞' => '&curlyeqprec;',
        '⋟' => '&cuesc;',
        '⋠' => '&NotPrecedesSlantEqual;',
        '⋡' => '&NotSucceedsSlantEqual;',
        '⋢' => '&NotSquareSubsetEqual;',
        '⋣' => '&NotSquareSupersetEqual;',
        '⋦' => '&lnsim;',
        '⋧' => '&gnsim;',
        '⋨' => '&precnsim;',
        '⋩' => '&scnsim;',
        '⋪' => '&nltri;',
        '⋫' => '&ntriangleright;',
        '⋬' => '&nltrie;',
        '⋭' => '&NotRightTriangleEqual;',
        '⋮' => '&vellip;',
        '⋯' => '&ctdot;',
        '⋰' => '&utdot;',
        '⋱' => '&dtdot;',
        '⋲' => '&disin;',
        '⋳' => '&isinsv;',
        '⋴' => '&isins;',
        '⋵' => '&isindot;',
        '⋵̸' => '&notindot;',
        '⋶' => '&notinvc;',
        '⋷' => '&notinvb;',
        '⋹' => '&isinE;',
        '⋹̸' => '&notinE;',
        '⋺' => '&nisd;',
        '⋻' => '&xnis;',
        '⋼' => '&nis;',
        '⋽' => '&notnivc;',
        '⋾' => '&notnivb;',
        '⌅' => '&barwed;',
        '⌆' => '&doublebarwedge;',
        '⌈' => '&lceil;',
        '⌉' => '&RightCeiling;',
        '⌊' => '&LeftFloor;',
        '⌋' => '&RightFloor;',
        '⌌' => '&drcrop;',
        '⌍' => '&dlcrop;',
        '⌎' => '&urcrop;',
        '⌏' => '&ulcrop;',
        '⌐' => '&bnot;',
        '⌒' => '&profline;',
        '⌓' => '&profsurf;',
        '⌕' => '&telrec;',
        '⌖' => '&target;',
        '⌜' => '&ulcorner;',
        '⌝' => '&urcorner;',
        '⌞' => '&llcorner;',
        '⌟' => '&drcorn;',
        '⌢' => '&frown;',
        '⌣' => '&smile;',
        '⌭' => '&cylcty;',
        '⌮' => '&profalar;',
        '⌶' => '&topbot;',
        '⌽' => '&ovbar;',
        '⌿' => '&solbar;',
        '⍼' => '&angzarr;',
        '⎰' => '&lmoust;',
        '⎱' => '&rmoust;',
        '⎴' => '&OverBracket;',
        '⎵' => '&bbrk;',
        '⎶' => '&bbrktbrk;',
        '⏜' => '&OverParenthesis;',
        '⏝' => '&UnderParenthesis;',
        '⏞' => '&OverBrace;',
        '⏟' => '&UnderBrace;',
        '⏢' => '&trpezium;',
        '⏧' => '&elinters;',
        '␣' => '&blank;',
        'Ⓢ' => '&oS;',
        '─' => '&HorizontalLine;',
        '│' => '&boxv;',
        '┌' => '&boxdr;',
        '┐' => '&boxdl;',
        '└' => '&boxur;',
        '┘' => '&boxul;',
        '├' => '&boxvr;',
        '┤' => '&boxvl;',
        '┬' => '&boxhd;',
        '┴' => '&boxhu;',
        '┼' => '&boxvh;',
        '═' => '&boxH;',
        '║' => '&boxV;',
        '╒' => '&boxdR;',
        '╓' => '&boxDr;',
        '╔' => '&boxDR;',
        '╕' => '&boxdL;',
        '╖' => '&boxDl;',
        '╗' => '&boxDL;',
        '╘' => '&boxuR;',
        '╙' => '&boxUr;',
        '╚' => '&boxUR;',
        '╛' => '&boxuL;',
        '╜' => '&boxUl;',
        '╝' => '&boxUL;',
        '╞' => '&boxvR;',
        '╟' => '&boxVr;',
        '╠' => '&boxVR;',
        '╡' => '&boxvL;',
        '╢' => '&boxVl;',
        '╣' => '&boxVL;',
        '╤' => '&boxHd;',
        '╥' => '&boxhD;',
        '╦' => '&boxHD;',
        '╧' => '&boxHu;',
        '╨' => '&boxhU;',
        '╩' => '&boxHU;',
        '╪' => '&boxvH;',
        '╫' => '&boxVh;',
        '╬' => '&boxVH;',
        '▀' => '&uhblk;',
        '▄' => '&lhblk;',
        '█' => '&block;',
        '░' => '&blk14;',
        '▒' => '&blk12;',
        '▓' => '&blk34;',
        '□' => '&Square;',
        '▪' => '&squarf;',
        '▫' => '&EmptyVerySmallSquare;',
        '▭' => '&rect;',
        '▮' => '&marker;',
        '▱' => '&fltns;',
        '△' => '&bigtriangleup;',
        '▴' => '&blacktriangle;',
        '▵' => '&triangle;',
        '▸' => '&blacktriangleright;',
        '▹' => '&rtri;',
        '▽' => '&bigtriangledown;',
        '▾' => '&blacktriangledown;',
        '▿' => '&triangledown;',
        '◂' => '&blacktriangleleft;',
        '◃' => '&ltri;',
        '◊' => '&lozenge;',
        '○' => '&cir;',
        '◬' => '&tridot;',
        '◯' => '&bigcirc;',
        '◸' => '&ultri;',
        '◹' => '&urtri;',
        '◺' => '&lltri;',
        '◻' => '&EmptySmallSquare;',
        '◼' => '&FilledSmallSquare;',
        '★' => '&starf;',
        '☆' => '&star;',
        '☎' => '&phone;',
        '♀' => '&female;',
        '♂' => '&male;',
        '♠' => '&spadesuit;',
        '♣' => '&clubs;',
        '♥' => '&hearts;',
        '♦' => '&diamondsuit;',
        '♪' => '&sung;',
        '♭' => '&flat;',
        '♮' => '&natur;',
        '♯' => '&sharp;',
        '✓' => '&check;',
        '✗' => '&cross;',
        '✠' => '&maltese;',
        '✶' => '&sext;',
        '❘' => '&VerticalSeparator;',
        '❲' => '&lbbrk;',
        '❳' => '&rbbrk;',
        '⟈' => '&bsolhsub;',
        '⟉' => '&suphsol;',
        '⟦' => '&LeftDoubleBracket;',
        '⟧' => '&RightDoubleBracket;',
        '⟨' => '&langle;',
        '⟩' => '&RightAngleBracket;',
        '⟪' => '&Lang;',
        '⟫' => '&Rang;',
        '⟬' => '&loang;',
        '⟭' => '&roang;',
        '⟵' => '&longleftarrow;',
        '⟶' => '&LongRightArrow;',
        '⟷' => '&LongLeftRightArrow;',
        '⟸' => '&xlArr;',
        '⟹' => '&DoubleLongRightArrow;',
        '⟺' => '&xhArr;',
        '⟼' => '&xmap;',
        '⟿' => '&dzigrarr;',
        '⤂' => '&nvlArr;',
        '⤃' => '&nvrArr;',
        '⤄' => '&nvHarr;',
        '⤅' => '&Map;',
        '⤌' => '&lbarr;',
        '⤍' => '&bkarow;',
        '⤎' => '&lBarr;',
        '⤏' => '&dbkarow;',
        '⤐' => '&drbkarow;',
        '⤑' => '&DDotrahd;',
        '⤒' => '&UpArrowBar;',
        '⤓' => '&DownArrowBar;',
        '⤖' => '&Rarrtl;',
        '⤙' => '&latail;',
        '⤚' => '&ratail;',
        '⤛' => '&lAtail;',
        '⤜' => '&rAtail;',
        '⤝' => '&larrfs;',
        '⤞' => '&rarrfs;',
        '⤟' => '&larrbfs;',
        '⤠' => '&rarrbfs;',
        '⤣' => '&nwarhk;',
        '⤤' => '&nearhk;',
        '⤥' => '&searhk;',
        '⤦' => '&swarhk;',
        '⤧' => '&nwnear;',
        '⤨' => '&toea;',
        '⤩' => '&seswar;',
        '⤪' => '&swnwar;',
        '⤳' => '&rarrc;',
        '⤳̸' => '&nrarrc;',
        '⤵' => '&cudarrr;',
        '⤶' => '&ldca;',
        '⤷' => '&rdca;',
        '⤸' => '&cudarrl;',
        '⤹' => '&larrpl;',
        '⤼' => '&curarrm;',
        '⤽' => '&cularrp;',
        '⥅' => '&rarrpl;',
        '⥈' => '&harrcir;',
        '⥉' => '&Uarrocir;',
        '⥊' => '&lurdshar;',
        '⥋' => '&ldrushar;',
        '⥎' => '&LeftRightVector;',
        '⥏' => '&RightUpDownVector;',
        '⥐' => '&DownLeftRightVector;',
        '⥑' => '&LeftUpDownVector;',
        '⥒' => '&LeftVectorBar;',
        '⥓' => '&RightVectorBar;',
        '⥔' => '&RightUpVectorBar;',
        '⥕' => '&RightDownVectorBar;',
        '⥖' => '&DownLeftVectorBar;',
        '⥗' => '&DownRightVectorBar;',
        '⥘' => '&LeftUpVectorBar;',
        '⥙' => '&LeftDownVectorBar;',
        '⥚' => '&LeftTeeVector;',
        '⥛' => '&RightTeeVector;',
        '⥜' => '&RightUpTeeVector;',
        '⥝' => '&RightDownTeeVector;',
        '⥞' => '&DownLeftTeeVector;',
        '⥟' => '&DownRightTeeVector;',
        '⥠' => '&LeftUpTeeVector;',
        '⥡' => '&LeftDownTeeVector;',
        '⥢' => '&lHar;',
        '⥣' => '&uHar;',
        '⥤' => '&rHar;',
        '⥥' => '&dHar;',
        '⥦' => '&luruhar;',
        '⥧' => '&ldrdhar;',
        '⥨' => '&ruluhar;',
        '⥩' => '&rdldhar;',
        '⥪' => '&lharul;',
        '⥫' => '&llhard;',
        '⥬' => '&rharul;',
        '⥭' => '&lrhard;',
        '⥮' => '&udhar;',
        '⥯' => '&ReverseUpEquilibrium;',
        '⥰' => '&RoundImplies;',
        '⥱' => '&erarr;',
        '⥲' => '&simrarr;',
        '⥳' => '&larrsim;',
        '⥴' => '&rarrsim;',
        '⥵' => '&rarrap;',
        '⥶' => '&ltlarr;',
        '⥸' => '&gtrarr;',
        '⥹' => '&subrarr;',
        '⥻' => '&suplarr;',
        '⥼' => '&lfisht;',
        '⥽' => '&rfisht;',
        '⥾' => '&ufisht;',
        '⥿' => '&dfisht;',
        '⦅' => '&lopar;',
        '⦆' => '&ropar;',
        '⦋' => '&lbrke;',
        '⦌' => '&rbrke;',
        '⦍' => '&lbrkslu;',
        '⦎' => '&rbrksld;',
        '⦏' => '&lbrksld;',
        '⦐' => '&rbrkslu;',
        '⦑' => '&langd;',
        '⦒' => '&rangd;',
        '⦓' => '&lparlt;',
        '⦔' => '&rpargt;',
        '⦕' => '&gtlPar;',
        '⦖' => '&ltrPar;',
        '⦚' => '&vzigzag;',
        '⦜' => '&vangrt;',
        '⦝' => '&angrtvbd;',
        '⦤' => '&ange;',
        '⦥' => '&range;',
        '⦦' => '&dwangle;',
        '⦧' => '&uwangle;',
        '⦨' => '&angmsdaa;',
        '⦩' => '&angmsdab;',
        '⦪' => '&angmsdac;',
        '⦫' => '&angmsdad;',
        '⦬' => '&angmsdae;',
        '⦭' => '&angmsdaf;',
        '⦮' => '&angmsdag;',
        '⦯' => '&angmsdah;',
        '⦰' => '&bemptyv;',
        '⦱' => '&demptyv;',
        '⦲' => '&cemptyv;',
        '⦳' => '&raemptyv;',
        '⦴' => '&laemptyv;',
        '⦵' => '&ohbar;',
        '⦶' => '&omid;',
        '⦷' => '&opar;',
        '⦹' => '&operp;',
        '⦻' => '&olcross;',
        '⦼' => '&odsold;',
        '⦾' => '&olcir;',
        '⦿' => '&ofcir;',
        '⧀' => '&olt;',
        '⧁' => '&ogt;',
        '⧂' => '&cirscir;',
        '⧃' => '&cirE;',
        '⧄' => '&solb;',
        '⧅' => '&bsolb;',
        '⧉' => '&boxbox;',
        '⧍' => '&trisb;',
        '⧎' => '&rtriltri;',
        '⧏' => '&LeftTriangleBar;',
        '⧏̸' => '&NotLeftTriangleBar;',
        '⧐' => '&RightTriangleBar;',
        '⧐̸' => '&NotRightTriangleBar;',
        '⧜' => '&iinfin;',
        '⧝' => '&infintie;',
        '⧞' => '&nvinfin;',
        '⧣' => '&eparsl;',
        '⧤' => '&smeparsl;',
        '⧥' => '&eqvparsl;',
        '⧫' => '&lozf;',
        '⧴' => '&RuleDelayed;',
        '⧶' => '&dsol;',
        '⨀' => '&xodot;',
        '⨁' => '&bigoplus;',
        '⨂' => '&bigotimes;',
        '⨄' => '&biguplus;',
        '⨆' => '&bigsqcup;',
        '⨌' => '&iiiint;',
        '⨍' => '&fpartint;',
        '⨐' => '&cirfnint;',
        '⨑' => '&awint;',
        '⨒' => '&rppolint;',
        '⨓' => '&scpolint;',
        '⨔' => '&npolint;',
        '⨕' => '&pointint;',
        '⨖' => '&quatint;',
        '⨗' => '&intlarhk;',
        '⨢' => '&pluscir;',
        '⨣' => '&plusacir;',
        '⨤' => '&simplus;',
        '⨥' => '&plusdu;',
        '⨦' => '&plussim;',
        '⨧' => '&plustwo;',
        '⨩' => '&mcomma;',
        '⨪' => '&minusdu;',
        '⨭' => '&loplus;',
        '⨮' => '&roplus;',
        '⨯' => '&Cross;',
        '⨰' => '&timesd;',
        '⨱' => '&timesbar;',
        '⨳' => '&smashp;',
        '⨴' => '&lotimes;',
        '⨵' => '&rotimes;',
        '⨶' => '&otimesas;',
        '⨷' => '&Otimes;',
        '⨸' => '&odiv;',
        '⨹' => '&triplus;',
        '⨺' => '&triminus;',
        '⨻' => '&tritime;',
        '⨼' => '&iprod;',
        '⨿' => '&amalg;',
        '⩀' => '&capdot;',
        '⩂' => '&ncup;',
        '⩃' => '&ncap;',
        '⩄' => '&capand;',
        '⩅' => '&cupor;',
        '⩆' => '&cupcap;',
        '⩇' => '&capcup;',
        '⩈' => '&cupbrcap;',
        '⩉' => '&capbrcup;',
        '⩊' => '&cupcup;',
        '⩋' => '&capcap;',
        '⩌' => '&ccups;',
        '⩍' => '&ccaps;',
        '⩐' => '&ccupssm;',
        '⩓' => '&And;',
        '⩔' => '&Or;',
        '⩕' => '&andand;',
        '⩖' => '&oror;',
        '⩗' => '&orslope;',
        '⩘' => '&andslope;',
        '⩚' => '&andv;',
        '⩛' => '&orv;',
        '⩜' => '&andd;',
        '⩝' => '&ord;',
        '⩟' => '&wedbar;',
        '⩦' => '&sdote;',
        '⩪' => '&simdot;',
        '⩭' => '&congdot;',
        '⩭̸' => '&ncongdot;',
        '⩮' => '&easter;',
        '⩯' => '&apacir;',
        '⩰' => '&apE;',
        '⩰̸' => '&napE;',
        '⩱' => '&eplus;',
        '⩲' => '&pluse;',
        '⩳' => '&Esim;',
        '⩴' => '&Colone;',
        '⩵' => '&Equal;',
        '⩷' => '&ddotseq;',
        '⩸' => '&equivDD;',
        '⩹' => '&ltcir;',
        '⩺' => '&gtcir;',
        '⩻' => '&ltquest;',
        '⩼' => '&gtquest;',
        '⩽' => '&les;',
        '⩽̸' => '&nles;',
        '⩾' => '&ges;',
        '⩾̸' => '&nges;',
        '⩿' => '&lesdot;',
        '⪀' => '&gesdot;',
        '⪁' => '&lesdoto;',
        '⪂' => '&gesdoto;',
        '⪃' => '&lesdotor;',
        '⪄' => '&gesdotol;',
        '⪅' => '&lap;',
        '⪆' => '&gap;',
        '⪇' => '&lne;',
        '⪈' => '&gne;',
        '⪉' => '&lnap;',
        '⪊' => '&gnap;',
        '⪋' => '&lesseqqgtr;',
        '⪌' => '&gEl;',
        '⪍' => '&lsime;',
        '⪎' => '&gsime;',
        '⪏' => '&lsimg;',
        '⪐' => '&gsiml;',
        '⪑' => '&lgE;',
        '⪒' => '&glE;',
        '⪓' => '&lesges;',
        '⪔' => '&gesles;',
        '⪕' => '&els;',
        '⪖' => '&egs;',
        '⪗' => '&elsdot;',
        '⪘' => '&egsdot;',
        '⪙' => '&el;',
        '⪚' => '&eg;',
        '⪝' => '&siml;',
        '⪞' => '&simg;',
        '⪟' => '&simlE;',
        '⪠' => '&simgE;',
        '⪡' => '&LessLess;',
        '⪡̸' => '&NotNestedLessLess;',
        '⪢' => '&GreaterGreater;',
        '⪢̸' => '&NotNestedGreaterGreater;',
        '⪤' => '&glj;',
        '⪥' => '&gla;',
        '⪦' => '&ltcc;',
        '⪧' => '&gtcc;',
        '⪨' => '&lescc;',
        '⪩' => '&gescc;',
        '⪪' => '&smt;',
        '⪫' => '&lat;',
        '⪬' => '&smte;',
        '⪬︀' => '&smtes;',
        '⪭' => '&late;',
        '⪭︀' => '&lates;',
        '⪮' => '&bumpE;',
        '⪯' => '&preceq;',
        '⪯̸' => '&NotPrecedesEqual;',
        '⪰' => '&SucceedsEqual;',
        '⪰̸' => '&NotSucceedsEqual;',
        '⪳' => '&prE;',
        '⪴' => '&scE;',
        '⪵' => '&precneqq;',
        '⪶' => '&scnE;',
        '⪷' => '&precapprox;',
        '⪸' => '&succapprox;',
        '⪹' => '&precnapprox;',
        '⪺' => '&succnapprox;',
        '⪻' => '&Pr;',
        '⪼' => '&Sc;',
        '⪽' => '&subdot;',
        '⪾' => '&supdot;',
        '⪿' => '&subplus;',
        '⫀' => '&supplus;',
        '⫁' => '&submult;',
        '⫂' => '&supmult;',
        '⫃' => '&subedot;',
        '⫄' => '&supedot;',
        '⫅' => '&subE;',
        '⫅̸' => '&nsubE;',
        '⫆' => '&supseteqq;',
        '⫆̸' => '&nsupseteqq;',
        '⫇' => '&subsim;',
        '⫈' => '&supsim;',
        '⫋' => '&subsetneqq;',
        '⫋︀' => '&vsubnE;',
        '⫌' => '&supnE;',
        '⫌︀' => '&varsupsetneqq;',
        '⫏' => '&csub;',
        '⫐' => '&csup;',
        '⫑' => '&csube;',
        '⫒' => '&csupe;',
        '⫓' => '&subsup;',
        '⫔' => '&supsub;',
        '⫕' => '&subsub;',
        '⫖' => '&supsup;',
        '⫗' => '&suphsub;',
        '⫘' => '&supdsub;',
        '⫙' => '&forkv;',
        '⫚' => '&topfork;',
        '⫛' => '&mlcp;',
        '⫤' => '&Dashv;',
        '⫦' => '&Vdashl;',
        '⫧' => '&Barv;',
        '⫨' => '&vBar;',
        '⫩' => '&vBarv;',
        '⫫' => '&Vbar;',
        '⫬' => '&Not;',
        '⫭' => '&bNot;',
        '⫮' => '&rnmid;',
        '⫯' => '&cirmid;',
        '⫰' => '&midcir;',
        '⫱' => '&topcir;',
        '⫲' => '&nhpar;',
        '⫳' => '&parsim;',
        '⫽' => '&parsl;',
        '⫽⃥' => '&nparsl;',
        'ﬀ' => '&fflig;',
        'ﬁ' => '&filig;',
        'ﬂ' => '&fllig;',
        'ﬃ' => '&ffilig;',
        'ﬄ' => '&ffllig;',
        '𝒜' => '&Ascr;',
        '𝒞' => '&Cscr;',
        '𝒟' => '&Dscr;',
        '𝒢' => '&Gscr;',
        '𝒥' => '&Jscr;',
        '𝒦' => '&Kscr;',
        '𝒩' => '&Nscr;',
        '𝒪' => '&Oscr;',
        '𝒫' => '&Pscr;',
        '𝒬' => '&Qscr;',
        '𝒮' => '&Sscr;',
        '𝒯' => '&Tscr;',
        '𝒰' => '&Uscr;',
        '𝒱' => '&Vscr;',
        '𝒲' => '&Wscr;',
        '𝒳' => '&Xscr;',
        '𝒴' => '&Yscr;',
        '𝒵' => '&Zscr;',
        '𝒶' => '&ascr;',
        '𝒷' => '&bscr;',
        '𝒸' => '&cscr;',
        '𝒹' => '&dscr;',
        '𝒻' => '&fscr;',
        '𝒽' => '&hscr;',
        '𝒾' => '&iscr;',
        '𝒿' => '&jscr;',
        '𝓀' => '&kscr;',
        '𝓁' => '&lscr;',
        '𝓂' => '&mscr;',
        '𝓃' => '&nscr;',
        '𝓅' => '&pscr;',
        '𝓆' => '&qscr;',
        '𝓇' => '&rscr;',
        '𝓈' => '&sscr;',
        '𝓉' => '&tscr;',
        '𝓊' => '&uscr;',
        '𝓋' => '&vscr;',
        '𝓌' => '&wscr;',
        '𝓍' => '&xscr;',
        '𝓎' => '&yscr;',
        '𝓏' => '&zscr;',
        '𝔄' => '&Afr;',
        '𝔅' => '&Bfr;',
        '𝔇' => '&Dfr;',
        '𝔈' => '&Efr;',
        '𝔉' => '&Ffr;',
        '𝔊' => '&Gfr;',
        '𝔍' => '&Jfr;',
        '𝔎' => '&Kfr;',
        '𝔏' => '&Lfr;',
        '𝔐' => '&Mfr;',
        '𝔑' => '&Nfr;',
        '𝔒' => '&Ofr;',
        '𝔓' => '&Pfr;',
        '𝔔' => '&Qfr;',
        '𝔖' => '&Sfr;',
        '𝔗' => '&Tfr;',
        '𝔘' => '&Ufr;',
        '𝔙' => '&Vfr;',
        '𝔚' => '&Wfr;',
        '𝔛' => '&Xfr;',
        '𝔜' => '&Yfr;',
        '𝔞' => '&afr;',
        '𝔟' => '&bfr;',
        '𝔠' => '&cfr;',
        '𝔡' => '&dfr;',
        '𝔢' => '&efr;',
        '𝔣' => '&ffr;',
        '𝔤' => '&gfr;',
        '𝔥' => '&hfr;',
        '𝔦' => '&ifr;',
        '𝔧' => '&jfr;',
        '𝔨' => '&kfr;',
        '𝔩' => '&lfr;',
        '𝔪' => '&mfr;',
        '𝔫' => '&nfr;',
        '𝔬' => '&ofr;',
        '𝔭' => '&pfr;',
        '𝔮' => '&qfr;',
        '𝔯' => '&rfr;',
        '𝔰' => '&sfr;',
        '𝔱' => '&tfr;',
        '𝔲' => '&ufr;',
        '𝔳' => '&vfr;',
        '𝔴' => '&wfr;',
        '𝔵' => '&xfr;',
        '𝔶' => '&yfr;',
        '𝔷' => '&zfr;',
        '𝔸' => '&Aopf;',
        '𝔹' => '&Bopf;',
        '𝔻' => '&Dopf;',
        '𝔼' => '&Eopf;',
        '𝔽' => '&Fopf;',
        '𝔾' => '&Gopf;',
        '𝕀' => '&Iopf;',
        '𝕁' => '&Jopf;',
        '𝕂' => '&Kopf;',
        '𝕃' => '&Lopf;',
        '𝕄' => '&Mopf;',
        '𝕆' => '&Oopf;',
        '𝕊' => '&Sopf;',
        '𝕋' => '&Topf;',
        '𝕌' => '&Uopf;',
        '𝕍' => '&Vopf;',
        '𝕎' => '&Wopf;',
        '𝕏' => '&Xopf;',
        '𝕐' => '&Yopf;',
        '𝕒' => '&aopf;',
        '𝕓' => '&bopf;',
        '𝕔' => '&copf;',
        '𝕕' => '&dopf;',
        '𝕖' => '&eopf;',
        '𝕗' => '&fopf;',
        '𝕘' => '&gopf;',
        '𝕙' => '&hopf;',
        '𝕚' => '&iopf;',
        '𝕛' => '&jopf;',
        '𝕜' => '&kopf;',
        '𝕝' => '&lopf;',
        '𝕞' => '&mopf;',
        '𝕟' => '&nopf;',
        '𝕠' => '&oopf;',
        '𝕡' => '&popf;',
        '𝕢' => '&qopf;',
        '𝕣' => '&ropf;',
        '𝕤' => '&sopf;',
        '𝕥' => '&topf;',
        '𝕦' => '&uopf;',
        '𝕧' => '&vopf;',
        '𝕨' => '&wopf;',
        '𝕩' => '&xopf;',
        '𝕪' => '&yopf;',
        '𝕫' => '&zopf;',
    );

    /**
     * List of never allowed regex replacements
     *
     * @var  array
     */
    protected static $_never_allowed_regex = array(
        // default javascript
        'javascript\s*:',
        // default javascript
        '(document|(document\.)?window)\.(location|on\w*)',
        // Java: jar-protocol is an XSS hazard
        'jar\s*:',
        // Mac (will not run the script, but open it in AppleScript Editor)
        'applescript\s*:',
        // IE: https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet#VBscript_in_an_image
        'vbscript\s*:',
        // IE, surprise!
        'wscript\s*:',
        // IE
        'jscript\s*:',
        // IE: https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet#VBscript_in_an_image
        'vbs\s*:',
        // https://html5sec.org/#behavior
        'behavior\s:',
        // ?
        'Redirect\s+30\d',
        // data-attribute + base64
        "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?",
        // remove Netscape 4 JS entities
        '&\s*\{[^}]*(\}\s*;?|$)',
        // old IE, old Netscape
        'expression\s*(\(|&\#40;)',
        // old Netscape
        'mocha\s*:',
        // old Netscape
        'livescript\s*:',
    );

    /**
     * XSS Hash - random Hash for protecting URLs.
     *
     * @var  string
     */
    protected $_xss_hash;

    /**
     * the replacement-string for not allowed strings
     *
     * @var string
     */
    protected $_replacement = '';

    /**
     * list of never allowed strings
     *
     * @var  array
     */
    protected $_never_allowed_str = array();

    /**
     * list of never allowed strings, afterwards
     *
     * @var array
     */
    protected $_never_allowed_str_afterwards = array();

    /**
     * __construct()
     */
    public function __construct()
    {
        $this->_never_allowed_str = array(
            'document.cookie' => $this->_replacement,
            'document.write' => $this->_replacement,
            '.parentNode' => $this->_replacement,
            '.innerHTML' => $this->_replacement,
            '-moz-binding' => $this->_replacement,
            '<!--' => '&lt;!--',
            '-->' => '--&gt;',
            '<![CDATA[' => '&lt;![CDATA[',
            '<!ENTITY' => '&lt;!ENTITY',
            '<!DOCTYPE' => '&lt;!DOCTYPE',
            '<!ATTLIST' => '&lt;!ATTLIST',
            '<comment>' => '&lt;comment&gt;',
        );

        $this->_never_allowed_str_afterwards = array(
            'FSCommand',
            'onAbort',
            'onActivate',
            'onAttribute',
            'onAfterPrint',
            'onAfterScriptExecute',
            'onAfterUpdate',
            'onAnimationEnd',
            'onAnimationIteration',
            'onAnimationStart',
            'onAriaRequest',
            'onAutoComplete',
            'onAutoCompleteError',
            'onBeforeActivate',
            'onBeforeCopy',
            'onBeforeCut',
            'onBeforeDeactivate',
            'onBeforeEditFocus',
            'onBeforePaste',
            'onBeforePrint',
            'onBeforeScriptExecute',
            'onBeforeUnload',
            'onBeforeUpdate',
            'onBegin',
            'onBlur',
            'onBounce',
            'onCancel',
            'onCanPlay',
            'onCanPlayThrough',
            'onCellChange',
            'onChange',
            'onClick',
            'onClose',
            'onCommand',
            'onCompassNeedsCalibration',
            'onContextMenu',
            'onControlSelect',
            'onCopy',
            'onCueChange',
            'onCut',
            'onDataAvailable',
            'onDataSetChanged',
            'onDataSetComplete',
            'onDblClick',
            'onDeactivate',
            'onDeviceLight',
            'onDeviceMotion',
            'onDeviceOrientation',
            'onDeviceProximity',
            'onDrag',
            'onDragDrop',
            'onDragEnd',
            'onDragEnter',
            'onDragLeave',
            'onDragOver',
            'onDragStart',
            'onDrop',
            'onDurationChange',
            'onEmptied',
            'onEnd',
            'onEnded',
            'onError',
            'onErrorUpdate',
            'onExit',
            'onFilterChange',
            'onFinish',
            'onFocus',
            'onFocusIn',
            'onFocusOut',
            'onFormChange',
            'onFormInput',
            'onFullScreenChange',
            'onFullScreenError',
            'onGotPointerCapture',
            'onHashChange',
            'onHelp',
            'onInput',
            'onInvalid',
            'onKeyDown',
            'onKeyPress',
            'onKeyUp',
            'onLanguageChange',
            'onLayoutComplete',
            'onLoad',
            'onLoadedData',
            'onLoadedMetaData',
            'onLoadStart',
            'onLoseCapture',
            'onLostPointerCapture',
            'onMediaComplete',
            'onMediaError',
            'onMessage',
            'onMouseDown',
            'onMouseEnter',
            'onMouseLeave',
            'onMouseMove',
            'onMouseOut',
            'onMouseOver',
            'onMouseUp',
            'onMouseWheel',
            'onMove',
            'onMoveEnd',
            'onMoveStart',
            'onMozFullScreenChange',
            'onMozFullScreenError',
            'onMozPointerLockChange',
            'onMozPointerLockError',
            'onMsContentZoom',
            'onMsFullScreenChange',
            'onMsFullScreenError',
            'onMsGestureChange',
            'onMsGestureDoubleTap',
            'onMsGestureEnd',
            'onMsGestureHold',
            'onMsGestureStart',
            'onMsGestureTap',
            'onMsGotPointerCapture',
            'onMsInertiaStart',
            'onMsLostPointerCapture',
            'onMsManipulationStateChanged',
            'onMsPointerCancel',
            'onMsPointerDown',
            'onMsPointerEnter',
            'onMsPointerLeave',
            'onMsPointerMove',
            'onMsPointerOut',
            'onMsPointerOver',
            'onMsPointerUp',
            'onMsSiteModeJumpListItemRemoved',
            'onMsThumbnailClick',
            'onOffline',
            'onOnline',
            'onOutOfSync',
            'onPage',
            'onPageHide',
            'onPageShow',
            'onPaste',
            'onPause',
            'onPlay',
            'onPlaying',
            'onPointerCancel',
            'onPointerDown',
            'onPointerEnter',
            'onPointerLeave',
            'onPointerLockChange',
            'onPointerLockError',
            'onPointerMove',
            'onPointerOut',
            'onPointerOver',
            'onPointerUp',
            'onPopState',
            'onProgress',
            'onPropertyChange',
            'onRateChange',
            'onReadyStateChange',
            'onReceived',
            'onRepeat',
            'onReset',
            'onResize',
            'onResizeEnd',
            'onResizeStart',
            'onResume',
            'onReverse',
            'onRowDelete',
            'onRowEnter',
            'onRowExit',
            'onRowInserted',
            'onRowsDelete',
            'onRowsEnter',
            'onRowsExit',
            'onRowsInserted',
            'onScroll',
            'onSearch',
            'onSeek',
            'onSeeked',
            'onSeeking',
            'onSelect',
            'onSelectionChange',
            'onSelectStart',
            'onStalled',
            'onStorage',
            'onStorageCommit',
            'onStart',
            'onStop',
            'onShow',
            'onSyncRestored',
            'onSubmit',
            'onSuspend',
            'onSynchRestored',
            'onTimeError',
            'onTimeUpdate',
            'onTrackChange',
            'onTransitionEnd',
            'onToggle',
            'onUnload',
            'onURLFlip',
            'onUserProximity',
            'onVolumeChange',
            'onWaiting',
            'onWebKitAnimationEnd',
            'onWebKitAnimationIteration',
            'onWebKitAnimationStart',
            'onWebKitFullScreenChange',
            'onWebKitFullScreenError',
            'onWebKitTransitionEnd',
            'onWheel',
            'seekSegmentTime',
            'userid',
            'datasrc',
            'datafld',
            'dataformatas',
            'ev:handler',
            'ev:event',
            '0;url',
        );
    }

    /**
     * XSS Clean
     *
     * Sanitizes data so that Cross Site Scripting Hacks can be
     * prevented.  This method does a fair amount of work but
     * it is extremely thorough, designed to prevent even the
     * most obscure XSS attempts.  Nothing is ever 100% foolproof,
     * of course, but I haven't been able to get anything passed
     * the filter.
     *
     * Note: Should only be used to deal with data upon submission.
     *   It's not something that should be used for general
     *   runtime processing.
     *
     * @link  http://channel.bitflux.ch/wiki/XSS_Prevention
     *    Based in part on some code and ideas from Bitflux.
     *
     * @link  http://ha.ckers.org/xss.html
     *    To help develop this script I used this great list of
     *    vulnerabilities along with a few other hacks I've
     *    harvested from examining vulnerabilities in other programs.
     *
     * @param mixed $str input data e.g. string or array
     * @param bool $is_image whether the input is an image
     *
     * @return  string|array|boolean  boolean: will return a boolean, if the "is_image"-parameter is true
     *                                string: will return a string, if the input is a string
     *                                array: will return a array, if the input is a array
     */
    public function xss_clean($str, $is_image = false)
    {
        if (is_array($str)) {
            foreach ($str as &$value) {
                $value = $this->xss_clean($value);
            }

            return $str;
        }

        $str = (string)$str;
        $strInt = (int)$str;
        $strFloat = (float)$str;
        /** @noinspection TypeUnsafeComparisonInspection */
        if (
            !$str
            ||
            $str === null
            ||
            is_bool($str)
            ||
            "$strInt" == $str || is_int($str)
            ||
            "$strFloat" == $str || is_float($str)
        ) {
            return $str;
        }

        // removes all non-UTF-8 characters
        // &&
        // remove NULL characters (ignored by some browsers)
        $str = UTF8::clean($str, true, true, false);

        // decode the string
        $str = $this->decode_string($str);

        // and again... removes all non-UTF-8 characters
        $str = UTF8::clean($str, true, true, false);

        // capture converted string for later comparison
        if ($is_image === true) {
            $converted_string = $str;
        }

        do {
            $old_str = $str;
            $str = $this->_do($str, $is_image);
        } while ($old_str !== $str);

        /*
     * images are Handled in a special way
     *
     * Essentially, we want to know that after all of the character
     * conversion is done whether any unwanted, likely XSS, code was found.
     *
     * If not, we return TRUE, as the image is clean.
     *
     * However, if the string post-conversion does not matched the
     * string post-removal of XSS, then it fails, as there was unwanted XSS
     * code found and removed/changed during processing.
     */
        if ($is_image === true) {
            /** @noinspection PhpUndefinedVariableInspection */
            return ($str === $converted_string);
        }

        return $str;
    }

    /**
     * @param $str
     * @param $is_image
     *
     * @return mixed
     */
    protected function _do($str, $is_image)
    {
        // remove strings that are never allowed
        $str = $this->_do_never_allowed($str);

        // make php tags safe for displaying
        $str = $this->make_php_tags_safe($str, $is_image);

        // corrects words before the browser will do it
        $str = $this->compact_exploded_javascript($str);

        // remove disallowed javascript calls in links, images etc.
        $str = $this->remove_disallowed_javascript($str);

        // remove evil attributes such as style, onclick and xmlns
        $str = $this->remove_evil_attributes($str, $is_image);

        // sanitize naughty HTML elements
        $str = $this->sanitize_naughty_html($str);

        // sanitize naughty JavaScript elements
        $str = $this->sanitize_naughty_javascript($str);

        // final clean up

        // This adds a bit of extra precaution in case
        // something got through the above filters.
        $str = $this->_do_never_allowed($str);
        $str = $this->_do_never_allowed_afterwards($str);

        return $str;
    }

    /**
     * decode the html-tags via "UTF8::html_entity_decode()" or the string via "UTF8::urldecode()"
     *
     * @param string $str
     *
     * @return string
     */
    protected function decode_string($str)
    {
        if (preg_match('/<\w+.*/si', $str, $matches) === 1) {
            $str = preg_replace_callback(
                '/<\w+.*/si',
                array(
                    $this,
                    '_decode_entity',
                ),
                $str
            );
        } else {
            $str = UTF8::urldecode($str);
        }

        return $str;
    }

    /**
     * Do Never Allowed
     *
     * @param string $str
     *
     * @return  string
     */
    protected function _do_never_allowed($str)
    {
        static $neverAllowedRegex;

        if (null === $neverAllowedRegex) {
            $neverAllowedRegex = implode('|', self::$_never_allowed_regex);
        }

        $str = str_ireplace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);

        $str = preg_replace('#' . $neverAllowedRegex . '#is', $this->_replacement, $str);

        return (string)$str;
    }

    /*
   * Makes PHP tags safe
   *
   * Note: XML tags are inadvertently replaced too:
   *
   * <?xml
   *
   * But it doesn't seem to pose a problem.
   *
   * @param string $str
   * @param boolean $is_image
   *
   * @return string
   */
    public function make_php_tags_safe($str, $is_image)
    {
        if ($is_image === true) {
            // Images have a tendency to have the PHP short opening and
            // closing tags every so often so we skip those and only
            // do the long opening tags.
            $str = preg_replace('/<\?(php)/i', '&lt;?\\1', $str);
        } else {
            $str = str_replace(
                array(
                    '<?',
                    '?>',
                ),
                array(
                    '&lt;?',
                    '?&gt;',
                ),
                $str
            );
        }

        return (string)$str;
    }

    /**
     * Compact any exploded words
     *
     * This corrects words like:  j a v a s c r i p t
     * These words are compacted back to their correct state.
     *
     * @param string $str
     *
     * @return string
     */
    public function compact_exploded_javascript($str)
    {
        static $wordsCache;

        $words = array(
            'javascript',
            'expression',
            'vbscript',
            'jscript',
            'wscript',
            'vbs',
            'script',
            'base64',
            'applet',
            'alert',
            'document',
            'write',
            'cookie',
            'window',
            'confirm',
            'prompt',
            'eval',
        );

        foreach ($words as $word) {
            if (!isset($wordsCache[$word])) {
                $word = $wordsCache[$word] = chunk_split($word, 1, '\s*');
            } else {
                $word = $wordsCache[$word];
            }

            // We only want to do this when it is followed by a non-word character
            // That way valid stuff like "dealer to" does not become "dealerto".
            $str = preg_replace_callback(
                '#(' . substr($word, 0, -3) . ')(\W)#is',
                array(
                    $this,
                    '_compact_exploded_words',
                ),
                $str
            );
        }

        return (string)$str;
    }

    /**
     * Remove disallowed Javascript in links or img tags
     * We used to do some version comparisons and use of stripos(),
     * but it is dog slow compared to these simplified non-capturing
     * preg_match(), especially if the pattern exists in the string
     *
     * Note: It was reported that not only space characters, but all in
     * the following pattern can be parsed as separators between a tag name
     * and its attributes: [\d\s"\'`;,\/\=\(\x00\x0B\x09\x0C]
     * ... however, remove_invisible_characters() above already strips the
     * hex-encoded ones, so we'll skip them below.
     *
     * @param string $str
     *
     * @return string
     */
    public function remove_disallowed_javascript($str)
    {
        do {
            $original = $str;

            if (preg_match('/<a/i', $str)) {
                $str = preg_replace_callback(
                    '#<a[^a-z0-9>]+([^>]*?)(?:>|$)#i',
                    array(
                        $this,
                        '_js_link_removal',
                    ),
                    $str
                );
            }

            if (preg_match('/<img/i', $str)) {
                $str = preg_replace_callback(
                    '#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#i',
                    array(
                        $this,
                        '_js_img_removal',
                    ),
                    $str
                );
            }

            if (preg_match('/script|xss/i', $str)) {
                $str = preg_replace('#</*(?:script|xss).*?>#si', $this->_replacement, $str);
            }
        } while ($original !== $str);

        return (string)$str;
    }

    /**
     * Remove Evil HTML Attributes (like event handlers and style)
     *
     * It removes the evil attribute and either:
     *
     *  - Everything up until a space. For example, everything between the pipes:
     *
     *  <code>
     *    <a |style=document.write('hello');alert('world');| class=link>
     *  </code>
     *
     *  - Everything inside the quotes. For example, everything between the pipes:
     *
     *  <code>
     *    <a |style="document.write('hello'); alert('world');"| class="link">
     *  </code>
     *
     * @param string $str The string to check
     * @param bool $is_image Whether the input is an image
     *
     * @return  string  The string with the evil attributes removed
     */
    public function remove_evil_attributes($str, $is_image)
    {
        // https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet#Event_Handlers

        $evil_attributes = array(
            'on\w*',
            'style',
            'xmlns',
            'formaction',
            'form',
            'xlink:href',
            'seekSegmentTime',
            'FSCommand',
            'eval',
        );

        if ($is_image === true) {
            /*
       * Adobe Photoshop puts XML metadata into JFIF images,
       * including namespacing, so we have to allow this for images.
       */
            unset($evil_attributes[array_search('xmlns', $evil_attributes, true)]);
        }

        $evil_attributes_string = implode('|', $evil_attributes);

        do {
            $count = $temp_count = 0;

            // replace occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
            $str = preg_replace('/(<[^>]+)(?<!\w)(' . $evil_attributes_string . ')\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is', '$1' . $this->_replacement, $str, -1, $temp_count);
            $count += $temp_count;

            // find occurrences of illegal attribute strings without quotes
            $str = preg_replace('/(<[^>]+)(?<!\w)(' . $evil_attributes_string . ')\s*=\s*([^\s>]*)/is', '$1' . $this->_replacement, $str, -1, $temp_count);
            $count += $temp_count;
        } while ($count);

        return (string)$str;
    }

    /**
     * Sanitize naughty HTML elements
     *
     * If a tag containing any of the words in the list
     * below is found, the tag gets converted to entities.
     *
     * So this: <blink>
     * Becomes: &lt;blink&gt;
     *
     * @param string $str
     *
     * @return string
     */
    public function sanitize_naughty_html($str)
    {
        $naughty = 'alert|prompt|confirm|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|button|select|isindex|layer|link|meta|keygen|object|plaintext|style|script|textarea|title|math|video|svg|xml|xss|eval';
        $str = preg_replace_callback(
            '#<(/*\s*)(' . $naughty . ')([^><]*)([><]*)#is',
            array(
                $this,
                '_sanitize_naughty_html',
            ),
            $str
        );

        return (string)$str;
    }

    /**
     * Sanitize naughty scripting elements
     *
     * Similar to above, only instead of looking for
     * tags it looks for PHP and JavaScript commands
     * that are disallowed. Rather than removing the
     * code, it simply converts the parenthesis to entities
     * rendering the code un-executable.
     *
     * For example:  eval('some code')
     * Becomes:  eval&#40;'some code'&#41;
     *
     * @param string $str
     *
     * @return string
     */
    public function sanitize_naughty_javascript($str)
    {
        $str = preg_replace(
            '#(alert|eval|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
            '\\1\\2&#40;\\3&#41;',
            $str
        );

        return (string)$str;
    }

    /**
     * Do Never Allowed Afterwards
     *
     * clean-up also some string, also if there is no html-tag
     *
     * @param string $str
     *
     * @return  string
     */
    protected function _do_never_allowed_afterwards($str)
    {
        static $neverAllowedStrAfterwardsRegex;

        if (null === $neverAllowedStrAfterwardsRegex) {
            foreach ($this->_never_allowed_str_afterwards as &$neverAllowedStr) {
                $neverAllowedStr .= '.*=';
            }
            unset($neverAllowedStr);

            $neverAllowedStrAfterwardsRegex = implode('|', $this->_never_allowed_str_afterwards);
        }

        $str = preg_replace('#' . $neverAllowedStrAfterwardsRegex . '#isU', $this->_replacement, $str);

        return (string)$str;
    }

    /**
     * set the replacement-string for not allowed strings
     *
     * @param string $string
     */
    public function setReplacement($string)
    {
        $this->_replacement = (string)$string;
    }

    /**
     * Compact Exploded Words
     *
     * Callback method for xss_clean() to remove whitespace from
     * things like 'j a v a s c r i p t'.
     *
     * @param array $matches
     *
     * @return  string
     */
    protected function _compact_exploded_words($matches)
    {
        return preg_replace('/\s+/', '', $matches[1]) . $matches[2];
    }

    /**
     * Sanitize Naughty HTML
     *
     * Callback method for AntiXSS->sanitize_naughty_html() to remove naughty HTML elements.
     *
     * @param array $matches
     *
     * @return  string
     */
    protected function _sanitize_naughty_html($matches)
    {
        return '&lt;' . $matches[1] . $matches[2] . $matches[3] // encode opening brace
            // encode captured opening or closing brace to prevent recursive vectors:
            . str_replace(
                array(
                    '>',
                    '<',
                ),
                array(
                    '&gt;',
                    '&lt;',
                ),
                $matches[4]
            );
    }

    /**
     * JS Image Removal
     *
     * Callback method for xss_clean() to sanitize image tags.
     *
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on image tag heavy strings.
     *
     * @param array $match
     *
     * @return  string
     */
    protected function _js_img_removal($match)
    {
        return $this->_js_removal($match, 'src');
    }

    /**
     * JS Removal
     *
     * Callback method for xss_clean() to sanitize tags.
     *
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on image tag heavy strings.
     *
     * @param array $match
     * @param string $search
     *
     * @return  string
     */
    protected function _js_removal($match, $search)
    {
        if (!$match[0]) {
            return '';
        }

        $replacer = preg_replace(
            '#' . $search . '=.*?(?:(?:alert|prompt|confirm)(?:\((\')*|&\#40;)|javascript:|livescript:|wscript:|vbscript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
            '',
            $this->_filter_attributes(str_replace(array('<', '>',), '', $match[1]))
        );

        return str_ireplace($match[1], $replacer, $match[0]);
    }

    /**
     * Filter Attributes
     *
     * Filters tag attributes for consistency and safety.
     *
     * @param string $str
     *
     * @return  string
     */
    protected function _filter_attributes($str)
    {
        if ($str === '') {
            return '';
        }

        $out = '';
        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#i', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace('#/\*.*?\*/#s', '', $match);
            }
        }

        return $out;
    }

    /**
     * JS Link Removal
     *
     * Callback method for xss_clean() to sanitize links.
     *
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on link-heavy strings.
     *
     * @param array $match
     *
     * @return  string
     */
    protected function _js_link_removal($match)
    {
        return $this->_js_removal($match, 'href');
    }

    /**
     * HTML Entity Decode Callback
     *
     * @param array $match
     *
     * @return  string
     */
    protected function _decode_entity($match)
    {
        $hash = $this->xss_hash();

        // protect GET variables in URLs
        // 901119URL5918AMP18930PROTECT8198
        $match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', $hash . '\\1=\\2', $match[0]);

        // un-protect URL GET vars
        return str_replace($this->xss_hash(), '&', $this->_entity_decode($match));
    }

    /**
     * XSS Hash
     *
     * Generates the XSS hash if needed and returns it.
     *
     * @return  string  XSS hash
     */
    public function xss_hash()
    {
        if ($this->_xss_hash === null) {
            $rand = Bootup::get_random_bytes(16);

            if (!$rand) {
                $this->_xss_hash = md5(uniqid(mt_rand(), true));
            } else {
                $this->_xss_hash = bin2hex($rand);
            }
        }

        return $this->_xss_hash;
    }

    /**
     * @param $str
     *
     * @return string
     */
    protected function _entity_decode($str)
    {
        static $entities;

        $flags = Bootup::is_php('5.4') ? ENT_QUOTES | ENT_HTML5 : ENT_QUOTES;

        // decode
        if (strpos($str, $this->xss_hash()) !== false) {
            $str = UTF8::html_entity_decode($str, $flags);
        } else {
            $str = UTF8::urldecode($str);
        }

        // decode-again, for e.g. HHVM, PHP 5.3, miss configured applications ...
        if (preg_match_all('/&[a-z]{2,}[;]{0}/i', $str, $matches)) {
            if (null === $entities) {

                // links:
                // - http://dev.w3.org/html5/html-author/charref
                // - http://www.w3schools.com/charsets/ref_html_entities_n.asp
                $entitiesSecurity = array(
                    '&#x00000;' => '',
                    '&#0;' => '',
                    '&#x00001;' => '',
                    '&#1;' => '',
                    '&nvgt;' => '',
                    '&#61253;' => '',
                    '&#x0EF45;' => '',
                    '&shy;' => '',
                    '&#x000AD;' => '',
                    '&#173;' => '',
                    '&colon;' => ':',
                    '&#x0003A;' => ':',
                    '&#58;' => ':',
                    '&lpar;' => '(',
                    '&#x00028;' => '(',
                    '&#40;' => '(',
                    '&rpar;' => ')',
                    '&#x00029;' => ')',
                    '&#41;' => ')',
                    '&quest;' => '?',
                    '&#x0003F;' => '?',
                    '&#63;' => '?',
                    '&sol;' => '/',
                    '&#x0002F;' => '/',
                    '&#47;' => '/',
                    '&apos;' => '\'',
                    '&#x00027;' => '\'',
                    '&#39;' => '\'',
                    '&bsol;' => '\\',
                    '&#x0005C;' => '\\',
                    '&#92;' => '\\',
                    '&comma;' => ',',
                    '&#x0002C;' => ',',
                    '&#44;' => ',',
                    '&period;' => '.',
                    '&#x0002E;' => '.',
                    '&quot;' => '"',
                    '&QUOT;' => '"',
                    '&#x00022;' => '"',
                    '&#34;' => '"',
                    '&grave;' => '`',
                    '&DiacriticalGrave;' => '`',
                    '&#x00060;' => '`',
                    '&#96;' => '`',
                    '&#46;' => '.',
                    '&equals;' => '=',
                    '&#x0003D;' => '=',
                    '&#61;' => '=',
                    '&newline;' => "\n",
                    '&#x0000A;' => "\n",
                    '&#10;' => "\n",
                    '&tab;' => "\t",
                    '&#x00009;' => "\n",
                    '&#9;' => "\n",
                );

                $entitiesTmp = get_html_translation_table(HTML_ENTITIES, $flags);
                $entitiesTmp = array_merge(self::$entitiesFallback, $entitiesTmp);

                $entities = array_merge(
                    $entitiesSecurity,
                    array_map('strtolower', array_flip($entitiesTmp))
                );
            }

            $replace = array();
            $matches = array_unique(array_map('strtolower', $matches[0]));
            foreach ($matches as $match) {
                $match .= ';';
                if (array_key_exists($match, $entities) === true) {
                    $replace[$match] = $entities[$match];
                }
            }

            if (count($replace) > 0) {
                $str = str_ireplace(array_keys($replace), array_values($replace), $str);
            }
        }

        return $str;
    }
}
