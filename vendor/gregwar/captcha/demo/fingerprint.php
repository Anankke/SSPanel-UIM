<?php

include(__DIR__.'/../CaptchaBuilderInterface.php');
include(__DIR__.'/../PhraseBuilderInterface.php');
include(__DIR__.'/../CaptchaBuilder.php');
include(__DIR__.'/../PhraseBuilder.php');

use Gregwar\Captcha\CaptchaBuilder;

echo count(CaptchaBuilder::create()
    ->build()
    ->getFingerprint()
);

echo "\n";
