<?php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

////////////////////////////////////////////////////
// Create a domain whitelabel. #
// POST /whitelabel/domains #

$request_body = json_decode('{
  "automatic_security": false, 
  "custom_spf": true, 
  "default": true, 
  "domain": "example.com", 
  "ips": [
    "192.168.1.1", 
    "192.168.1.2"
  ], 
  "subdomain": "news", 
  "username": "john@example.com"
}');
$response = $sg->client->whitelabel()->domains()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// List all domain whitelabels. #
// GET /whitelabel/domains #

$query_params = json_decode('{"username": "test_string", "domain": "test_string", "exclude_subusers": "true", "limit": 1, "offset": 1}');
$response = $sg->client->whitelabel()->domains()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Get the default domain whitelabel. #
// GET /whitelabel/domains/default #

$response = $sg->client->whitelabel()->domains()->default()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// List the domain whitelabel associated with the given user. #
// GET /whitelabel/domains/subuser #

$response = $sg->client->whitelabel()->domains()->subuser()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Disassociate a domain whitelabel from a given user. #
// DELETE /whitelabel/domains/subuser #

$response = $sg->client->whitelabel()->domains()->subuser()->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update a domain whitelabel. #
// PATCH /whitelabel/domains/{domain_id} #

$request_body = json_decode('{
  "custom_spf": true, 
  "default": false
}');
$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a domain whitelabel. #
// GET /whitelabel/domains/{domain_id} #

$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a domain whitelabel. #
// DELETE /whitelabel/domains/{domain_id} #

$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Associate a domain whitelabel with a given user. #
// POST /whitelabel/domains/{domain_id}/subuser #

$request_body = json_decode('{
  "username": "jane@example.com"
}');
$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->subuser()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Add an IP to a domain whitelabel. #
// POST /whitelabel/domains/{id}/ips #

$request_body = json_decode('{
  "ip": "192.168.0.1"
}');
$id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($id)->ips()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Remove an IP from a domain whitelabel. #
// DELETE /whitelabel/domains/{id}/ips/{ip} #

$id = "test_url_param";
$ip = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($id)->ips()->_($ip)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Validate a domain whitelabel. #
// POST /whitelabel/domains/{id}/validate #

$id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($id)->validate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Create an IP whitelabel #
// POST /whitelabel/ips #

$request_body = json_decode('{
  "domain": "example.com", 
  "ip": "192.168.1.1", 
  "subdomain": "email"
}');
$response = $sg->client->whitelabel()->ips()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all IP whitelabels #
// GET /whitelabel/ips #

$query_params = json_decode('{"ip": "test_string", "limit": 1, "offset": 1}');
$response = $sg->client->whitelabel()->ips()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve an IP whitelabel #
// GET /whitelabel/ips/{id} #

$id = "test_url_param";
$response = $sg->client->whitelabel()->ips()->_($id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete an IP whitelabel #
// DELETE /whitelabel/ips/{id} #

$id = "test_url_param";
$response = $sg->client->whitelabel()->ips()->_($id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Validate an IP whitelabel #
// POST /whitelabel/ips/{id}/validate #

$id = "test_url_param";
$response = $sg->client->whitelabel()->ips()->_($id)->validate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Create a Link Whitelabel #
// POST /whitelabel/links #

$request_body = json_decode('{
  "default": true, 
  "domain": "example.com", 
  "subdomain": "mail"
}');
$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->whitelabel()->links()->post($request_body, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve all link whitelabels #
// GET /whitelabel/links #

$query_params = json_decode('{"limit": 1}');
$response = $sg->client->whitelabel()->links()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a Default Link Whitelabel #
// GET /whitelabel/links/default #

$query_params = json_decode('{"domain": "test_string"}');
$response = $sg->client->whitelabel()->links()->default()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve Associated Link Whitelabel #
// GET /whitelabel/links/subuser #

$query_params = json_decode('{"username": "test_string"}');
$response = $sg->client->whitelabel()->links()->subuser()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Disassociate a Link Whitelabel #
// DELETE /whitelabel/links/subuser #

$query_params = json_decode('{"username": "test_string"}');
$response = $sg->client->whitelabel()->links()->subuser()->delete(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Update a Link Whitelabel #
// PATCH /whitelabel/links/{id} #

$request_body = json_decode('{
  "default": true
}');
$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Retrieve a Link Whitelabel #
// GET /whitelabel/links/{id} #

$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Delete a Link Whitelabel #
// DELETE /whitelabel/links/{id} #

$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Validate a Link Whitelabel #
// POST /whitelabel/links/{id}/validate #

$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->validate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();

////////////////////////////////////////////////////
// Associate a Link Whitelabel #
// POST /whitelabel/links/{link_id}/subuser #

$request_body = json_decode('{
  "username": "jane@example.com"
}');
$link_id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($link_id)->subuser()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();

