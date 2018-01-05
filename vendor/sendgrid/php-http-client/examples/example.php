<?php

// If running this outside of this context, use the following include and
// comment out the two includes below
// require __DIR__ . '/vendor/autoload.php';
include(dirname(__DIR__) . '/lib/Client.php');
// This gets the parent directory, for your current directory use getcwd()
$path_to_config = dirname(__DIR__);
$apiKey = getenv('SENDGRID_API_KEY');
$headers = ['Authorization: Bearer ' . $apiKey];
$client = new SendGrid\Client('https://api.sendgrid.com', $headers, '/v3');

// GET Collection
$query_params = ['limit' => 100, 'offset' => 0];
$request_headers = ['X-Mock: 200'];
$response = $client->api_keys()->get(null, $query_params, $request_headers);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

// POST
$request_body = [
    'name' => 'My PHP API Key',
    'scopes' => [
        'mail.send',
        'alerts.create',
        'alerts.read'
    ]
];
$response = $client->api_keys()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
$response_body = json_decode($response->body());
$api_key_id = $response_body->api_key_id;

// GET Single
$response = $client->version('/v3')->api_keys()->_($api_key_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

// PATCH
$request_body = [
    'name' => 'A New Hope'
];
$response = $client->api_keys()->_($api_key_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

// PUT
$request_body = [
    'name' => 'A New Hope',
    'scopes' => [
        'user.profile.read',
        'user.profile.update'
    ]
];
$response = $client->api_keys()->_($api_key_id)->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

// DELETE
$response = $client->api_keys()->_($api_key_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
