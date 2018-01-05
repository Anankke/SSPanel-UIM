<?php

namespace Gregwar\Captcha;

/**
 * Generates random phrase
 *
 * @author Gregwar <g.passault@gmail.com>
 */
class PhraseBuilder implements PhraseBuilderInterface
{
    /**
     * Generates  random phrase of given length with given charset
     */
    public function build($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789')
    {
        $phrase = '';
        $chars = str_split($charset);

        for ($i = 0; $i < $length; $i++) {
            $phrase .= $chars[array_rand($chars)];
        }

        return $phrase;
    }

    /**
     * "Niceize" a code
     */
    public function niceize($str)
    {
        return strtr(strtolower($str), '01', 'ol');
    }
}
