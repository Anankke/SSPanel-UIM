<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Create a transactional template. #
// POST /templates #

$request_body = json_decode('{
  "name": "example_name"
}');
$response = $sg->client->templates()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all transactional templates. #
// GET /templates #

$response = $sg->client->templates()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Edit a transactional template. #
// PATCH /templates/{template_id} #

$request_body = json_decode('{
  "name": "new_example_name"
}');
$template_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a single transactional template. #
// GET /templates/{template_id} #

$template_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a template. #
// DELETE /templates/{template_id} #

$template_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Create a new transactional template version. #
// POST /templates/{template_id}/versions #

$request_body = json_decode('{
  "active": 1, 
  "html_content": "<%body%>", 
  "name": "example_version_name", 
  "plain_content": "<%body%>", 
  "subject": "<%subject%>", 
  "template_id": "ddb96bbc-9b92-425e-8979-99464621b543"
}');
$template_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Edit a transactional template version. #
// PATCH /templates/{template_id}/versions/{version_id} #

$request_body = json_decode('{
  "active": 1, 
  "html_content": "<%body%>", 
  "name": "updated_example_name", 
  "plain_content": "<%body%>", 
  "subject": "<%subject%>"
}');
$template_id = "test_url_param";
$version_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->_($version_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a specific transactional template version. #
// GET /templates/{template_id}/versions/{version_id} #

$template_id = "test_url_param";
$version_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->_($version_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a transactional template version. #
// DELETE /templates/{template_id}/versions/{version_id} #

$template_id = "test_url_param";
$version_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->_($version_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Activate a transactional template version. #
// POST /templates/{template_id}/versions/{version_id}/activate #

$template_id = "test_url_param";
$version_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->_($version_id)->activate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

