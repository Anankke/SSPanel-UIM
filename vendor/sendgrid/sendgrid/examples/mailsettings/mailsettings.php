<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Retrieve all mail settings #
// GET /mail_settings #

$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->mail_settings()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update address whitelist mail settings #
// PATCH /mail_settings/address_whitelist #

$request_body = json_decode('{
  "enabled": true, 
  "list": [
    "email1@example.com", 
    "example.com"
  ]
}');
$response = $sg->client->mail_settings()->address_whitelist()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve address whitelist mail settings #
// GET /mail_settings/address_whitelist #

$response = $sg->client->mail_settings()->address_whitelist()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update BCC mail settings #
// PATCH /mail_settings/bcc #

$request_body = json_decode('{
  "email": "email@example.com", 
  "enabled": false
}');
$response = $sg->client->mail_settings()->bcc()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all BCC mail settings #
// GET /mail_settings/bcc #

$response = $sg->client->mail_settings()->bcc()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update bounce purge mail settings #
// PATCH /mail_settings/bounce_purge #

$request_body = json_decode('{
  "enabled": true, 
  "hard_bounces": 5, 
  "soft_bounces": 5
}');
$response = $sg->client->mail_settings()->bounce_purge()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve bounce purge mail settings #
// GET /mail_settings/bounce_purge #

$response = $sg->client->mail_settings()->bounce_purge()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update footer mail settings #
// PATCH /mail_settings/footer #

$request_body = json_decode('{
  "enabled": true, 
  "html_content": "...", 
  "plain_content": "..."
}');
$response = $sg->client->mail_settings()->footer()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve footer mail settings #
// GET /mail_settings/footer #

$response = $sg->client->mail_settings()->footer()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update forward bounce mail settings #
// PATCH /mail_settings/forward_bounce #

$request_body = json_decode('{
  "email": "example@example.com", 
  "enabled": true
}');
$response = $sg->client->mail_settings()->forward_bounce()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve forward bounce mail settings #
// GET /mail_settings/forward_bounce #

$response = $sg->client->mail_settings()->forward_bounce()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update forward spam mail settings #
// PATCH /mail_settings/forward_spam #

$request_body = json_decode('{
  "email": "", 
  "enabled": false
}');
$response = $sg->client->mail_settings()->forward_spam()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve forward spam mail settings #
// GET /mail_settings/forward_spam #

$response = $sg->client->mail_settings()->forward_spam()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update plain content mail settings #
// PATCH /mail_settings/plain_content #

$request_body = json_decode('{
  "enabled": false
}');
$response = $sg->client->mail_settings()->plain_content()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve plain content mail settings #
// GET /mail_settings/plain_content #

$response = $sg->client->mail_settings()->plain_content()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update spam check mail settings #
// PATCH /mail_settings/spam_check #

$request_body = json_decode('{
  "enabled": true, 
  "max_score": 5, 
  "url": "url"
}');
$response = $sg->client->mail_settings()->spam_check()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve spam check mail settings #
// GET /mail_settings/spam_check #

$response = $sg->client->mail_settings()->spam_check()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update template mail settings #
// PATCH /mail_settings/template #

$request_body = json_decode('{
  "enabled": true, 
  "html_content": "<% body %>"
}');
$response = $sg->client->mail_settings()->template()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve legacy template mail settings #
// GET /mail_settings/template #

$response = $sg->client->mail_settings()->template()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

