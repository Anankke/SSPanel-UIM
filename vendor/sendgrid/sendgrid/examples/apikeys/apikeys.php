<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Create API keys #
// POST /api_keys #

$request_body = json_decode('{
  "name": "My API Key", 
  "sample": "data", 
  "scopes": [
    "mail.send", 
    "alerts.create", 
    "alerts.read"
  ]
}');
$response = $sg->client->api_keys()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all API Keys belonging to the authenticated user #
// GET /api_keys #

$query_params = json_decode('{"limit": 1}');
$response = $sg->client->api_keys()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update the name & scopes of an API Key #
// PUT /api_keys/{api_key_id} #

$request_body = json_decode('{
  "name": "A New Hope", 
  "scopes": [
    "user.profile.read", 
    "user.profile.update"
  ]
}');
$api_key_id = "test_url_param";
$response = $sg->client->api_keys()->_($api_key_id)->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update API keys #
// PATCH /api_keys/{api_key_id} #

$request_body = json_decode('{
  "name": "A New Hope"
}');
$api_key_id = "test_url_param";
$response = $sg->client->api_keys()->_($api_key_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve an existing API Key #
// GET /api_keys/{api_key_id} #

$api_key_id = "test_url_param";
$response = $sg->client->api_keys()->_($api_key_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete API keys #
// DELETE /api_keys/{api_key_id} #

$api_key_id = "test_url_param";
$response = $sg->client->api_keys()->_($api_key_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

