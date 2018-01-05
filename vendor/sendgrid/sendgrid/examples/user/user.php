<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Get a user's account information. #
// GET /user/account #

$response = $sg->client->user()->account()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve your credit balance #
// GET /user/credits #

$response = $sg->client->user()->credits()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update your account email address #
// PUT /user/email #

$request_body = json_decode('{
  "email": "example@example.com"
}');
$response = $sg->client->user()->email()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve your account email address #
// GET /user/email #

$response = $sg->client->user()->email()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update your password #
// PUT /user/password #

$request_body = json_decode('{
  "new_password": "new_password", 
  "old_password": "old_password"
}');
$response = $sg->client->user()->password()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update a user's profile #
// PATCH /user/profile #

$request_body = json_decode('{
  "city": "Orange", 
  "first_name": "Example", 
  "last_name": "User"
}');
$response = $sg->client->user()->profile()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Get a user's profile #
// GET /user/profile #

$response = $sg->client->user()->profile()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Cancel or pause a scheduled send #
// POST /user/scheduled_sends #

$request_body = json_decode('{
  "batch_id": "YOUR_BATCH_ID", 
  "status": "pause"
}');
$response = $sg->client->user()->scheduled_sends()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all scheduled sends #
// GET /user/scheduled_sends #

$response = $sg->client->user()->scheduled_sends()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update user scheduled send information #
// PATCH /user/scheduled_sends/{batch_id} #

$request_body = json_decode('{
  "status": "pause"
}');
$batch_id = "test_url_param";
$response = $sg->client->user()->scheduled_sends()->_($batch_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve scheduled send #
// GET /user/scheduled_sends/{batch_id} #

$batch_id = "test_url_param";
$response = $sg->client->user()->scheduled_sends()->_($batch_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a cancellation or pause of a scheduled send #
// DELETE /user/scheduled_sends/{batch_id} #

$batch_id = "test_url_param";
$response = $sg->client->user()->scheduled_sends()->_($batch_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update Enforced TLS settings #
// PATCH /user/settings/enforced_tls #

$request_body = json_decode('{
  "require_tls": true, 
  "require_valid_cert": false
}');
$response = $sg->client->user()->settings()->enforced_tls()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve current Enforced TLS settings. #
// GET /user/settings/enforced_tls #

$response = $sg->client->user()->settings()->enforced_tls()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update your username #
// PUT /user/username #

$request_body = json_decode('{
  "username": "test_username"
}');
$response = $sg->client->user()->username()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve your username #
// GET /user/username #

$response = $sg->client->user()->username()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update Event Notification Settings #
// PATCH /user/webhooks/event/settings #

$request_body = json_decode('{
  "bounce": true, 
  "click": true, 
  "deferred": true, 
  "delivered": true, 
  "dropped": true, 
  "enabled": true, 
  "group_resubscribe": true, 
  "group_unsubscribe": true, 
  "open": true, 
  "processed": true, 
  "spam_report": true, 
  "unsubscribe": true, 
  "url": "url"
}');
$response = $sg->client->user()->webhooks()->event()->settings()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve Event Webhook settings #
// GET /user/webhooks/event/settings #

$response = $sg->client->user()->webhooks()->event()->settings()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Test Event Notification Settings  #
// POST /user/webhooks/event/test #

$request_body = json_decode('{
  "url": "url"
}');
$response = $sg->client->user()->webhooks()->event()->test()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Create a parse setting #
// POST /user/webhooks/parse/settings #

$request_body = json_decode('{
  "hostname": "myhostname.com", 
  "send_raw": false, 
  "spam_check": true, 
  "url": "http://email.myhosthame.com"
}');
$response = $sg->client->user()->webhooks()->parse()->settings()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all parse settings #
// GET /user/webhooks/parse/settings #

$response = $sg->client->user()->webhooks()->parse()->settings()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update a parse setting #
// PATCH /user/webhooks/parse/settings/{hostname} #

$request_body = json_decode('{
  "send_raw": true, 
  "spam_check": false, 
  "url": "http://newdomain.com/parse"
}');
$hostname = "test_url_param";
$response = $sg->client->user()->webhooks()->parse()->settings()->_($hostname)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a specific parse setting #
// GET /user/webhooks/parse/settings/{hostname} #

$hostname = "test_url_param";
$response = $sg->client->user()->webhooks()->parse()->settings()->_($hostname)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a parse setting #
// DELETE /user/webhooks/parse/settings/{hostname} #

$hostname = "test_url_param";
$response = $sg->client->user()->webhooks()->parse()->settings()->_($hostname)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieves Inbound Parse Webhook statistics. #
// GET /user/webhooks/parse/stats #

$query_params = json_decode('{"aggregated_by": "day", "limit": "test_string", "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": "test_string"}');
$response = $sg->client->user()->webhooks()->parse()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

