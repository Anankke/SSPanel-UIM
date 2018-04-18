<?php

/***
 * ss-panel v3 Bootstrap
 * @author orvice
 * @email sspanel@orx.me
 * @url https://github.com/orvice/ss-panel
 */

use App\Services\Boot;

//  BASE_PATH
define('BASE_PATH', __DIR__);
define('VERSION', '20180419');

// Vendor Autoload
require BASE_PATH.'/vendor/autoload.php';

require BASE_PATH."/config/.config.php";



Boot::loadEnv();
Boot::setDebug();
//Boot::setVersion(VERSION);
// config time zone
Boot::setTimezone();
// Init db
Boot::bootDb();
