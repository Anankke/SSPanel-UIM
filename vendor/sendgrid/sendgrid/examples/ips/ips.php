<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Retrieve all IP addresses #
// GET /ips #

$query_params = json_decode('{"subuser": "test_string", "ip": "test_string", "limit": 1, "exclude_whitelabels": "true", "offset": 1}');
$response = $sg->client->ips()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all assigned IPs #
// GET /ips/assigned #

$response = $sg->client->ips()->assigned()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Create an IP pool. #
// POST /ips/pools #

$request_body = json_decode('{
  "name": "marketing"
}');
$response = $sg->client->ips()->pools()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all IP pools. #
// GET /ips/pools #

$response = $sg->client->ips()->pools()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update an IP pools name. #
// PUT /ips/pools/{pool_name} #

$request_body = json_decode('{
  "name": "new_pool_name"
}');
$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all IPs in a specified pool. #
// GET /ips/pools/{pool_name} #

$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete an IP pool. #
// DELETE /ips/pools/{pool_name} #

$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Add an IP address to a pool #
// POST /ips/pools/{pool_name}/ips #

$request_body = json_decode('{
  "ip": "0.0.0.0"
}');
$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->ips()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Remove an IP address from a pool. #
// DELETE /ips/pools/{pool_name}/ips/{ip} #

$pool_name = "test_url_param";
$ip = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->ips()->_($ip)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Add an IP to warmup #
// POST /ips/warmup #

$request_body = json_decode('{
  "ip": "0.0.0.0"
}');
$response = $sg->client->ips()->warmup()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all IPs currently in warmup #
// GET /ips/warmup #

$response = $sg->client->ips()->warmup()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve warmup status for a specific IP address #
// GET /ips/warmup/{ip_address} #

$ip_address = "test_url_param";
$response = $sg->client->ips()->warmup()->_($ip_address)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Remove an IP from warmup #
// DELETE /ips/warmup/{ip_address} #

$ip_address = "test_url_param";
$response = $sg->client->ips()->warmup()->_($ip_address)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all IP pools an IP address belongs to #
// GET /ips/{ip_address} #

$ip_address = "test_url_param";
$response = $sg->client->ips()->_($ip_address)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

