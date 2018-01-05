<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Retrieve Tracking Settings #
// GET /tracking_settings #

$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->tracking_settings()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update Click Tracking Settings #
// PATCH /tracking_settings/click #

$request_body = json_decode('{
  "enabled": true
}');
$response = $sg->client->tracking_settings()->click()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve Click Track Settings #
// GET /tracking_settings/click #

$response = $sg->client->tracking_settings()->click()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update Google Analytics Settings #
// PATCH /tracking_settings/google_analytics #

$request_body = json_decode('{
  "enabled": true, 
  "utm_campaign": "website", 
  "utm_content": "", 
  "utm_medium": "email", 
  "utm_source": "sendgrid.com", 
  "utm_term": ""
}');
$response = $sg->client->tracking_settings()->google_analytics()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve Google Analytics Settings #
// GET /tracking_settings/google_analytics #

$response = $sg->client->tracking_settings()->google_analytics()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update Open Tracking Settings #
// PATCH /tracking_settings/open #

$request_body = json_decode('{
  "enabled": true
}');
$response = $sg->client->tracking_settings()->open()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Get Open Tracking Settings #
// GET /tracking_settings/open #

$response = $sg->client->tracking_settings()->open()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update Subscription Tracking Settings #
// PATCH /tracking_settings/subscription #

$request_body = json_decode('{
  "enabled": true, 
  "html_content": "html content", 
  "landing": "landing page html", 
  "plain_content": "text content", 
  "replace": "replacement tag", 
  "url": "url"
}');
$response = $sg->client->tracking_settings()->subscription()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve Subscription Tracking Settings #
// GET /tracking_settings/subscription #

$response = $sg->client->tracking_settings()->subscription()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

