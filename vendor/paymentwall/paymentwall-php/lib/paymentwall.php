<?php

if (!function_exists('curl_init')) {
	throw new Exception('CURL PHP extension is required');
}

if (!function_exists('json_decode')) {
	throw new Exception('JSON PHP extension is required');
}

require_once(dirname(__FILE__) . '/Paymentwall/Instance.php');

require_once(dirname(__FILE__) . '/Paymentwall/Config.php');

require_once(dirname(__FILE__) . '/Paymentwall/Base.php');

require_once(dirname(__FILE__) . '/Paymentwall/Product.php');

require_once(dirname(__FILE__) . '/Paymentwall/Widget.php');

require_once(dirname(__FILE__) . '/Paymentwall/Pingback.php');

require_once(dirname(__FILE__) . '/Paymentwall/Card.php');

require_once(dirname(__FILE__) . '/Paymentwall/ApiObject.php');

require_once(dirname(__FILE__) . '/Paymentwall/ApiObjectInterface.php');

require_once(dirname(__FILE__) . '/Paymentwall/OneTimeToken.php');

require_once(dirname(__FILE__) . '/Paymentwall/Charge.php');

require_once(dirname(__FILE__) . '/Paymentwall/Subscription.php');

require_once(dirname(__FILE__) . '/Paymentwall/HttpAction.php');

require_once(dirname(__FILE__) . '/Paymentwall/Signature/Abstract.php');

require_once(dirname(__FILE__) . '/Paymentwall/Signature/Widget.php');

require_once(dirname(__FILE__) . '/Paymentwall/Signature/Pingback.php');

require_once(dirname(__FILE__) . '/Paymentwall/Response/Factory.php');

require_once(dirname(__FILE__) . '/Paymentwall/Response/Abstract.php');

require_once(dirname(__FILE__) . '/Paymentwall/Response/Interface.php');

require_once(dirname(__FILE__) . '/Paymentwall/Response/Success.php');

require_once(dirname(__FILE__) . '/Paymentwall/Response/Error.php');

require_once(dirname(__FILE__) . '/Paymentwall/GenerericApiObject.php');