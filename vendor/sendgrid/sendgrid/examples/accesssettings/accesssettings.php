<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Retrieve all recent access attempts #
// GET /access_settings/activity #

$query_params = json_decode('{"limit": 1}');
$response = $sg->client->access_settings()->activity()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Add one or more IPs to the whitelist #
// POST /access_settings/whitelist #

$request_body = json_decode('{
  "ips": [
    {
      "ip": "192.168.1.1"
    }, 
    {
      "ip": "192.*.*.*"
    }, 
    {
      "ip": "192.168.1.3/32"
    }
  ]
}');
$response = $sg->client->access_settings()->whitelist()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a list of currently whitelisted IPs #
// GET /access_settings/whitelist #

$response = $sg->client->access_settings()->whitelist()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Remove one or more IPs from the whitelist #
// DELETE /access_settings/whitelist #

$request_body = json_decode('{
  "ids": [
    1, 
    2, 
    3
  ]
}');
$response = $sg->client->access_settings()->whitelist()->delete($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a specific whitelisted IP #
// GET /access_settings/whitelist/{rule_id} #

$rule_id = "test_url_param";
$response = $sg->client->access_settings()->whitelist()->_($rule_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Remove a specific IP from the whitelist #
// DELETE /access_settings/whitelist/{rule_id} #

$rule_id = "test_url_param";
$response = $sg->client->access_settings()->whitelist()->_($rule_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

