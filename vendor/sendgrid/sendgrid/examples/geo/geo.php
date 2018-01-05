<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Retrieve email statistics by country and state/province. #
// GET /geo/stats #

$query_params = json_decode('{"end_date": "2016-04-01", "country": "US", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01"}');
$response = $sg->client->geo()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

