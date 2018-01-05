Mailgun-PHP
===========

This is the Mailgun PHP SDK. This SDK contains methods for easily interacting 
with the Mailgun API. 
Below are examples to get you started. For additional examples, please see our 
official documentation 
at http://documentation.mailgun.com

[![Latest Stable Version](https://poser.pugx.org/mailgun/mailgun-php/v/stable.png)](https://packagist.org/packages/mailgun/mailgun-php)
[![Build Status](https://travis-ci.org/mailgun/mailgun-php.png)](https://travis-ci.org/mailgun/mailgun-php)

Installation
------------
To install the SDK, you will need to be using [Composer](http://getcomposer.org/) 
in your project. 
If you aren't using Composer yet, it's really simple! Here's how to install 
composer and the Mailgun SDK.

```PHP
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Add Mailgun as a dependency
php composer.phar require mailgun/mailgun-php:~1.7.1
``` 

**For shared hosts without SSH access, check out our [Shared Host Instructions](SharedHostInstall.md).**

**Rather just download the files? [Library Download](https://9f67cbbd1116d8afb399-7760483f5d1e5f28c2d253278a2a5045.ssl.cf2.rackcdn.com/mailgun-php-1.7.1.zip).**

Next, require Composer's autoloader, in your application, to automatically 
load the Mailgun SDK in your project:
```PHP
require 'vendor/autoload.php';
use Mailgun\Mailgun;
```

Usage
-----
Here's how to send a message using the SDK:

```php
# First, instantiate the SDK with your API credentials and define your domain. 
$mg = new Mailgun("key-example");
$domain = "example.com";

# Now, compose and send your message.
$mg->sendMessage($domain, array('from'    => 'bob@example.com', 
                                'to'      => 'sally@example.com', 
                                'subject' => 'The PHP SDK is awesome!', 
                                'text'    => 'It is so simple to send a message.'));
```

Or obtain the last 25 log items: 
```php
# First, instantiate the SDK with your API credentials and define your domain. 
$mg = new Mailgun("key-example");
$domain = "example.com";

# Now, issue a GET against the Logs endpoint.
$mg->get("$domain/log", array('limit' => 25, 
                              'skip'  => 0));
```

Response
--------

The results, provided by the endpoint, are returned as an object, which you 
can traverse like an array. 

Example: 

```php
$mg = new Mailgun("key-example");
$domain = "example.com";

$result = $mg->get("$domain/log", array('limit' => 25, 
                                        'skip'  => 0));

$httpResponseCode = $result->http_response_code;
$httpResponseBody = $result->http_response_body;

# Iterate through the results and echo the message IDs.
$logItems = $result->http_response_body->items;
foreach($logItems as $logItem){
    echo $logItem->message_id . "\n";
}
```

Example Contents:  
**$httpResponseCode** will contain an integer. You can find how we use HTTP response 
codes in our documentation: 
http://documentation.mailgun.com/api-intro.html?highlight=401#errors

**$httpResponseBody** will contain an object of the API response. In the above 
example, a var_dump($result) would contain the following: 

```
object(stdClass)#26 (2) {
["http_response_body"]=>
  object(stdClass)#26 (2) {
    ["total_count"]=>
    int(12)
    ["items"]=>
    array(1) {
      [0]=>
      object(stdClass)#31 (5) {
        ["hap"]=>
        string(9) "delivered"
        ["created_at"]=>
        string(29) "Tue, 20 Aug 2013 20:24:34 GMT"
        ["message"]=>
        string(66) "Delivered: me@samples.mailgun.org → travis@mailgunhq.com 'Hello'"
        ["type"]=>
        string(4) "info"
        ["message_id"]=>
        string(46) "20130820202406.24739.21973@samples.mailgun.org"
      }
    }
  }
}
```

Debugging
---------

Debugging the PHP SDK can be really helpful when things aren't working quite right. 
To debug the SDK, here are some suggestions: 

Set the endpoint to Mailgun's Postbin. A Postbin is a web service that allows you to 
post data, which is then displayed through a browser. This allows you to quickly determine
what is actually being transmitted to Mailgun's API. 

**Step 1 - Create a new Postbin.**  
Go to http://bin.mailgun.net. The Postbin will generate a special URL. Save that URL. 

**Step 2 - Instantiate the Mailgun client using Postbin.**  

*Tip: The bin id will be the URL part after bin.mailgun.net. It will be random generated letters and numbers. For example, the bin id in this URL, http://bin.mailgun.net/aecf68de, is "aecf68de".*

```php
# First, instantiate the SDK with your API credentials and define your domain. 
$mg = new Mailgun('key-example', 'bin.mailgun.net', 'aecf68de', $ssl = False);
$domain = 'example.com';

# Now, compose and send your message.
$mg->sendMessage($domain, array('from'    => 'bob@example.com', 
                                'to'      => 'sally@example.com', 
                                'subject' => 'The PHP SDK is awesome!', 
                                'text'    => 'It is so simple to send a message.'));
```
Additional Info
---------------

For usage examples on each API endpoint, head over to our official documentation 
pages. 

This SDK includes a [Message Builder](src/Mailgun/Messages/README.md), 
[Batch Message](src/Mailgun/Messages/README.md) and [Opt-In Handler](src/Mailgun/Lists/README.md) component.

Message Builder allows you to quickly create the array of parameters, required 
to send a message, by calling a methods for each parameter.
Batch Message is an extension of Message Builder, and allows you to easily send 
a batch message job within a few seconds. The complexity of 
batch messaging is eliminated! 

Support and Feedback
--------------------

Be sure to visit the Mailgun official 
[documentation website](http://documentation.mailgun.com/) for additional 
information about our API. 

If you find a bug, please submit the issue in Github directly. 
[Mailgun-PHP Issues](https://github.com/mailgun/Mailgun-PHP/issues)

As always, if you need additional assistance, drop us a note through your Control Panel at
[https://mailgun.com/cp/support](https://mailgun.com/cp/support).

