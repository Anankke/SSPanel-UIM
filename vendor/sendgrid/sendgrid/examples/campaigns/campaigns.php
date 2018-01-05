<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Create a Campaign #
// POST /campaigns #

$request_body = json_decode('{
  "categories": [
    "spring line"
  ], 
  "custom_unsubscribe_url": "", 
  "html_content": "<html><head><title></title></head><body><p>Check out our spring line!</p></body></html>", 
  "ip_pool": "marketing", 
  "list_ids": [
    110, 
    124
  ], 
  "plain_content": "Check out our spring line!", 
  "segment_ids": [
    110
  ], 
  "sender_id": 124451, 
  "subject": "New Products for Spring!", 
  "suppression_group_id": 42, 
  "title": "March Newsletter"
}');
$response = $sg->client->campaigns()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all Campaigns #
// GET /campaigns #

$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->campaigns()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update a Campaign #
// PATCH /campaigns/{campaign_id} #

$request_body = json_decode('{
  "categories": [
    "summer line"
  ], 
  "html_content": "<html><head><title></title></head><body><p>Check out our summer line!</p></body></html>", 
  "plain_content": "Check out our summer line!", 
  "subject": "New Products for Summer!", 
  "title": "May Newsletter"
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a single campaign #
// GET /campaigns/{campaign_id} #

$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a Campaign #
// DELETE /campaigns/{campaign_id} #

$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update a Scheduled Campaign #
// PATCH /campaigns/{campaign_id}/schedules #

$request_body = json_decode('{
  "send_at": 1489451436
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Schedule a Campaign #
// POST /campaigns/{campaign_id}/schedules #

$request_body = json_decode('{
  "send_at": 1489771528
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// View Scheduled Time of a Campaign #
// GET /campaigns/{campaign_id}/schedules #

$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Unschedule a Scheduled Campaign #
// DELETE /campaigns/{campaign_id}/schedules #

$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Send a Campaign #
// POST /campaigns/{campaign_id}/schedules/now #

$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->now()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Send a Test Campaign #
// POST /campaigns/{campaign_id}/schedules/test #

$request_body = json_decode('{
  "to": "your.email@example.com"
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->test()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

