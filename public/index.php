<?php

//  PUBLIC_PATH
define('PUBLIC_PATH', __DIR__);

// Bootstrap
require PUBLIC_PATH.'/../bootstrap.php';


// Init slim routes
require BASE_PATH.'/config/routes.php';
