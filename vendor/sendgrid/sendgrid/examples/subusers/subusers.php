<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Create Subuser #
// POST /subusers #

$request_body = json_decode('{
  "email": "John@example.com", 
  "ips": [
    "1.1.1.1", 
    "2.2.2.2"
  ], 
  "password": "johns_password", 
  "username": "John@example.com"
}');
$response = $sg->client->subusers()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// List all Subusers #
// GET /subusers #

$query_params = json_decode('{"username": "test_string", "limit": 1, "offset": 1}');
$response = $sg->client->subusers()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve Subuser Reputations #
// GET /subusers/reputations #

$query_params = json_decode('{"usernames": "test_string"}');
$response = $sg->client->subusers()->reputations()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve email statistics for your subusers. #
// GET /subusers/stats #

$query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01", "subusers": "test_string"}');
$response = $sg->client->subusers()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve monthly stats for all subusers #
// GET /subusers/stats/monthly #

$query_params = json_decode('{"subuser": "test_string", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "date": "test_string", "sort_by_direction": "asc"}');
$response = $sg->client->subusers()->stats()->monthly()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
//  Retrieve the totals for each email statistic metric for all subusers. #
// GET /subusers/stats/sums #

$query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "start_date": "2016-01-01", "sort_by_direction": "asc"}');
$response = $sg->client->subusers()->stats()->sums()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Enable/disable a subuser #
// PATCH /subusers/{subuser_name} #

$request_body = json_decode('{
  "disabled": false
}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a subuser #
// DELETE /subusers/{subuser_name} #

$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update IPs assigned to a subuser #
// PUT /subusers/{subuser_name}/ips #

$request_body = json_decode('[
  "127.0.0.1"
]');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->ips()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update Monitor Settings for a subuser #
// PUT /subusers/{subuser_name}/monitor #

$request_body = json_decode('{
  "email": "example@example.com", 
  "frequency": 500
}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Create monitor settings #
// POST /subusers/{subuser_name}/monitor #

$request_body = json_decode('{
  "email": "example@example.com", 
  "frequency": 50000
}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve monitor settings for a subuser #
// GET /subusers/{subuser_name}/monitor #

$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete monitor settings #
// DELETE /subusers/{subuser_name}/monitor #

$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve the monthly email statistics for a single subuser #
// GET /subusers/{subuser_name}/stats/monthly #

$query_params = json_decode('{"date": "test_string", "sort_by_direction": "asc", "limit": 1, "sort_by_metric": "test_string", "offset": 1}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->stats()->monthly()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

