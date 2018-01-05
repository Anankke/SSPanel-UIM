#!/usr/bin/env php
<?php

/*
 * From: http://raamdev.com/2008/amazon-s3-hmac-signatures-without-pear-or-php5/
 */

/*
 * Calculate HMAC-SHA1 according to RFC2104
 * See http://www.faqs.org/rfcs/rfc2104.html
 */
function hmacsha1($key,$data) {
    $blocksize=64;
    $hashfunc='sha1';
    if (strlen($key)>$blocksize)
        $key=pack('H*', $hashfunc($key));
    $key=str_pad($key,$blocksize,chr(0x00));
    $ipad=str_repeat(chr(0x36),$blocksize);
    $opad=str_repeat(chr(0x5c),$blocksize);
    $hmac = pack(
                'H*',$hashfunc(
                    ($key^$opad).pack(
                        'H*',$hashfunc(
                            ($key^$ipad).$data
                        )
                    )
                )
            );
    return bin2hex($hmac);
}

/*
 * Used to encode a field for Amazon Auth
 * (taken from the Amazon S3 PHP example library)
 */
function hex2b64($str)
{
    $raw = '';
    for ($i=0; $i < strlen($str); $i+=2)
    {
        $raw .= chr(hexdec(substr($str, $i, 2)));
    }
    return base64_encode($raw);
}

if (count($argv) != 3) {
  echo "Usage: " . $argv[0] . " <S3 Policy File> <S3 secret key>\n";
  exit(1);
}

$policy = file_get_contents($argv[1]);
$secret = $argv[2];

/*
 * Base64 encode the Policy Document and then
 * create HMAC SHA-1 signature of the base64 encoded policy
 * using the secret key. Finally, encode it for Amazon Authentication.
 */
$base64_policy = base64_encode($policy);
$signature = hex2b64(hmacsha1($secret, $base64_policy));
echo "S3_POLICY=\"" . $base64_policy . "\"\nS3_SIGNATURE=\"" . $signature . "\"\n"
?>
