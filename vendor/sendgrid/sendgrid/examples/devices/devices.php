<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Retrieve email statistics by device type. #
// GET /devices/stats #

$query_params = json_decode('{"aggregated_by": "day", "limit": 1, "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": 1}');
$response = $sg->client->devices()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

