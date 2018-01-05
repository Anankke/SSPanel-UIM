<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Create a Sender Identity #
// POST /senders #

$request_body = json_decode('{
  "address": "123 Elm St.", 
  "address_2": "Apt. 456", 
  "city": "Denver", 
  "country": "United States", 
  "from": {
    "email": "from@example.com", 
    "name": "Example INC"
  }, 
  "nickname": "My Sender ID", 
  "reply_to": {
    "email": "replyto@example.com", 
    "name": "Example INC"
  }, 
  "state": "Colorado", 
  "zip": "80202"
}');
$response = $sg->client->senders()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Get all Sender Identities #
// GET /senders #

$response = $sg->client->senders()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update a Sender Identity #
// PATCH /senders/{sender_id} #

$request_body = json_decode('{
  "address": "123 Elm St.", 
  "address_2": "Apt. 456", 
  "city": "Denver", 
  "country": "United States", 
  "from": {
    "email": "from@example.com", 
    "name": "Example INC"
  }, 
  "nickname": "My Sender ID", 
  "reply_to": {
    "email": "replyto@example.com", 
    "name": "Example INC"
  }, 
  "state": "Colorado", 
  "zip": "80202"
}');
$sender_id = "test_url_param";
$response = $sg->client->senders()->_($sender_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// View a Sender Identity #
// GET /senders/{sender_id} #

$sender_id = "test_url_param";
$response = $sg->client->senders()->_($sender_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a Sender Identity #
// DELETE /senders/{sender_id} #

$sender_id = "test_url_param";
$response = $sg->client->senders()->_($sender_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Resend Sender Identity Verification #
// POST /senders/{sender_id}/resend_verification #

$sender_id = "test_url_param";
$response = $sg->client->senders()->_($sender_id)->resend_verification()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

