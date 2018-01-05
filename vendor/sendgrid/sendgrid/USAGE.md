This documentation is based on our [OAI specification](https://github.com/sendgrid/sendgrid-oai).

# INITIALIZATION

```php
// If you are using Composer
require 'vendor/autoload.php';


$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);
```

# Table of Contents

* [ACCESS SETTINGS](#access_settings)
* [ALERTS](#alerts)
* [API KEYS](#api_keys)
* [ASM](#asm)
* [BROWSERS](#browsers)
* [CAMPAIGNS](#campaigns)
* [CATEGORIES](#categories)
* [CLIENTS](#clients)
* [CONTACTDB](#contactdb)
* [DEVICES](#devices)
* [GEO](#geo)
* [IPS](#ips)
* [MAIL](#mail)
* [MAIL SETTINGS](#mail_settings)
* [MAILBOX PROVIDERS](#mailbox_providers)
* [PARTNER SETTINGS](#partner_settings)
* [SCOPES](#scopes)
* [SENDERS](#senders)
* [STATS](#stats)
* [SUBUSERS](#subusers)
* [SUPPRESSION](#suppression)
* [TEMPLATES](#templates)
* [TRACKING SETTINGS](#tracking_settings)
* [USER](#user)
* [WHITELABEL](#whitelabel)


<a name="access_settings"></a>
# ACCESS SETTINGS

## Retrieve all recent access attempts

**This endpoint allows you to retrieve a list of all of the IP addresses that recently attempted to access your account either through the User Interface or the API.**

IP Access Management allows you to control which IP addresses can be used to access your account, either through the User Interface or the API. There is no limit to the number of IP addresses that you can add to your whitelist. It is possible to remove your own IP address from the whitelist, thus preventing yourself from accessing your account.

For more information, please see our [User Guide](http://sendgrid.com/docs/User_Guide/Settings/ip_access_management.html).

### GET /access_settings/activity


```php
$query_params = json_decode('{"limit": 1}');
$response = $sg->client->access_settings()->activity()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add one or more IPs to the whitelist

**This endpoint allows you to add one or more IP addresses to your IP whitelist.**

When adding an IP to your whitelist, include the IP address in an array. You can whitelist one IP at a time, or you can whitelist multiple IPs at once.

IP Access Management allows you to control which IP addresses can be used to access your account, either through the User Interface or the API. There is no limit to the number of IP addresses that you can add to your whitelist. It is possible to remove your own IP address from the whitelist, thus preventing yourself from accessing your account.

For more information, please see our [User Guide](http://sendgrid.com/docs/User_Guide/Settings/ip_access_management.html).

### POST /access_settings/whitelist


```php
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
```
## Retrieve a list of currently whitelisted IPs

**This endpoint allows you to retrieve a list of IP addresses that are currently whitelisted.**

IP Access Management allows you to control which IP addresses can be used to access your account, either through the User Interface or the API. There is no limit to the number of IP addresses that you can add to your whitelist. It is possible to remove your own IP address from the whitelist, thus preventing yourself from accessing your account.

For more information, please see our [User Guide](http://sendgrid.com/docs/User_Guide/Settings/ip_access_management.html).

### GET /access_settings/whitelist


```php
$response = $sg->client->access_settings()->whitelist()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Remove one or more IPs from the whitelist

**This endpoint allows you to remove one or more IPs from your IP whitelist.**

You can remove one IP at a time, or you can remove multiple IP addresses.

IP Access Management allows you to control which IP addresses can be used to access your account, either through the User Interface or the API. There is no limit to the number of IP addresses that you can add to your whitelist. It is possible to remove your own IP address from the whitelist, thus preventing yourself from accessing your account.

For more information, please see our [User Guide](http://sendgrid.com/docs/User_Guide/Settings/ip_access_management.html).

### DELETE /access_settings/whitelist


```php
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
```
## Retrieve a specific whitelisted IP

**This endpoint allows you to retreive a specific IP address that has been whitelisted.**

You must include the ID for the specific IP address you want to retrieve in your call.

IP Access Management allows you to control which IP addresses can be used to access your account, either through the User Interface or the API. There is no limit to the number of IP addresses that you can add to your whitelist. It is possible to remove your own IP address from the whitelist, thus preventing yourself from accessing your account.

For more information, please see our [User Guide](http://sendgrid.com/docs/User_Guide/Settings/ip_access_management.html).

### GET /access_settings/whitelist/{rule_id}


```php
$rule_id = "test_url_param";
$response = $sg->client->access_settings()->whitelist()->_($rule_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Remove a specific IP from the whitelist

**This endpoint allows you to remove a specific IP address from your IP whitelist.**

When removing a specific IP address from your whitelist, you must include the ID in your call.

IP Access Management allows you to control which IP addresses can be used to access your account, either through the User Interface or the API. There is no limit to the number of IP addresses that you can add to your whitelist. It is possible to remove your own IP address from the whitelist, thus preventing yourself from accessing your account.

For more information, please see our [User Guide](http://sendgrid.com/docs/User_Guide/Settings/ip_access_management.html).

### DELETE /access_settings/whitelist/{rule_id}


```php
$rule_id = "test_url_param";
$response = $sg->client->access_settings()->whitelist()->_($rule_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="alerts"></a>
# ALERTS

## Create a new Alert

**This endpoint allows you to create a new alert.**

Alerts allow you to specify an email address to receive notifications regarding your email usage or statistics.
* Usage alerts allow you to set the threshold at which an alert will be sent.
* Stats notifications allow you to set how frequently you would like to receive email statistics reports. For example, "daily", "weekly", or "monthly".

For more information about alerts, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/alerts.html).

### POST /alerts


```php
$request_body = json_decode('{
  "email_to": "example@example.com",
  "frequency": "daily",
  "type": "stats_notification"
}');
$response = $sg->client->alerts()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all alerts

**This endpoint allows you to retieve all of your alerts.**

Alerts allow you to specify an email address to receive notifications regarding your email usage or statistics.
* Usage alerts allow you to set the threshold at which an alert will be sent.
* Stats notifications allow you to set how frequently you would like to receive email statistics reports. For example, "daily", "weekly", or "monthly".

For more information about alerts, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/alerts.html).

### GET /alerts


```php
$response = $sg->client->alerts()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update an alert

**This endpoint allows you to update an alert.**

Alerts allow you to specify an email address to receive notifications regarding your email usage or statistics.
* Usage alerts allow you to set the threshold at which an alert will be sent.
* Stats notifications allow you to set how frequently you would like to receive email statistics reports. For example, "daily", "weekly", or "monthly".

For more information about alerts, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/alerts.html).

### PATCH /alerts/{alert_id}


```php
$request_body = json_decode('{
  "email_to": "example@example.com"
}');
$alert_id = "test_url_param";
$response = $sg->client->alerts()->_($alert_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a specific alert

**This endpoint allows you to retrieve a specific alert.**

Alerts allow you to specify an email address to receive notifications regarding your email usage or statistics.
* Usage alerts allow you to set the threshold at which an alert will be sent.
* Stats notifications allow you to set how frequently you would like to receive email statistics reports. For example, "daily", "weekly", or "monthly".

For more information about alerts, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/alerts.html).

### GET /alerts/{alert_id}


```php
$alert_id = "test_url_param";
$response = $sg->client->alerts()->_($alert_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete an alert

**This endpoint allows you to delete an alert.**

Alerts allow you to specify an email address to receive notifications regarding your email usage or statistics.
* Usage alerts allow you to set the threshold at which an alert will be sent.
* Stats notifications allow you to set how frequently you would like to receive email statistics reports. For example, "daily", "weekly", or "monthly".

For more information about alerts, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/alerts.html).

### DELETE /alerts/{alert_id}


```php
$alert_id = "test_url_param";
$response = $sg->client->alerts()->_($alert_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="api_keys"></a>
# API KEYS

## Create API keys

**This enpoint allows you to create a new random API Key for the user.**

A JSON request body containing a "name" property is required. If number of maximum keys is reached, HTTP 403 will be returned.

There is a limit of 100 API Keys on your account.

The API Keys feature allows customers to be able to generate an API Key credential which can be used for authentication with the SendGrid v3 Web API or the [Mail API Endpoint](https://sendgrid.com/docs/API_Reference/Web_API/mail.html).

See the [API Key Permissions List](https://sendgrid.com/docs/API_Reference/Web_API_v3/API_Keys/api_key_permissions_list.html) for a list of all available scopes.

### POST /api_keys


```php
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
```
## Retrieve all API Keys belonging to the authenticated user

**This endpoint allows you to retrieve all API Keys that belong to the authenticated user.**

The API Keys feature allows customers to be able to generate an API Key credential which can be used for authentication with the SendGrid v3 Web API or the [Mail API Endpoint](https://sendgrid.com/docs/API_Reference/Web_API/mail.html).

### GET /api_keys


```php
$query_params = json_decode('{"limit": 1}');
$response = $sg->client->api_keys()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update the name & scopes of an API Key

**This endpoint allows you to update the name and scopes of a given API key.**

A JSON request body with a "name" property is required.
Most provide the list of all the scopes an api key should have.

The API Keys feature allows customers to be able to generate an API Key credential which can be used for authentication with the SendGrid v3 Web API or the [Mail API Endpoint](https://sendgrid.com/docs/API_Reference/Web_API/mail.html).


### PUT /api_keys/{api_key_id}


```php
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
```
## Update API keys

**This endpoint allows you to update the name of an existing API Key.**

A JSON request body with a "name" property is required.

The API Keys feature allows customers to be able to generate an API Key credential which can be used for authentication with the SendGrid v3 Web API or the [Mail API Endpoint](https://sendgrid.com/docs/API_Reference/Web_API/mail.html).

## URI Parameters

| URI Parameter   | Type  | Required?  | Description  |
|---|---|---|---|
|api_key_id |string | required | The ID of the API Key you are updating.|

### PATCH /api_keys/{api_key_id}


```php
$request_body = json_decode('{
  "name": "A New Hope"
}');
$api_key_id = "test_url_param";
$response = $sg->client->api_keys()->_($api_key_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve an existing API Key

**This endpoint allows you to retrieve a single api key.**

If the API Key ID does not exist an HTTP 404 will be returned.

### GET /api_keys/{api_key_id}


```php
$api_key_id = "test_url_param";
$response = $sg->client->api_keys()->_($api_key_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete API keys

**This endpoint allows you to revoke an existing API Key**

Authentications using this API Key will fail after this request is made, with some small propogation delay.If the API Key ID does not exist an HTTP 404 will be returned.

The API Keys feature allows customers to be able to generate an API Key credential which can be used for authentication with the SendGrid v3 Web API or the [Mail API Endpoint](https://sendgrid.com/docs/API_Reference/Web_API/mail.html).

## URI Parameters

| URI Parameter   | Type  | Required?  | Description  |
|---|---|---|---|
|api_key_id |string | required | The ID of the API Key you are deleting.|

### DELETE /api_keys/{api_key_id}


```php
$api_key_id = "test_url_param";
$response = $sg->client->api_keys()->_($api_key_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="asm"></a>
# ASM

## Create a new suppression group

**This endpoint allows you to create a new suppression group.**

Suppression groups, or unsubscribe groups, are specific types or categories of email that you would like your recipients to be able to unsubscribe from. For example: Daily Newsletters, Invoices, System Alerts.

The **name** and **description** of the unsubscribe group will be visible by recipients when they are managing their subscriptions.

Each user can create up to 25 different suppression groups.

### POST /asm/groups


```php
$request_body = json_decode('{
  "description": "Suggestions for products our users might like.",
  "is_default": true,
  "name": "Product Suggestions"
}');
$response = $sg->client->asm()->groups()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve information about multiple suppression groups

**This endpoint allows you to retrieve information about multiple suppression groups.**

This endpoint will return information for each group ID that you include in your request. To add a group ID to your request, simply append `&id=` followed by the group ID.

Suppressions are a list of email addresses that will not receive content sent under a given [group](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html).

Suppression groups, or [unsubscribe groups](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html), allow you to label a category of content that you regularly send. This gives your recipients the ability to opt out of a specific set of your email. For example, you might define a group for your transactional email, and one for your marketing email so that your users can continue recieving your transactional email witout having to receive your marketing content.

### GET /asm/groups


```php
$query_params = json_decode('{"id": 1}');
$response = $sg->client->asm()->groups()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a suppression group.

**This endpoint allows you to update or change a suppression group.**

Suppression groups, or unsubscribe groups, are specific types or categories of email that you would like your recipients to be able to unsubscribe from. For example: Daily Newsletters, Invoices, System Alerts.

The **name** and **description** of the unsubscribe group will be visible by recipients when they are managing their subscriptions.

Each user can create up to 25 different suppression groups.

### PATCH /asm/groups/{group_id}


```php
$request_body = json_decode('{
  "description": "Suggestions for items our users might like.",
  "id": 103,
  "name": "Item Suggestions"
}');
$group_id = "test_url_param";
$response = $sg->client->asm()->groups()->_($group_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Get information on a single suppression group.

**This endpoint allows you to retrieve a single suppression group.**

Suppression groups, or unsubscribe groups, are specific types or categories of email that you would like your recipients to be able to unsubscribe from. For example: Daily Newsletters, Invoices, System Alerts.

The **name** and **description** of the unsubscribe group will be visible by recipients when they are managing their subscriptions.

Each user can create up to 25 different suppression groups.

### GET /asm/groups/{group_id}


```php
$group_id = "test_url_param";
$response = $sg->client->asm()->groups()->_($group_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a suppression group.

**This endpoint allows you to delete a suppression group.**

You can only delete groups that have not been attached to sent mail in the last 60 days. If a recipient uses the "one-click unsubscribe" option on an email associated with a deleted group, that recipient will be added to the global suppression list.

Suppression groups, or unsubscribe groups, are specific types or categories of email that you would like your recipients to be able to unsubscribe from. For example: Daily Newsletters, Invoices, System Alerts.

The **name** and **description** of the unsubscribe group will be visible by recipients when they are managing their subscriptions.

Each user can create up to 25 different suppression groups.

### DELETE /asm/groups/{group_id}


```php
$group_id = "test_url_param";
$response = $sg->client->asm()->groups()->_($group_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add suppressions to a suppression group

**This endpoint allows you to add email addresses to an unsubscribe group.**

If you attempt to add suppressions to a group that has been deleted or does not exist, the suppressions will be added to the global suppressions list.

Suppressions are recipient email addresses that are added to [unsubscribe groups](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html). Once a recipient's address is on the suppressions list for an unsubscribe group, they will not receive any emails that are tagged with that unsubscribe group.

### POST /asm/groups/{group_id}/suppressions


```php
$request_body = json_decode('{
  "recipient_emails": [
    "test1@example.com",
    "test2@example.com"
  ]
}');
$group_id = "test_url_param";
$response = $sg->client->asm()->groups()->_($group_id)->suppressions()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all suppressions for a suppression group

**This endpoint allows you to retrieve all suppressed email addresses belonging to the given group.**

Suppressions are recipient email addresses that are added to [unsubscribe groups](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html). Once a recipient's address is on the suppressions list for an unsubscribe group, they will not receive any emails that are tagged with that unsubscribe group.

### GET /asm/groups/{group_id}/suppressions


```php
$group_id = "test_url_param";
$response = $sg->client->asm()->groups()->_($group_id)->suppressions()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Search for suppressions within a group

**This endpoint allows you to search a suppression group for multiple suppressions.**

When given a list of email addresses and a group ID, this endpoint will return only the email addresses that have been unsubscribed from the given group.

Suppressions are a list of email addresses that will not receive content sent under a given [group](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html).

### POST /asm/groups/{group_id}/suppressions/search


```php
$request_body = json_decode('{
  "recipient_emails": [
    "exists1@example.com",
    "exists2@example.com",
    "doesnotexists@example.com"
  ]
}');
$group_id = "test_url_param";
$response = $sg->client->asm()->groups()->_($group_id)->suppressions()->search()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a suppression from a suppression group

**This endpoint allows you to remove a suppressed email address from the given suppression group.**

Suppressions are recipient email addresses that are added to [unsubscribe groups](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html). Once a recipient's address is on the suppressions list for an unsubscribe group, they will not receive any emails that are tagged with that unsubscribe group.

### DELETE /asm/groups/{group_id}/suppressions/{email}


```php
$group_id = "test_url_param";
$email = "test_url_param";
$response = $sg->client->asm()->groups()->_($group_id)->suppressions()->_($email)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all suppressions

**This endpoint allows you to retrieve a list of all suppressions.**

Suppressions are a list of email addresses that will not receive content sent under a given [group](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html).

### GET /asm/suppressions


```php
$response = $sg->client->asm()->suppressions()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add recipient addresses to the global suppression group.

**This endpoint allows you to add one or more email addresses to the global suppressions group.**

A global suppression (or global unsubscribe) is an email address of a recipient who does not want to receive any of your messages. A globally suppressed recipient will be removed from any email you send. For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/global_unsubscribes.html).

### POST /asm/suppressions/global


```php
$request_body = json_decode('{
  "recipient_emails": [
    "test1@example.com",
    "test2@example.com"
  ]
}');
$response = $sg->client->asm()->suppressions()->global()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a Global Suppression

**This endpoint allows you to retrieve a global suppression. You can also use this endpoint to confirm if an email address is already globally suppresed.**

If the email address you include in the URL path parameter `{email}` is alreayd globally suppressed, the response will include that email address. If the address you enter for `{email}` is not globally suppressed, an empty JSON object `{}` will be returned.

A global suppression (or global unsubscribe) is an email address of a recipient who does not want to receive any of your messages. A globally suppressed recipient will be removed from any email you send. For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/global_unsubscribes.html).

### GET /asm/suppressions/global/{email}


```php
$email = "test_url_param";
$response = $sg->client->asm()->suppressions()->global()->_($email)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a Global Suppression

**This endpoint allows you to remove an email address from the global suppressions group.**

A global suppression (or global unsubscribe) is an email address of a recipient who does not want to receive any of your messages. A globally suppressed recipient will be removed from any email you send. For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/global_unsubscribes.html).

### DELETE /asm/suppressions/global/{email}


```php
$email = "test_url_param";
$response = $sg->client->asm()->suppressions()->global()->_($email)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all suppression groups for an email address

**This endpoint returns the list of all groups that the given email address has been unsubscribed from.**

Suppressions are a list of email addresses that will not receive content sent under a given [group](https://sendgrid.com/docs/API_Reference/Web_API_v3/Suppression_Management/groups.html).

### GET /asm/suppressions/{email}


```php
$email = "test_url_param";
$response = $sg->client->asm()->suppressions()->_($email)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="browsers"></a>
# BROWSERS

## Retrieve email statistics by browser.

**This endpoint allows you to retrieve your email statistics segmented by browser type.**

**We only store up to 7 days of email activity in our database.** By default, 500 items will be returned per request via the Advanced Stats API endpoints.

Advanced Stats provide a more in-depth view of your email statistics and the actions taken by your recipients. You can segment these statistics by geographic location, device type, client type, browser, and mailbox provider. For more information about statistics, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/index.html).

### GET /browsers/stats


```php
$query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "browsers": "test_string", "limit": "test_string", "offset": "test_string", "start_date": "2016-01-01"}');
$response = $sg->client->browsers()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="campaigns"></a>
# CAMPAIGNS

## Create a Campaign

**This endpoint allows you to create a campaign.**

Our Marketing Campaigns API lets you create, manage, send, and schedule campaigns.

Note: In order to send or schedule the campaign, you will be required to provide a subject, sender ID, content (we suggest both html and plain text), and at least one list or segment ID. This information is not required when you create a campaign.

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### POST /campaigns


```php
$request_body = json_decode('{
  "categories": [
    "spring line"
  ],
  "custom_unsubscribe_url": "",
  "html_content": "<html><head><title></title></head><body><p>Check out our spring line!</p></body></html>",
  "ip_pool": "marketing",
  "list_ids": [
    110,
    124
  ],
  "plain_content": "Check out our spring line!",
  "segment_ids": [
    110
  ],
  "sender_id": 124451,
  "subject": "New Products for Spring!",
  "suppression_group_id": 42,
  "title": "March Newsletter"
}');
$response = $sg->client->campaigns()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all Campaigns

**This endpoint allows you to retrieve a list of all of your campaigns.**

Returns campaigns in reverse order they were created (newest first).

Returns an empty array if no campaigns exist.

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### GET /campaigns


```php
$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->campaigns()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a Campaign

Update a campaign. This is especially useful if you only set up the campaign using POST /campaigns, but didn't set many of the parameters.

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### PATCH /campaigns/{campaign_id}


```php
$request_body = json_decode('{
  "categories": [
    "summer line"
  ],
  "html_content": "<html><head><title></title></head><body><p>Check out our summer line!</p></body></html>",
  "plain_content": "Check out our summer line!",
  "subject": "New Products for Summer!",
  "title": "May Newsletter"
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a single campaign

**This endpoint allows you to retrieve a specific campaign.**

Our Marketing Campaigns API lets you create, manage, send, and schedule campaigns.

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### GET /campaigns/{campaign_id}


```php
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a Campaign

**This endpoint allows you to delete a specific campaign.**

Our Marketing Campaigns API lets you create, manage, send, and schedule campaigns.

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### DELETE /campaigns/{campaign_id}


```php
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a Scheduled Campaign

**This endpoint allows to you change the scheduled time and date for a campaign to be sent.**

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### PATCH /campaigns/{campaign_id}/schedules


```php
$request_body = json_decode('{
  "send_at": 1489451436
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Schedule a Campaign

**This endpoint allows you to schedule a specific date and time for your campaign to be sent.**

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### POST /campaigns/{campaign_id}/schedules


```php
$request_body = json_decode('{
  "send_at": 1489771528
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## View Scheduled Time of a Campaign

**This endpoint allows you to retrieve the date and time that the given campaign has been scheduled to be sent.**

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### GET /campaigns/{campaign_id}/schedules


```php
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Unschedule a Scheduled Campaign

**This endpoint allows you to unschedule a campaign that has already been scheduled to be sent.**

A successful unschedule will return a 204.
If the specified campaign is in the process of being sent, the only option is to cancel (a different method).

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### DELETE /campaigns/{campaign_id}/schedules


```php
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Send a Campaign

**This endpoint allows you to immediately send a campaign at the time you make the API call.**

Normally a POST would have a request body, but since this endpoint is telling us to send a resource that is already created, a request body is not needed.

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### POST /campaigns/{campaign_id}/schedules/now


```php
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->now()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Send a Test Campaign

**This endpoint allows you to send a test campaign.**

To send to multiple addresses, use an array for the JSON "to" value ["one@address","two@address"]

For more information:

* [User Guide > Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html)

### POST /campaigns/{campaign_id}/schedules/test


```php
$request_body = json_decode('{
  "to": "your.email@example.com"
}');
$campaign_id = "test_url_param";
$response = $sg->client->campaigns()->_($campaign_id)->schedules()->test()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="categories"></a>
# CATEGORIES

## Retrieve all categories

**This endpoint allows you to retrieve a list of all of your categories.**

Categories can help organize your email analytics by enabling you to tag emails by type or broad topic. You can define your own custom categories. For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/categories.html).

### GET /categories


```php
$query_params = json_decode('{"category": "test_string", "limit": 1, "offset": 1}');
$response = $sg->client->categories()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve Email Statistics for Categories

**This endpoint allows you to retrieve all of your email statistics for each of your categories.**

If you do not define any query parameters, this endpoint will return a sum for each category in groups of 10.

Categories allow you to group your emails together according to broad topics that you define. For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/categories.html).

### GET /categories/stats


```php
$query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01", "categories": "test_string"}');
$response = $sg->client->categories()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve sums of email stats for each category [Needs: Stats object defined, has category ID?]

**This endpoint allows you to retrieve the total sum of each email statistic for every category over the given date range.**

If you do not define any query parameters, this endpoint will return a sum for each category in groups of 10.

Categories allow you to group your emails together according to broad topics that you define. For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/categories.html).

### GET /categories/stats/sums


```php
$query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "start_date": "2016-01-01", "sort_by_direction": "asc"}');
$response = $sg->client->categories()->stats()->sums()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="clients"></a>
# CLIENTS

## Retrieve email statistics by client type.

**This endpoint allows you to retrieve your email statistics segmented by client type.**

**We only store up to 7 days of email activity in our database.** By default, 500 items will be returned per request via the Advanced Stats API endpoints.

Advanced Stats provide a more in-depth view of your email statistics and the actions taken by your recipients. You can segment these statistics by geographic location, device type, client type, browser, and mailbox provider. For more information about statistics, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/index.html).

### GET /clients/stats


```php
$query_params = json_decode('{"aggregated_by": "day", "start_date": "2016-01-01", "end_date": "2016-04-01"}');
$response = $sg->client->clients()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve stats by a specific client type.

**This endpoint allows you to retrieve your email statistics segmented by a specific client type.**

**We only store up to 7 days of email activity in our database.** By default, 500 items will be returned per request via the Advanced Stats API endpoints.

## Available Client Types
- phone
- tablet
- webmail
- desktop

Advanced Stats provide a more in-depth view of your email statistics and the actions taken by your recipients. You can segment these statistics by geographic location, device type, client type, browser, and mailbox provider. For more information about statistics, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/index.html).

### GET /clients/{client_type}/stats


```php
$query_params = json_decode('{"aggregated_by": "day", "start_date": "2016-01-01", "end_date": "2016-04-01"}');
$client_type = "test_url_param";
$response = $sg->client->clients()->_($client_type)->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="contactdb"></a>
# CONTACTDB

## Create a Custom Field

**This endpoint allows you to create a custom field.**

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### POST /contactdb/custom_fields


```php
$request_body = json_decode('{
  "name": "pet",
  "type": "text"
}');
$response = $sg->client->contactdb()->custom_fields()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all custom fields

**This endpoint allows you to retrieve all custom fields.**

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### GET /contactdb/custom_fields


```php
$response = $sg->client->contactdb()->custom_fields()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a Custom Field

**This endpoint allows you to retrieve a custom field by ID.**

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### GET /contactdb/custom_fields/{custom_field_id}


```php
$custom_field_id = "test_url_param";
$response = $sg->client->contactdb()->custom_fields()->_($custom_field_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a Custom Field

**This endpoint allows you to delete a custom field by ID.**

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### DELETE /contactdb/custom_fields/{custom_field_id}


```php
$custom_field_id = "test_url_param";
$response = $sg->client->contactdb()->custom_fields()->_($custom_field_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create a List

**This endpoint allows you to create a list for your recipients.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### POST /contactdb/lists


```php
$request_body = json_decode('{
  "name": "your list name"
}');
$response = $sg->client->contactdb()->lists()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all lists

**This endpoint allows you to retrieve all of your recipient lists. If you don't have any lists, an empty array will be returned.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### GET /contactdb/lists


```php
$response = $sg->client->contactdb()->lists()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete Multiple lists

**This endpoint allows you to delete multiple recipient lists.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### DELETE /contactdb/lists


```php
$request_body = json_decode('[
  1,
  2,
  3,
  4
]');
$response = $sg->client->contactdb()->lists()->delete($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a List

**This endpoint allows you to update the name of one of your recipient lists.**


The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### PATCH /contactdb/lists/{list_id}


```php
$request_body = json_decode('{
  "name": "newlistname"
}');
$query_params = json_decode('{"list_id": 1}');
$list_id = "test_url_param";
$response = $sg->client->contactdb()->lists()->_($list_id)->patch($request_body, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a single list

This endpoint allows you to retrieve a single recipient list.

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### GET /contactdb/lists/{list_id}


```php
$query_params = json_decode('{"list_id": 1}');
$list_id = "test_url_param";
$response = $sg->client->contactdb()->lists()->_($list_id)->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a List

**This endpoint allows you to delete a specific recipient list with the given ID.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### DELETE /contactdb/lists/{list_id}


```php
$query_params = json_decode('{"delete_contacts": "true"}');
$list_id = "test_url_param";
$response = $sg->client->contactdb()->lists()->_($list_id)->delete(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add Multiple Recipients to a List

**This endpoint allows you to add multiple recipients to a list.**

Adds existing recipients to a list, passing in the recipient IDs to add. Recipient IDs should be passed exactly as they are returned from recipient endpoints.

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### POST /contactdb/lists/{list_id}/recipients


```php
$request_body = json_decode('[
  "recipient_id1",
  "recipient_id2"
]');
$list_id = "test_url_param";
$response = $sg->client->contactdb()->lists()->_($list_id)->recipients()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all recipients on a List

**This endpoint allows you to retrieve all recipients on the list with the given ID.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### GET /contactdb/lists/{list_id}/recipients


```php
$query_params = json_decode('{"page": 1, "page_size": 1, "list_id": 1}');
$list_id = "test_url_param";
$response = $sg->client->contactdb()->lists()->_($list_id)->recipients()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add a Single Recipient to a List

**This endpoint allows you to add a single recipient to a list.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### POST /contactdb/lists/{list_id}/recipients/{recipient_id}


```php
$list_id = "test_url_param";
$recipient_id = "test_url_param";
$response = $sg->client->contactdb()->lists()->_($list_id)->recipients()->_($recipient_id)->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a Single Recipient from a Single List

**This endpoint allows you to delete a single recipient from a list.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### DELETE /contactdb/lists/{list_id}/recipients/{recipient_id}


```php
$query_params = json_decode('{"recipient_id": 1, "list_id": 1}');
$list_id = "test_url_param";
$recipient_id = "test_url_param";
$response = $sg->client->contactdb()->lists()->_($list_id)->recipients()->_($recipient_id)->delete(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Recipient

**This endpoint allows you to update one or more recipients.**

The body of an API call to this endpoint must include an array of one or more recipient objects.

It is of note that you can add custom field data as parameters on recipient objects. We have provided an example using some of the default custom fields SendGrid provides.

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### PATCH /contactdb/recipients


```php
$request_body = json_decode('[
  {
    "email": "jones@example.com",
    "first_name": "Guy",
    "last_name": "Jones"
  }
]');
$response = $sg->client->contactdb()->recipients()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add recipients

**This endpoint allows you to add a Marketing Campaigns recipient.**

It is of note that you can add custom field data as a parameter on this endpoint. We have provided an example using some of the default custom fields SendGrid provides.

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### POST /contactdb/recipients


```php
$request_body = json_decode('[
  {
    "age": 25,
    "email": "example@example.com",
    "first_name": "",
    "last_name": "User"
  },
  {
    "age": 25,
    "email": "example2@example.com",
    "first_name": "Example",
    "last_name": "User"
  }
]');
$response = $sg->client->contactdb()->recipients()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve recipients

**This endpoint allows you to retrieve all of your Marketing Campaigns recipients.**

Batch deletion of a page makes it possible to receive an empty page of recipients before reaching the end of
the list of recipients. To avoid this issue; iterate over pages until a 404 is retrieved.

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### GET /contactdb/recipients


```php
$query_params = json_decode('{"page": 1, "page_size": 1}');
$response = $sg->client->contactdb()->recipients()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete Recipient

**This endpoint allows you to deletes one or more recipients.**

The body of an API call to this endpoint must include an array of recipient IDs of the recipients you want to delete.

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### DELETE /contactdb/recipients


```php
$request_body = json_decode('[
  "recipient_id1",
  "recipient_id2"
]');
$response = $sg->client->contactdb()->recipients()->delete($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve the count of billable recipients

**This endpoint allows you to retrieve the number of Marketing Campaigns recipients that you will be billed for.**

You are billed for marketing campaigns based on the highest number of recipients you have had in your account at one time. This endpoint will allow you to know the current billable count value.

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### GET /contactdb/recipients/billable_count


```php
$response = $sg->client->contactdb()->recipients()->billable_count()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a Count of Recipients

**This endpoint allows you to retrieve the total number of Marketing Campaigns recipients.**

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### GET /contactdb/recipients/count


```php
$response = $sg->client->contactdb()->recipients()->count()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve recipients matching search criteria

**This endpoint allows you to perform a search on all of your Marketing Campaigns recipients.**

field_name:

* is a variable that is substituted for your actual custom field name from your recipient.
* Text fields must be url-encoded. Date fields are searchable only by unix timestamp (e.g. 2/2/2015 becomes 1422835200)
* If field_name is a 'reserved' date field, such as created_at or updated_at, the system will internally convert
your epoch time to a date range encompassing the entire day. For example, an epoch time of 1422835600 converts to
Mon, 02 Feb 2015 00:06:40 GMT, but internally the system will search from Mon, 02 Feb 2015 00:00:00 GMT through
Mon, 02 Feb 2015 23:59:59 GMT.

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### GET /contactdb/recipients/search


```php
$query_params = json_decode('{"{field_name}": "test_string"}');
$response = $sg->client->contactdb()->recipients()->search()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a single recipient

**This endpoint allows you to retrieve a single recipient by ID from your contact database.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### GET /contactdb/recipients/{recipient_id}


```php
$recipient_id = "test_url_param";
$response = $sg->client->contactdb()->recipients()->_($recipient_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a Recipient

**This endpoint allows you to delete a single recipient with the given ID from your contact database.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### DELETE /contactdb/recipients/{recipient_id}


```php
$recipient_id = "test_url_param";
$response = $sg->client->contactdb()->recipients()->_($recipient_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve the lists that a recipient is on

**This endpoint allows you to retrieve the lists that a given recipient belongs to.**

Each recipient can be on many lists. This endpoint gives you all of the lists that any one recipient has been added to.

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

### GET /contactdb/recipients/{recipient_id}/lists


```php
$recipient_id = "test_url_param";
$response = $sg->client->contactdb()->recipients()->_($recipient_id)->lists()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve reserved fields

**This endpoint allows you to list all fields that are reserved and can't be used for custom field names.**

The contactdb is a database of your contacts for [SendGrid Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html).

### GET /contactdb/reserved_fields


```php
$response = $sg->client->contactdb()->reserved_fields()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create a Segment

**This endpoint allows you to create a segment.**

All recipients in your contactdb will be added or removed automatically depending on whether they match the criteria for this segment.

List Id:

* Send this to segment from an existing list
* Don't send this in order to segment from your entire contactdb.

Valid operators for create and update depend on the type of the field you are segmenting:

* **Dates:** "eq", "ne", "lt" (before), "gt" (after)
* **Text:** "contains", "eq" (is - matches the full field), "ne" (is not - matches any field where the entire field is not the condition value)
* **Numbers:** "eq", "lt", "gt"
* **Email Clicks and Opens:** "eq" (opened), "ne" (not opened)

Segment conditions using "eq" or "ne" for email clicks and opens should provide a "field" of either *clicks.campaign_identifier* or *opens.campaign_identifier*. The condition value should be a string containing the id of a completed campaign.

Segments may contain multiple condtions, joined by an "and" or "or" in the "and_or" field. The first condition in the conditions list must have an empty "and_or", and subsequent conditions must all specify an "and_or".

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

For more information about segments in Marketing Campaigns, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/lists.html#-Create-a-Segment).

### POST /contactdb/segments


```php
$request_body = json_decode('{
  "conditions": [
    {
      "and_or": "",
      "field": "last_name",
      "operator": "eq",
      "value": "Miller"
    },
    {
      "and_or": "and",
      "field": "last_clicked",
      "operator": "gt",
      "value": "01/02/2015"
    },
    {
      "and_or": "or",
      "field": "clicks.campaign_identifier",
      "operator": "eq",
      "value": "513"
    }
  ],
  "list_id": 4,
  "name": "Last Name Miller"
}');
$response = $sg->client->contactdb()->segments()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all segments

**This endpoint allows you to retrieve all of your segments.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

For more information about segments in Marketing Campaigns, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/lists.html#-Create-a-Segment).

### GET /contactdb/segments


```php
$response = $sg->client->contactdb()->segments()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a segment

**This endpoint allows you to update a segment.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

For more information about segments in Marketing Campaigns, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/lists.html#-Create-a-Segment).

### PATCH /contactdb/segments/{segment_id}


```php
$request_body = json_decode('{
  "conditions": [
    {
      "and_or": "",
      "field": "last_name",
      "operator": "eq",
      "value": "Miller"
    }
  ],
  "list_id": 5,
  "name": "The Millers"
}');
$query_params = json_decode('{"segment_id": "test_string"}');
$segment_id = "test_url_param";
$response = $sg->client->contactdb()->segments()->_($segment_id)->patch($request_body, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a segment

**This endpoint allows you to retrieve a single segment with the given ID.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

For more information about segments in Marketing Campaigns, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/lists.html#-Create-a-Segment).

### GET /contactdb/segments/{segment_id}


```php
$query_params = json_decode('{"segment_id": 1}');
$segment_id = "test_url_param";
$response = $sg->client->contactdb()->segments()->_($segment_id)->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a segment

**This endpoint allows you to delete a segment from your recipients database.**

You also have the option to delete all the contacts from your Marketing Campaigns recipient database who were in this segment.

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

For more information about segments in Marketing Campaigns, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/lists.html#-Create-a-Segment).

### DELETE /contactdb/segments/{segment_id}


```php
$query_params = json_decode('{"delete_contacts": "true"}');
$segment_id = "test_url_param";
$response = $sg->client->contactdb()->segments()->_($segment_id)->delete(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve recipients on a segment

**This endpoint allows you to retrieve all of the recipients in a segment with the given ID.**

The Contacts API helps you manage your [Marketing Campaigns](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/index.html) recipients.

For more information about segments in Marketing Campaigns, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/lists.html#-Create-a-Segment).

### GET /contactdb/segments/{segment_id}/recipients


```php
$query_params = json_decode('{"page": 1, "page_size": 1}');
$segment_id = "test_url_param";
$response = $sg->client->contactdb()->segments()->_($segment_id)->recipients()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="devices"></a>
# DEVICES

## Retrieve email statistics by device type.

**This endpoint allows you to retrieve your email statistics segmented by the device type.**

**We only store up to 7 days of email activity in our database.** By default, 500 items will be returned per request via the Advanced Stats API endpoints.

## Available Device Types
| **Device** | **Description** | **Example** |
|---|---|---|
| Desktop | Email software on desktop computer. | I.E., Outlook, Sparrow, or Apple Mail. |
| Webmail |	A web-based email client. | I.E., Yahoo, Google, AOL, or Outlook.com. |
| Phone | A smart phone. | iPhone, Android, Blackberry, etc.
| Tablet | A tablet computer. | iPad, android based tablet, etc. |
| Other | An unrecognized device. |

Advanced Stats provide a more in-depth view of your email statistics and the actions taken by your recipients. You can segment these statistics by geographic location, device type, client type, browser, and mailbox provider. For more information about statistics, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/index.html).

### GET /devices/stats


```php
$query_params = json_decode('{"aggregated_by": "day", "limit": 1, "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": 1}');
$response = $sg->client->devices()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="geo"></a>
# GEO

## Retrieve email statistics by country and state/province.

**This endpoint allows you to retrieve your email statistics segmented by country and state/province.**

**We only store up to 7 days of email activity in our database.** By default, 500 items will be returned per request via the Advanced Stats API endpoints.

Advanced Stats provide a more in-depth view of your email statistics and the actions taken by your recipients. You can segment these statistics by geographic location, device type, client type, browser, and mailbox provider. For more information about statistics, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/index.html).

### GET /geo/stats


```php
$query_params = json_decode('{"end_date": "2016-04-01", "country": "US", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01"}');
$response = $sg->client->geo()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="ips"></a>
# IPS

## Retrieve all IP addresses

**This endpoint allows you to retrieve a list of all assigned and unassigned IPs.**

Response includes warm up status, pools, assigned subusers, and whitelabel info. The start_date field corresponds to when warmup started for that IP.

A single IP address or a range of IP addresses may be dedicated to an account in order to send email for multiple domains. The reputation of this IP is based on the aggregate performance of all the senders who use it.

### GET /ips


```php
$query_params = json_decode('{"subuser": "test_string", "ip": "test_string", "limit": 1, "exclude_whitelabels": "true", "offset": 1}');
$response = $sg->client->ips()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all assigned IPs

**This endpoint allows you to retrieve only assigned IP addresses.**

A single IP address or a range of IP addresses may be dedicated to an account in order to send email for multiple domains. The reputation of this IP is based on the aggregate performance of all the senders who use it.

### GET /ips/assigned


```php
$response = $sg->client->ips()->assigned()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create an IP pool.

**This endpoint allows you to create an IP pool.**

**Each user can create up to 10 different IP pools.**

IP Pools allow you to group your dedicated SendGrid IP addresses together. For example, you could create separate pools for your transactional and marketing email. When sending marketing emails, specify that you want to use the marketing IP pool. This allows you to maintain separate reputations for your different email traffic.

IP pools can only be used with whitelabeled IP addresses.

If an IP pool is NOT specified for an email, it will use any IP available, including ones in pools.

### POST /ips/pools


```php
$request_body = json_decode('{
  "name": "marketing"
}');
$response = $sg->client->ips()->pools()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all IP pools.

**This endpoint allows you to retreive all of your IP pools.**

IP Pools allow you to group your dedicated SendGrid IP addresses together. For example, you could create separate pools for your transactional and marketing email. When sending marketing emails, specify that you want to use the marketing IP pool. This allows you to maintain separate reputations for your different email traffic.

IP pools can only be used with whitelabeled IP addresses.

If an IP pool is NOT specified for an email, it will use any IP available, including ones in pools.

### GET /ips/pools


```php
$response = $sg->client->ips()->pools()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update an IP pools name.

**This endpoint allows you to update the name of an IP pool.**

IP Pools allow you to group your dedicated SendGrid IP addresses together. For example, you could create separate pools for your transactional and marketing email. When sending marketing emails, specify that you want to use the marketing IP pool. This allows you to maintain separate reputations for your different email traffic.

IP pools can only be used with whitelabeled IP addresses.

If an IP pool is NOT specified for an email, it will use any IP available, including ones in pools.

### PUT /ips/pools/{pool_name}


```php
$request_body = json_decode('{
  "name": "new_pool_name"
}');
$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all IPs in a specified pool.

**This endpoint allows you to list all of the IP addresses that are in a specific IP pool.**

IP Pools allow you to group your dedicated SendGrid IP addresses together. For example, you could create separate pools for your transactional and marketing email. When sending marketing emails, specify that you want to use the marketing IP pool. This allows you to maintain separate reputations for your different email traffic.

IP pools can only be used with whitelabeled IP addresses.

If an IP pool is NOT specified for an email, it will use any IP available, including ones in pools.

### GET /ips/pools/{pool_name}


```php
$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete an IP pool.

**This endpoint allows you to delete an IP pool.**

IP Pools allow you to group your dedicated SendGrid IP addresses together. For example, you could create separate pools for your transactional and marketing email. When sending marketing emails, specify that you want to use the marketing IP pool. This allows you to maintain separate reputations for your different email traffic.

IP pools can only be used with whitelabeled IP addresses.

If an IP pool is NOT specified for an email, it will use any IP available, including ones in pools.

### DELETE /ips/pools/{pool_name}


```php
$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add an IP address to a pool

**This endpoint allows you to add an IP address to an IP pool.**

You can add the same IP address to multiple pools. It may take up to 60 seconds for your IP address to be added to a pool after your request is made.

A single IP address or a range of IP addresses may be dedicated to an account in order to send email for multiple domains. The reputation of this IP is based on the aggregate performance of all the senders who use it.

### POST /ips/pools/{pool_name}/ips


```php
$request_body = json_decode('{
  "ip": "0.0.0.0"
}');
$pool_name = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->ips()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Remove an IP address from a pool.

**This endpoint allows you to remove an IP address from an IP pool.**

The same IP address can be added to multiple IP pools.

A single IP address or a range of IP addresses may be dedicated to an account in order to send email for multiple domains. The reputation of this IP is based on the aggregate performance of all the senders who use it.

### DELETE /ips/pools/{pool_name}/ips/{ip}


```php
$pool_name = "test_url_param";
$ip = "test_url_param";
$response = $sg->client->ips()->pools()->_($pool_name)->ips()->_($ip)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add an IP to warmup

**This endpoint allows you to enter an IP address into warmup mode.**

SendGrid can automatically warm up dedicated IP addresses by limiting the amount of mail that can be sent through them per hour, with the limit determined by how long the IP address has been in warmup. See the [warmup schedule](https://sendgrid.com/docs/API_Reference/Web_API_v3/IP_Management/ip_warmup_schedule.html) for more details on how SendGrid limits your email traffic for IPs in warmup.

For more general information about warming up IPs, please see our [Classroom](https://sendgrid.com/docs/Classroom/Deliver/Delivery_Introduction/warming_up_ips.html).

### POST /ips/warmup


```php
$request_body = json_decode('{
  "ip": "0.0.0.0"
}');
$response = $sg->client->ips()->warmup()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all IPs currently in warmup

**This endpoint allows you to retrieve all of your IP addresses that are currently warming up.**

SendGrid can automatically warm up dedicated IP addresses by limiting the amount of mail that can be sent through them per hour, with the limit determined by how long the IP address has been in warmup. See the [warmup schedule](https://sendgrid.com/docs/API_Reference/Web_API_v3/IP_Management/ip_warmup_schedule.html) for more details on how SendGrid limits your email traffic for IPs in warmup.

For more general information about warming up IPs, please see our [Classroom](https://sendgrid.com/docs/Classroom/Deliver/Delivery_Introduction/warming_up_ips.html).

### GET /ips/warmup


```php
$response = $sg->client->ips()->warmup()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve warmup status for a specific IP address

**This endpoint allows you to retrieve the warmup status for a specific IP address.**

SendGrid can automatically warm up dedicated IP addresses by limiting the amount of mail that can be sent through them per hour, with the limit determined by how long the IP address has been in warmup. See the [warmup schedule](https://sendgrid.com/docs/API_Reference/Web_API_v3/IP_Management/ip_warmup_schedule.html) for more details on how SendGrid limits your email traffic for IPs in warmup.

For more general information about warming up IPs, please see our [Classroom](https://sendgrid.com/docs/Classroom/Deliver/Delivery_Introduction/warming_up_ips.html).

### GET /ips/warmup/{ip_address}


```php
$ip_address = "test_url_param";
$response = $sg->client->ips()->warmup()->_($ip_address)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Remove an IP from warmup

**This endpoint allows you to remove an IP address from warmup mode.**

SendGrid can automatically warm up dedicated IP addresses by limiting the amount of mail that can be sent through them per hour, with the limit determined by how long the IP address has been in warmup. See the [warmup schedule](https://sendgrid.com/docs/API_Reference/Web_API_v3/IP_Management/ip_warmup_schedule.html) for more details on how SendGrid limits your email traffic for IPs in warmup.

For more general information about warming up IPs, please see our [Classroom](https://sendgrid.com/docs/Classroom/Deliver/Delivery_Introduction/warming_up_ips.html).

### DELETE /ips/warmup/{ip_address}


```php
$ip_address = "test_url_param";
$response = $sg->client->ips()->warmup()->_($ip_address)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all IP pools an IP address belongs to

**This endpoint allows you to see which IP pools a particular IP address has been added to.**

The same IP address can be added to multiple IP pools.

A single IP address or a range of IP addresses may be dedicated to an account in order to send email for multiple domains. The reputation of this IP is based on the aggregate performance of all the senders who use it.

### GET /ips/{ip_address}


```php
$ip_address = "test_url_param";
$response = $sg->client->ips()->_($ip_address)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="mail"></a>
# MAIL

## Create a batch ID

**This endpoint allows you to generate a new batch ID. This batch ID can be associated with scheduled sends via the mail/send endpoint.**

If you set the SMTPAPI header `batch_id`, it allows you to then associate multiple scheduled mail/send requests together with the same ID. Then at anytime up to 10 minutes before the schedule date, you can cancel all of the mail/send requests that have this batch ID by calling the Cancel Scheduled Send endpoint.

More Information:

* [Scheduling Parameters > Batch ID](https://sendgrid.com/docs/API_Reference/SMTP_API/scheduling_parameters.html)

### POST /mail/batch


```php
$response = $sg->client->mail()->batch()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Validate batch ID

**This endpoint allows you to validate a batch ID.**

If you set the SMTPAPI header `batch_id`, it allows you to then associate multiple scheduled mail/send requests together with the same ID. Then at anytime up to 10 minutes before the schedule date, you can cancel all of the mail/send requests that have this batch ID by calling the Cancel Scheduled Send endpoint.

More Information:

* [Scheduling Parameters > Batch ID](https://sendgrid.com/docs/API_Reference/SMTP_API/scheduling_parameters.html)

### GET /mail/batch/{batch_id}


```php
$batch_id = "test_url_param";
$response = $sg->client->mail()->batch()->_($batch_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## v3 Mail Send

This endpoint allows you to send email over SendGrids v3 Web API, the most recent version of our API. If you are looking for documentation about the v2 Mail Send endpoint, please see our [v2 API Reference](https://sendgrid.com/docs/API_Reference/Web_API/mail.html).

* Top level parameters are referred to as "global".
* Individual fields within the personalizations array will override any other global, or message level, parameters that are defined outside of personalizations.

For an overview of the v3 Mail Send endpoint, please visit our [v3 API Reference](https://sendgrid.com/docs/API_Reference/Web_API_v3/Mail/index.html)

For more detailed information about how to use the v3 Mail Send endpoint, please visit our [Classroom](https://sendgrid.com/docs/Classroom/Send/v3_Mail_Send/index.html).

### POST /mail/send

This endpoint has a helper, check it out [here](https://github.com/sendgrid/sendgrid-php/blob/master/lib/helpers/mail/README.md).

```php
$request_body = json_decode('{
  "asm": {
    "group_id": 1,
    "groups_to_display": [
      1,
      2,
      3
    ]
  },
  "attachments": [
    {
      "content": "[BASE64 encoded content block here]",
      "content_id": "ii_139db99fdb5c3704",
      "disposition": "inline",
      "filename": "file1.jpg",
      "name": "file1",
      "type": "jpg"
    }
  ],
  "batch_id": "[YOUR BATCH ID GOES HERE]",
  "categories": [
    "category1",
    "category2"
  ],
  "content": [
    {
      "type": "text/html",
      "value": "<html><p>Hello, world!</p><img src=[CID GOES HERE]></img></html>"
    }
  ],
  "custom_args": {
    "New Argument 1": "New Value 1",
    "activationAttempt": "1",
    "customerAccountNumber": "[CUSTOMER ACCOUNT NUMBER GOES HERE]"
  },
  "from": {
    "email": "sam.smith@example.com",
    "name": "Sam Smith"
  },
  "headers": {},
  "ip_pool_name": "[YOUR POOL NAME GOES HERE]",
  "mail_settings": {
    "bcc": {
      "email": "ben.doe@example.com",
      "enable": true
    },
    "bypass_list_management": {
      "enable": true
    },
    "footer": {
      "enable": true,
      "html": "<p>Thanks</br>The SendGrid Team</p>",
      "text": "Thanks,/n The SendGrid Team"
    },
    "sandbox_mode": {
      "enable": false
    },
    "spam_check": {
      "enable": true,
      "post_to_url": "http://example.com/compliance",
      "threshold": 3
    }
  },
  "personalizations": [
    {
      "bcc": [
        {
          "email": "sam.doe@example.com",
          "name": "Sam Doe"
        }
      ],
      "cc": [
        {
          "email": "jane.doe@example.com",
          "name": "Jane Doe"
        }
      ],
      "custom_args": {
        "New Argument 1": "New Value 1",
        "activationAttempt": "1",
        "customerAccountNumber": "[CUSTOMER ACCOUNT NUMBER GOES HERE]"
      },
      "headers": {
        "X-Accept-Language": "en",
        "X-Mailer": "MyApp"
      },
      "send_at": 1409348513,
      "subject": "Hello, World!",
      "substitutions": {
        "id": "substitutions",
        "type": "object"
      },
      "to": [
        {
          "email": "john.doe@example.com",
          "name": "John Doe"
        }
      ]
    }
  ],
  "reply_to": {
    "email": "sam.smith@example.com",
    "name": "Sam Smith"
  },
  "sections": {
    "section": {
      ":sectionName1": "section 1 text",
      ":sectionName2": "section 2 text"
    }
  },
  "send_at": 1409348513,
  "subject": "Hello, World!",
  "template_id": "[YOUR TEMPLATE ID GOES HERE]",
  "tracking_settings": {
    "click_tracking": {
      "enable": true,
      "enable_text": true
    },
    "ganalytics": {
      "enable": true,
      "utm_campaign": "[NAME OF YOUR REFERRER SOURCE]",
      "utm_content": "[USE THIS SPACE TO DIFFERENTIATE YOUR EMAIL FROM ADS]",
      "utm_medium": "[NAME OF YOUR MARKETING MEDIUM e.g. email]",
      "utm_name": "[NAME OF YOUR CAMPAIGN]",
      "utm_term": "[IDENTIFY PAID KEYWORDS HERE]"
    },
    "open_tracking": {
      "enable": true,
      "substitution_tag": "%opentrack"
    },
    "subscription_tracking": {
      "enable": true,
      "html": "If you would like to unsubscribe and stop receiving these emails <% clickhere %>.",
      "substitution_tag": "<%click here%>",
      "text": "If you would like to unsubscribe and stop receiveing these emails <% click here %>."
    }
  }
}');
$response = $sg->client->mail()->send()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="mail_settings"></a>
# MAIL SETTINGS

## Retrieve all mail settings

**This endpoint allows you to retrieve a list of all mail settings.**

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings


```php
$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->mail_settings()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update address whitelist mail settings

**This endpoint allows you to update your current email address whitelist settings.**

The address whitelist setting whitelists a specified email address or domain for which mail should never be suppressed. For example, you own the domain example.com, and one or more of your recipients use email@example.com addresses, by placing example.com in the address whitelist setting, all bounces, blocks, and unsubscribes logged for that domain will be ignored and sent as if under normal sending conditions.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/address_whitelist


```php
$request_body = json_decode('{
  "enabled": true,
  "list": [
    "email1@example.com",
    "example.com"
  ]
}');
$response = $sg->client->mail_settings()->address_whitelist()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve address whitelist mail settings

**This endpoint allows you to retrieve your current email address whitelist settings.**

The address whitelist setting whitelists a specified email address or domain for which mail should never be suppressed. For example, you own the domain example.com, and one or more of your recipients use email@example.com addresses, by placing example.com in the address whitelist setting, all bounces, blocks, and unsubscribes logged for that domain will be ignored and sent as if under normal sending conditions.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/address_whitelist


```php
$response = $sg->client->mail_settings()->address_whitelist()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update BCC mail settings

**This endpoint allows you to update your current BCC mail settings.**

When the BCC mail setting is enabled, SendGrid will automatically send a blind carbon copy (BCC) to an address for every email sent without adding that address to the header. Please note that only one email address may be entered in this field, if you wish to distribute BCCs to multiple addresses you will need to create a distribution group or use forwarding rules.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/bcc


```php
$request_body = json_decode('{
  "email": "email@example.com",
  "enabled": false
}');
$response = $sg->client->mail_settings()->bcc()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all BCC mail settings

**This endpoint allows you to retrieve your current BCC mail settings.**

When the BCC mail setting is enabled, SendGrid will automatically send a blind carbon copy (BCC) to an address for every email sent without adding that address to the header. Please note that only one email address may be entered in this field, if you wish to distribute BCCs to multiple addresses you will need to create a distribution group or use forwarding rules.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/bcc


```php
$response = $sg->client->mail_settings()->bcc()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update bounce purge mail settings

**This endpoint allows you to update your current bounce purge settings.**

This setting allows you to set a schedule for SendGrid to automatically delete contacts from your soft and hard bounce suppression lists.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/bounce_purge


```php
$request_body = json_decode('{
  "enabled": true,
  "hard_bounces": 5,
  "soft_bounces": 5
}');
$response = $sg->client->mail_settings()->bounce_purge()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve bounce purge mail settings

**This endpoint allows you to retrieve your current bounce purge settings.**

This setting allows you to set a schedule for SendGrid to automatically delete contacts from your soft and hard bounce suppression lists.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/bounce_purge


```php
$response = $sg->client->mail_settings()->bounce_purge()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update footer mail settings

**This endpoint allows you to update your current Footer mail settings.**

The footer setting will insert a custom footer at the bottom of the text and HTML bodies. Use the embedded HTML editor and plain text entry fields to create the content of the footers to be inserted into your emails.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/footer


```php
$request_body = json_decode('{
  "enabled": true,
  "html_content": "...",
  "plain_content": "..."
}');
$response = $sg->client->mail_settings()->footer()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve footer mail settings

**This endpoint allows you to retrieve your current Footer mail settings.**

The footer setting will insert a custom footer at the bottom of the text and HTML bodies. Use the embedded HTML editor and plain text entry fields to create the content of the footers to be inserted into your emails.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/footer


```php
$response = $sg->client->mail_settings()->footer()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update forward bounce mail settings

**This endpoint allows you to update your current bounce forwarding mail settings.**

Activating this setting allows you to specify an email address to which bounce reports are forwarded.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/forward_bounce


```php
$request_body = json_decode('{
  "email": "example@example.com",
  "enabled": true
}');
$response = $sg->client->mail_settings()->forward_bounce()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve forward bounce mail settings

**This endpoint allows you to retrieve your current bounce forwarding mail settings.**

Activating this setting allows you to specify an email address to which bounce reports are forwarded.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/forward_bounce


```php
$response = $sg->client->mail_settings()->forward_bounce()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update forward spam mail settings

**This endpoint allows you to update your current Forward Spam mail settings.**

Enabling the forward spam setting allows you to specify an email address to which spam reports will be forwarded.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/forward_spam


```php
$request_body = json_decode('{
  "email": "",
  "enabled": false
}');
$response = $sg->client->mail_settings()->forward_spam()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve forward spam mail settings

**This endpoint allows you to retrieve your current Forward Spam mail settings.**

Enabling the forward spam setting allows you to specify an email address to which spam reports will be forwarded.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/forward_spam


```php
$response = $sg->client->mail_settings()->forward_spam()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update plain content mail settings

**This endpoint allows you to update your current Plain Content mail settings.**

The plain content setting will automatically convert any plain text emails that you send to HTML before sending.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/plain_content


```php
$request_body = json_decode('{
  "enabled": false
}');
$response = $sg->client->mail_settings()->plain_content()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve plain content mail settings

**This endpoint allows you to retrieve your current Plain Content mail settings.**

The plain content setting will automatically convert any plain text emails that you send to HTML before sending.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/plain_content


```php
$response = $sg->client->mail_settings()->plain_content()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update spam check mail settings

**This endpoint allows you to update your current spam checker mail settings.**

The spam checker filter notifies you when emails are detected that exceed a predefined spam threshold.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/spam_check


```php
$request_body = json_decode('{
  "enabled": true,
  "max_score": 5,
  "url": "url"
}');
$response = $sg->client->mail_settings()->spam_check()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve spam check mail settings

**This endpoint allows you to retrieve your current Spam Checker mail settings.**

The spam checker filter notifies you when emails are detected that exceed a predefined spam threshold.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/spam_check


```php
$response = $sg->client->mail_settings()->spam_check()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update template mail settings

**This endpoint allows you to update your current legacy email template settings.**

This setting refers to our original email templates. We currently support more fully featured [transactional templates](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

The legacy email template setting wraps an HTML template around your email content. This can be useful for sending out marketing email and/or other HTML formatted messages.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### PATCH /mail_settings/template


```php
$request_body = json_decode('{
  "enabled": true,
  "html_content": "<% body %>"
}');
$response = $sg->client->mail_settings()->template()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve legacy template mail settings

**This endpoint allows you to retrieve your current legacy email template settings.**

This setting refers to our original email templates. We currently support more fully featured [transactional templates](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

The legacy email template setting wraps an HTML template around your email content. This can be useful for sending out marketing email and/or other HTML formatted messages.

Mail settings allow you to tell SendGrid specific things to do to every email that you send to your recipients over SendGrids [Web API](https://sendgrid.com/docs/API_Reference/Web_API/mail.html) or [SMTP Relay](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

### GET /mail_settings/template


```php
$response = $sg->client->mail_settings()->template()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="mailbox_providers"></a>
# MAILBOX PROVIDERS

## Retrieve email statistics by mailbox provider.

**This endpoint allows you to retrieve your email statistics segmented by recipient mailbox provider.**

**We only store up to 7 days of email activity in our database.** By default, 500 items will be returned per request via the Advanced Stats API endpoints.

Advanced Stats provide a more in-depth view of your email statistics and the actions taken by your recipients. You can segment these statistics by geographic location, device type, client type, browser, and mailbox provider. For more information about statistics, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/index.html).

### GET /mailbox_providers/stats


```php
$query_params = json_decode('{"end_date": "2016-04-01", "mailbox_providers": "test_string", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01"}');
$response = $sg->client->mailbox_providers()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="partner_settings"></a>
# PARTNER SETTINGS

## Returns a list of all partner settings.

**This endpoint allows you to retrieve a list of all partner settings that you can enable.**

Our partner settings allow you to integrate your SendGrid account with our partners to increase your SendGrid experience and functionality. For more information about our partners, and how you can begin integrating with them, please visit our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/partners.html).

### GET /partner_settings


```php
$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->partner_settings()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Updates New Relic partner settings.

**This endpoint allows you to update or change your New Relic partner settings.**

Our partner settings allow you to integrate your SendGrid account with our partners to increase your SendGrid experience and functionality. For more information about our partners, and how you can begin integrating with them, please visit our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/partners.html).

By integrating with New Relic, you can send your SendGrid email statistics to your New Relic Dashboard. If you enable this setting, your stats will be sent to New Relic every 5 minutes. You will need your New Relic License Key to enable this setting. For more information, please see our [Classroom](https://sendgrid.com/docs/Classroom/Track/Collecting_Data/new_relic.html).

### PATCH /partner_settings/new_relic


```php
$request_body = json_decode('{
  "enable_subuser_statistics": true,
  "enabled": true,
  "license_key": ""
}');
$response = $sg->client->partner_settings()->new_relic()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Returns all New Relic partner settings.

**This endpoint allows you to retrieve your current New Relic partner settings.**

Our partner settings allow you to integrate your SendGrid account with our partners to increase your SendGrid experience and functionality. For more information about our partners, and how you can begin integrating with them, please visit our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/partners.html).

By integrating with New Relic, you can send your SendGrid email statistics to your New Relic Dashboard. If you enable this setting, your stats will be sent to New Relic every 5 minutes. You will need your New Relic License Key to enable this setting. For more information, please see our [Classroom](https://sendgrid.com/docs/Classroom/Track/Collecting_Data/new_relic.html).

### GET /partner_settings/new_relic


```php
$response = $sg->client->partner_settings()->new_relic()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="scopes"></a>
# SCOPES

## Retrieve a list of scopes for which this user has access.

**This endpoint returns a list of all scopes that this user has access to.**

API Keys can be used to authenticate the use of [SendGrids v3 Web API](https://sendgrid.com/docs/API_Reference/Web_API_v3/index.html), or the [Mail API Endpoint](https://sendgrid.com/docs/API_Reference/Web_API/mail.html). API Keys may be assigned certain permissions, or scopes, that limit which API endpoints they are able to access. For a more detailed explanation of how you can use API Key permissios, please visit our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/api_keys.html#-API-Key-Permissions) or [Classroom](https://sendgrid.com/docs/Classroom/Basics/API/api_key_permissions.html).

### GET /scopes


```php
$response = $sg->client->scopes()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="senders"></a>
# SENDERS

## Create a Sender Identity

**This endpoint allows you to create a new sender identity.**

*You may create up to 100 unique sender identities.*

Sender Identities are required to be verified before use. If your domain has been whitelabeled it will auto verify on creation. Otherwise an email will be sent to the `from.email`.

### POST /senders


```php
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
```
## Get all Sender Identities

**This endpoint allows you to retrieve a list of all sender identities that have been created for your account.**

Sender Identities are required to be verified before use. If your domain has been whitelabeled it will auto verify on creation. Otherwise an email will be sent to the `from.email`.

### GET /senders


```php
$response = $sg->client->senders()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a Sender Identity

**This endpoint allows you to update a sender identity.**

Updates to `from.email` require re-verification. If your domain has been whitelabeled it will auto verify on creation. Otherwise an email will be sent to the `from.email`.

Partial updates are allowed, but fields that are marked as "required" in the POST (create) endpoint must not be nil if that field is included in the PATCH request.

### PATCH /senders/{sender_id}


```php
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
```
## View a Sender Identity

**This endpoint allows you to retrieve a specific sender identity.**

Sender Identities are required to be verified before use. If your domain has been whitelabeled it will auto verify on creation. Otherwise an email will be sent to the `from.email`.

### GET /senders/{sender_id}


```php
$sender_id = "test_url_param";
$response = $sg->client->senders()->_($sender_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a Sender Identity

**This endoint allows you to delete one of your sender identities.**

Sender Identities are required to be verified before use. If your domain has been whitelabeled it will auto verify on creation. Otherwise an email will be sent to the `from.email`.

### DELETE /senders/{sender_id}


```php
$sender_id = "test_url_param";
$response = $sg->client->senders()->_($sender_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Resend Sender Identity Verification

**This enpdoint allows you to resend a sender identity verification email.**

Sender Identities are required to be verified before use. If your domain has been whitelabeled it will auto verify on creation. Otherwise an email will be sent to the `from.email`.

### POST /senders/{sender_id}/resend_verification


```php
$sender_id = "test_url_param";
$response = $sg->client->senders()->_($sender_id)->resend_verification()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="stats"></a>
# STATS

## Retrieve global email statistics

**This endpoint allows you to retrieve all of your global email statistics between a given date range.**

Parent accounts will see aggregated stats for their account and all subuser accounts. Subuser accounts will only see their own stats.

### GET /stats


```php
$query_params = json_decode('{"aggregated_by": "day", "limit": 1, "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": 1}');
$response = $sg->client->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="subusers"></a>
# SUBUSERS

## Create Subuser

This endpoint allows you to retrieve a list of all of your subusers. You can choose to retrieve specific subusers as well as limit the results that come back from the API.

For more information about Subusers:

* [User Guide > Subusers](https://sendgrid.com/docs/User_Guide/Settings/Subusers/index.html)
* [Classroom > How do I add more subusers to my account?](https://sendgrid.com/docs/Classroom/Basics/Account/how_do_i_add_more_subusers_to_my_account.html)

### POST /subusers


```php
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
```
## List all Subusers

This endpoint allows you to retrieve a list of all of your subusers. You can choose to retrieve specific subusers as well as limit the results that come back from the API.

For more information about Subusers:

* [User Guide > Subusers](https://sendgrid.com/docs/User_Guide/Settings/Subusers/index.html)
* [Classroom > How do I add more subusers to my account?](https://sendgrid.com/docs/Classroom/Basics/Account/how_do_i_add_more_subusers_to_my_account.html)

### GET /subusers


```php
$query_params = json_decode('{"username": "test_string", "limit": 1, "offset": 1}');
$response = $sg->client->subusers()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve Subuser Reputations

Subuser sender reputations give a good idea how well a sender is doing with regards to how recipients and recipient servers react to the mail that is being received. When a bounce, spam report, or other negative action happens on a sent email, it will effect your sender rating.

This endpoint allows you to request the reputations for your subusers.

### GET /subusers/reputations


```php
$query_params = json_decode('{"usernames": "test_string"}');
$response = $sg->client->subusers()->reputations()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve email statistics for your subusers.

**This endpoint allows you to retrieve the email statistics for the given subusers.**

You may retrieve statistics for up to 10 different subusers by including an additional _subusers_ parameter for each additional subuser.

While you can always view the statistics for all email activity on your account, subuser statistics enable you to view specific segments of your stats. Emails sent, bounces, and spam reports are always tracked for subusers. Unsubscribes, clicks, and opens are tracked if you have enabled the required settings.

For more information, see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/subuser.html).

### GET /subusers/stats


```php
$query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01", "subusers": "test_string"}');
$response = $sg->client->subusers()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve monthly stats for all subusers

**This endpoint allows you to retrieve the monthly email statistics for all subusers over the given date range.**

While you can always view the statistics for all email activity on your account, subuser statistics enable you to view specific segments of your stats for your subusers. Emails sent, bounces, and spam reports are always tracked for subusers. Unsubscribes, clicks, and opens are tracked if you have enabled the required settings.

When using the `sort_by_metric` to sort your stats by a specific metric, you can not sort by the following metrics:
`bounce_drops`, `deferred`, `invalid_emails`, `processed`, `spam_report_drops`, `spam_reports`, or `unsubscribe_drops`.

For more information, see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/subuser.html).

### GET /subusers/stats/monthly


```php
$query_params = json_decode('{"subuser": "test_string", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "date": "test_string", "sort_by_direction": "asc"}');
$response = $sg->client->subusers()->stats()->monthly()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
##  Retrieve the totals for each email statistic metric for all subusers.

**This endpoint allows you to retrieve the total sums of each email statistic metric for all subusers over the given date range.**


While you can always view the statistics for all email activity on your account, subuser statistics enable you to view specific segments of your stats. Emails sent, bounces, and spam reports are always tracked for subusers. Unsubscribes, clicks, and opens are tracked if you have enabled the required settings.

For more information, see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/subuser.html).

### GET /subusers/stats/sums


```php
$query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "start_date": "2016-01-01", "sort_by_direction": "asc"}');
$response = $sg->client->subusers()->stats()->sums()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Enable/disable a subuser

This endpoint allows you to enable or disable a subuser.

For more information about Subusers:

* [User Guide > Subusers](https://sendgrid.com/docs/User_Guide/Settings/Subusers/index.html)
* [Classroom > How do I add more subusers to my account?](https://sendgrid.com/docs/Classroom/Basics/Account/how_do_i_add_more_subusers_to_my_account.html)

### PATCH /subusers/{subuser_name}


```php
$request_body = json_decode('{
  "disabled": false
}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a subuser

This endpoint allows you to delete a subuser. This is a permanent action, once deleted a subuser cannot be retrieved.

For more information about Subusers:

* [User Guide > Subusers](https://sendgrid.com/docs/User_Guide/Settings/Subusers/index.html)
* [Classroom > How do I add more subusers to my account?](https://sendgrid.com/docs/Classroom/Basics/Account/how_do_i_add_more_subusers_to_my_account.html)

### DELETE /subusers/{subuser_name}


```php
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update IPs assigned to a subuser

Each subuser should be assigned to an IP address, from which all of this subuser's mail will be sent. Often, this is the same IP as the parent account, but each subuser can have their own, or multiple, IP addresses as well.

More information:

* [How to request more IPs](https://sendgrid.com/docs/Classroom/Basics/Account/adding_an_additional_dedicated_ip_to_your_account.html)
* [IPs can be whitelabeled](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/ips.html)

### PUT /subusers/{subuser_name}/ips


```php
$request_body = json_decode('[
  "127.0.0.1"
]');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->ips()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Monitor Settings for a subuser

Subuser monitor settings allow you to receive a sample of an outgoing message by a specific customer at a specific frequency of emails.

### PUT /subusers/{subuser_name}/monitor


```php
$request_body = json_decode('{
  "email": "example@example.com",
  "frequency": 500
}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create monitor settings

Subuser monitor settings allow you to receive a sample of an outgoing message by a specific customer at a specific frequency of emails.

### POST /subusers/{subuser_name}/monitor


```php
$request_body = json_decode('{
  "email": "example@example.com",
  "frequency": 50000
}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve monitor settings for a subuser

Subuser monitor settings allow you to receive a sample of an outgoing message by a specific customer at a specific frequency of emails.

### GET /subusers/{subuser_name}/monitor


```php
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete monitor settings

Subuser monitor settings allow you to receive a sample of an outgoing message by a specific customer at a specific frequency of emails.

### DELETE /subusers/{subuser_name}/monitor


```php
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->monitor()->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve the monthly email statistics for a single subuser

**This endpoint allows you to retrive the monthly email statistics for a specific subuser.**

While you can always view the statistics for all email activity on your account, subuser statistics enable you to view specific segments of your stats for your subusers. Emails sent, bounces, and spam reports are always tracked for subusers. Unsubscribes, clicks, and opens are tracked if you have enabled the required settings.

When using the `sort_by_metric` to sort your stats by a specific metric, you can not sort by the following metrics:
`bounce_drops`, `deferred`, `invalid_emails`, `processed`, `spam_report_drops`, `spam_reports`, or `unsubscribe_drops`.

For more information, see our [User Guide](https://sendgrid.com/docs/User_Guide/Statistics/subuser.html).

### GET /subusers/{subuser_name}/stats/monthly


```php
$query_params = json_decode('{"date": "test_string", "sort_by_direction": "asc", "limit": 1, "sort_by_metric": "test_string", "offset": 1}');
$subuser_name = "test_url_param";
$response = $sg->client->subusers()->_($subuser_name)->stats()->monthly()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="suppression"></a>
# SUPPRESSION

## Retrieve all blocks

**This endpoint allows you to retrieve a list of all email addresses that are currently on your blocks list.**

[Blocks](https://sendgrid.com/docs/Glossary/blocks.html) happen when your message was rejected for a reason related to the message, not the recipient address. This can happen when your mail server IP address has been added to a blacklist or blocked by an ISP, or if the message content is flagged by a filter on the receiving server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/blocks.html).

### GET /suppression/blocks


```php
$query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
$response = $sg->client->suppression()->blocks()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete blocks

**This endpoint allows you to delete all email addresses on your blocks list.**

There are two options for deleting blocked emails:

1. You can delete all blocked emails by setting `delete_all` to true in the request body.
2. You can delete some blocked emails by specifying the email addresses in an array in the request body.

[Blocks](https://sendgrid.com/docs/Glossary/blocks.html) happen when your message was rejected for a reason related to the message, not the recipient address. This can happen when your mail server IP address has been added to a blacklist or blocked by an ISP, or if the message content is flagged by a filter on the receiving server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/blocks.html).

### DELETE /suppression/blocks


```php
$request_body = json_decode('{
  "delete_all": false,
  "emails": [
    "example1@example.com",
    "example2@example.com"
  ]
}');
$response = $sg->client->suppression()->blocks()->delete($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a specific block

**This endpoint allows you to retrieve a specific email address from your blocks list.**

[Blocks](https://sendgrid.com/docs/Glossary/blocks.html) happen when your message was rejected for a reason related to the message, not the recipient address. This can happen when your mail server IP address has been added to a blacklist or blocked by an ISP, or if the message content is flagged by a filter on the receiving server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/blocks.html).

### GET /suppression/blocks/{email}


```php
$email = "test_url_param";
$response = $sg->client->suppression()->blocks()->_($email)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a specific block

**This endpoint allows you to delete a specific email address from your blocks list.**

[Blocks](https://sendgrid.com/docs/Glossary/blocks.html) happen when your message was rejected for a reason related to the message, not the recipient address. This can happen when your mail server IP address has been added to a blacklist or blocked by an ISP, or if the message content is flagged by a filter on the receiving server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/blocks.html).

### DELETE /suppression/blocks/{email}


```php
$email = "test_url_param";
$response = $sg->client->suppression()->blocks()->_($email)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all bounces

**This endpoint allows you to retrieve all of your bounces.**

Bounces are messages that are returned to the server that sent it.

For more information see:

* [User Guide > Bounces](https://sendgrid.com/docs/User_Guide/Suppressions/bounces.html) for more information
* [Glossary > Bounces](https://sendgrid.com/docs/Glossary/Bounces.html)

### GET /suppression/bounces


```php
$query_params = json_decode('{"start_time": 1, "end_time": 1}');
$response = $sg->client->suppression()->bounces()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete bounces

**This endpoint allows you to delete all of your bounces. You can also use this endpoint to remove a specific email address from your bounce list.**

Bounces are messages that are returned to the server that sent it.

For more information see:

* [User Guide > Bounces](https://sendgrid.com/docs/User_Guide/Suppressions/bounces.html) for more information
* [Glossary > Bounces](https://sendgrid.com/docs/Glossary/Bounces.html)
* [Classroom > List Scrubbing Guide](https://sendgrid.com/docs/Classroom/Deliver/list_scrubbing.html)

Note: the `delete_all` and `emails` parameters should be used independently of each other as they have different purposes.

### DELETE /suppression/bounces


```php
$request_body = json_decode('{
  "delete_all": true,
  "emails": [
    "example@example.com",
    "example2@example.com"
  ]
}');
$response = $sg->client->suppression()->bounces()->delete($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a Bounce

**This endpoint allows you to retrieve a specific bounce for a given email address.**

Bounces are messages that are returned to the server that sent it.

For more information see:

* [User Guide > Bounces](https://sendgrid.com/docs/User_Guide/Suppressions/bounces.html) for more information
* [Glossary > Bounces](https://sendgrid.com/docs/Glossary/Bounces.html)
* [Classroom > List Scrubbing Guide](https://sendgrid.com/docs/Classroom/Deliver/list_scrubbing.html)

### GET /suppression/bounces/{email}


```php
$email = "test_url_param";
$response = $sg->client->suppression()->bounces()->_($email)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a bounce

**This endpoint allows you to remove an email address from your bounce list.**

Bounces are messages that are returned to the server that sent it. This endpoint allows you to delete a single email addresses from your bounce list.

For more information see:

* [User Guide > Bounces](https://sendgrid.com/docs/User_Guide/Suppressions/bounces.html) for more information
* [Glossary > Bounces](https://sendgrid.com/docs/Glossary/Bounces.html)
* [Classroom > List Scrubbing Guide](https://sendgrid.com/docs/Classroom/Deliver/list_scrubbing.html)

### DELETE /suppression/bounces/{email}


```php
$query_params = json_decode('{"email_address": "example@example.com"}');
$email = "test_url_param";
$response = $sg->client->suppression()->bounces()->_($email)->delete(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all invalid emails

**This endpoint allows you to retrieve a list of all invalid email addresses.**

An invalid email occurs when you attempt to send email to an address that is formatted in a manner that does not meet internet email format standards or the email does not exist at the recipients mail server.

Examples include addresses without the @ sign or addresses that include certain special characters and/or spaces. This response can come from our own server or the recipient mail server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/invalid_emails.html).

### GET /suppression/invalid_emails


```php
$query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
$response = $sg->client->suppression()->invalid_emails()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete invalid emails

**This endpoint allows you to remove email addresses from your invalid email address list.**

There are two options for deleting invalid email addresses:

1) You can delete all invalid email addresses by setting `delete_all` to true in the request body.
2) You can delete some invalid email addresses by specifying certain addresses in an array in the request body.

An invalid email occurs when you attempt to send email to an address that is formatted in a manner that does not meet internet email format standards or the email does not exist at the recipients mail server.

Examples include addresses without the @ sign or addresses that include certain special characters and/or spaces. This response can come from our own server or the recipient mail server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/invalid_emails.html).

### DELETE /suppression/invalid_emails


```php
$request_body = json_decode('{
  "delete_all": false,
  "emails": [
    "example1@example.com",
    "example2@example.com"
  ]
}');
$response = $sg->client->suppression()->invalid_emails()->delete($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a specific invalid email

**This endpoint allows you to retrieve a specific invalid email addresses.**

An invalid email occurs when you attempt to send email to an address that is formatted in a manner that does not meet internet email format standards or the email does not exist at the recipients mail server.

Examples include addresses without the @ sign or addresses that include certain special characters and/or spaces. This response can come from our own server or the recipient mail server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/invalid_emails.html).

### GET /suppression/invalid_emails/{email}


```php
$email = "test_url_param";
$response = $sg->client->suppression()->invalid_emails()->_($email)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a specific invalid email

**This endpoint allows you to remove a specific email address from the invalid email address list.**

An invalid email occurs when you attempt to send email to an address that is formatted in a manner that does not meet internet email format standards or the email does not exist at the recipients mail server.

Examples include addresses without the @ sign or addresses that include certain special characters and/or spaces. This response can come from our own server or the recipient mail server.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/invalid_emails.html).

### DELETE /suppression/invalid_emails/{email}


```php
$email = "test_url_param";
$response = $sg->client->suppression()->invalid_emails()->_($email)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a specific spam report

**This endpoint allows you to retrieve a specific spam report.**

[Spam reports](https://sendgrid.com/docs/Glossary/spam_reports.html) happen when a recipient indicates that they think your email is [spam](https://sendgrid.com/docs/Glossary/spam.html) and then their email provider reports this to SendGrid.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/spam_reports.html).

### GET /suppression/spam_report/{email}


```php
$email = "test_url_param";
$response = $sg->client->suppression()->spam_report()->_($email)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a specific spam report

**This endpoint allows you to delete a specific spam report.**

[Spam reports](https://sendgrid.com/docs/Glossary/spam_reports.html) happen when a recipient indicates that they think your email is [spam](https://sendgrid.com/docs/Glossary/spam.html) and then their email provider reports this to SendGrid.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/spam_reports.html).

### DELETE /suppression/spam_report/{email}


```php
$email = "test_url_param";
$response = $sg->client->suppression()->spam_report()->_($email)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all spam reports

**This endpoint allows you to retrieve all spam reports.**

[Spam reports](https://sendgrid.com/docs/Glossary/spam_reports.html) happen when a recipient indicates that they think your email is [spam](https://sendgrid.com/docs/Glossary/spam.html) and then their email provider reports this to SendGrid.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/spam_reports.html).

### GET /suppression/spam_reports


```php
$query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
$response = $sg->client->suppression()->spam_reports()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete spam reports

**This endpoint allows you to delete your spam reports.**

There are two options for deleting spam reports:

1) You can delete all spam reports by setting "delete_all" to true in the request body.
2) You can delete some spam reports by specifying the email addresses in an array in the request body.

[Spam reports](https://sendgrid.com/docs/Glossary/spam_reports.html) happen when a recipient indicates that they think your email is [spam](https://sendgrid.com/docs/Glossary/spam.html) and then their email provider reports this to SendGrid.

For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/spam_reports.html).

### DELETE /suppression/spam_reports


```php
$request_body = json_decode('{
  "delete_all": false,
  "emails": [
    "example1@example.com",
    "example2@example.com"
  ]
}');
$response = $sg->client->suppression()->spam_reports()->delete($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all global suppressions

**This endpoint allows you to retrieve a list of all email address that are globally suppressed.**

A global suppression (or global unsubscribe) is an email address of a recipient who does not want to receive any of your messages. A globally suppressed recipient will be removed from any email you send. For more information, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Suppressions/global_unsubscribes.html).

### GET /suppression/unsubscribes


```php
$query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
$response = $sg->client->suppression()->unsubscribes()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="templates"></a>
# TEMPLATES

## Create a transactional template.

**This endpoint allows you to create a transactional template.**

Each user can create up to 300 different transactional templates. Transactional templates are specific to accounts and subusers. Templates created on a parent account will not be accessible from the subuser accounts.

Transactional templates are templates created specifically for transactional email and are not to be confused with [Marketing Campaigns templates](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/templates.html). For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

### POST /templates


```php
$request_body = json_decode('{
  "name": "example_name"
}');
$response = $sg->client->templates()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all transactional templates.

**This endpoint allows you to retrieve all transactional templates.**

Each user can create up to 300 different transactional templates. Transactional templates are specific to accounts and subusers. Templates created on a parent account will not be accessible from the subuser accounts.

Transactional templates are templates created specifically for transactional email and are not to be confused with [Marketing Campaigns templates](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/templates.html). For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

### GET /templates


```php
$response = $sg->client->templates()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Edit a transactional template.

**This endpoint allows you to edit a transactional template.**

Each user can create up to 300 different transactional templates. Transactional templates are specific to accounts and subusers. Templates created on a parent account will not be accessible from the subuser accounts.

Transactional templates are templates created specifically for transactional email and are not to be confused with [Marketing Campaigns templates](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/templates.html). For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).


### PATCH /templates/{template_id}


```php
$request_body = json_decode('{
  "name": "new_example_name"
}');
$template_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a single transactional template.

**This endpoint allows you to retrieve a single transactional template.**

Each user can create up to 300 different transactional templates. Transactional templates are specific to accounts and subusers. Templates created on a parent account will not be accessible from the subuser accounts.

Transactional templates are templates created specifically for transactional email and are not to be confused with [Marketing Campaigns templates](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/templates.html). For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).


### GET /templates/{template_id}


```php
$template_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a template.

**This endpoint allows you to delete a transactional template.**

Each user can create up to 300 different transactional templates. Transactional templates are specific to accounts and subusers. Templates created on a parent account will not be accessible from the subuser accounts.

Transactional templates are templates created specifically for transactional email and are not to be confused with [Marketing Campaigns templates](https://sendgrid.com/docs/User_Guide/Marketing_Campaigns/templates.html). For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).


### DELETE /templates/{template_id}


```php
$template_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create a new transactional template version.

**This endpoint allows you to create a new version of a template.**

Each transactional template can have multiple versions, each version with its own subject and content. Each user can have up to 300 versions across across all templates.

For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).


### POST /templates/{template_id}/versions


```php
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
```
## Edit a transactional template version.

**This endpoint allows you to edit a version of one of your transactional templates.**

Each transactional template can have multiple versions, each version with its own subject and content. Each user can have up to 300 versions across across all templates.

For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

## URI Parameters
| URI Parameter | Type | Description |
|---|---|---|
| template_id | string | The ID of the original template |
| version_id | string | The ID of the template version |

### PATCH /templates/{template_id}/versions/{version_id}


```php
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
```
## Retrieve a specific transactional template version.

**This endpoint allows you to retrieve a specific version of a template.**

Each transactional template can have multiple versions, each version with its own subject and content. Each user can have up to 300 versions across across all templates.

For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

## URI Parameters
| URI Parameter | Type | Description |
|---|---|---|
| template_id | string | The ID of the original template |
| version_id | string |  The ID of the template version |

### GET /templates/{template_id}/versions/{version_id}


```php
$template_id = "test_url_param";
$version_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->_($version_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a transactional template version.

**This endpoint allows you to delete one of your transactional template versions.**

Each transactional template can have multiple versions, each version with its own subject and content. Each user can have up to 300 versions across across all templates.

For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

## URI Parameters
| URI Parameter | Type | Description |
|---|---|---|
| template_id | string | The ID of the original template |
| version_id | string | The ID of the template version |

### DELETE /templates/{template_id}/versions/{version_id}


```php
$template_id = "test_url_param";
$version_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->_($version_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Activate a transactional template version.

**This endpoint allows you to activate a version of one of your templates.**

Each transactional template can have multiple versions, each version with its own subject and content. Each user can have up to 300 versions across across all templates.


For more information about transactional templates, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Transactional_Templates/index.html).

## URI Parameters
| URI Parameter | Type | Description |
|---|---|---|
| template_id | string | The ID of the original template |
| version_id | string |  The ID of the template version |

### POST /templates/{template_id}/versions/{version_id}/activate


```php
$template_id = "test_url_param";
$version_id = "test_url_param";
$response = $sg->client->templates()->_($template_id)->versions()->_($version_id)->activate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="tracking_settings"></a>
# TRACKING SETTINGS

## Retrieve Tracking Settings

**This endpoint allows you to retrieve a list of all tracking settings that you can enable on your account.**

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### GET /tracking_settings


```php
$query_params = json_decode('{"limit": 1, "offset": 1}');
$response = $sg->client->tracking_settings()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Click Tracking Settings

**This endpoint allows you to change your current click tracking setting. You can enable, or disable, click tracking using this endpoint.**

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### PATCH /tracking_settings/click


```php
$request_body = json_decode('{
  "enabled": true
}');
$response = $sg->client->tracking_settings()->click()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve Click Track Settings

**This endpoint allows you to retrieve your current click tracking setting.**

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### GET /tracking_settings/click


```php
$response = $sg->client->tracking_settings()->click()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Google Analytics Settings

**This endpoint allows you to update your current setting for Google Analytics.**

For more information about using Google Analytics, please refer to [Googles URL Builder](https://support.google.com/analytics/answer/1033867?hl=en) and their article on ["Best Practices for Campaign Building"](https://support.google.com/analytics/answer/1037445).

We default the settings to Googles recommendations. For more information, see [Google Analytics Demystified](https://sendgrid.com/docs/Classroom/Track/Collecting_Data/google_analytics_demystified_ga_statistics_vs_sg_statistics.html).

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### PATCH /tracking_settings/google_analytics


```php
$request_body = json_decode('{
  "enabled": true,
  "utm_campaign": "website",
  "utm_content": "",
  "utm_medium": "email",
  "utm_source": "sendgrid.com",
  "utm_term": ""
}');
$response = $sg->client->tracking_settings()->google_analytics()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve Google Analytics Settings

**This endpoint allows you to retrieve your current setting for Google Analytics.**

For more information about using Google Analytics, please refer to [Googles URL Builder](https://support.google.com/analytics/answer/1033867?hl=en) and their article on ["Best Practices for Campaign Building"](https://support.google.com/analytics/answer/1037445).

We default the settings to Googles recommendations. For more information, see [Google Analytics Demystified](https://sendgrid.com/docs/Classroom/Track/Collecting_Data/google_analytics_demystified_ga_statistics_vs_sg_statistics.html).

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### GET /tracking_settings/google_analytics


```php
$response = $sg->client->tracking_settings()->google_analytics()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Open Tracking Settings

**This endpoint allows you to update your current settings for open tracking.**

Open Tracking adds an invisible image at the end of the email which can track email opens. If the email recipient has images enabled on their email client, a request to SendGrids server for the invisible image is executed and an open event is logged. These events are logged in the Statistics portal, Email Activity interface, and are reported by the Event Webhook.

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### PATCH /tracking_settings/open


```php
$request_body = json_decode('{
  "enabled": true
}');
$response = $sg->client->tracking_settings()->open()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Get Open Tracking Settings

**This endpoint allows you to retrieve your current settings for open tracking.**

Open Tracking adds an invisible image at the end of the email which can track email opens. If the email recipient has images enabled on their email client, a request to SendGrids server for the invisible image is executed and an open event is logged. These events are logged in the Statistics portal, Email Activity interface, and are reported by the Event Webhook.

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### GET /tracking_settings/open


```php
$response = $sg->client->tracking_settings()->open()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Subscription Tracking Settings

**This endpoint allows you to update your current settings for subscription tracking.**

Subscription tracking adds links to the bottom of your emails that allows your recipients to subscribe to, or unsubscribe from, your emails.

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### PATCH /tracking_settings/subscription


```php
$request_body = json_decode('{
  "enabled": true,
  "html_content": "html content",
  "landing": "landing page html",
  "plain_content": "text content",
  "replace": "replacement tag",
  "url": "url"
}');
$response = $sg->client->tracking_settings()->subscription()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve Subscription Tracking Settings

**This endpoint allows you to retrieve your current settings for subscription tracking.**

Subscription tracking adds links to the bottom of your emails that allows your recipients to subscribe to, or unsubscribe from, your emails.

You can track a variety of the actions your recipients may take when interacting with your emails including opening your emails, clicking on links in your emails, and subscribing to (or unsubscribing from) your emails.

For more information about tracking, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/tracking.html).

### GET /tracking_settings/subscription


```php
$response = $sg->client->tracking_settings()->subscription()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="user"></a>
# USER

## Get a user's account information.

**This endpoint allows you to retrieve your user account details.**

Your user's account information includes the user's account type and reputation.

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

### GET /user/account


```php
$response = $sg->client->user()->account()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve your credit balance

**This endpoint allows you to retrieve the current credit balance for your account.**

Your monthly credit allotment limits the number of emails you may send before incurring overage charges. For more information about credits and billing, please visit our [Clssroom](https://sendgrid.com/docs/Classroom/Basics/Billing/billing_info_and_faqs.html).

### GET /user/credits


```php
$response = $sg->client->user()->credits()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update your account email address

**This endpoint allows you to update the email address currently on file for your account.**

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

### PUT /user/email


```php
$request_body = json_decode('{
  "email": "example@example.com"
}');
$response = $sg->client->user()->email()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve your account email address

**This endpoint allows you to retrieve the email address currently on file for your account.**

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

### GET /user/email


```php
$response = $sg->client->user()->email()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update your password

**This endpoint allows you to update your password.**

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

### PUT /user/password


```php
$request_body = json_decode('{
  "new_password": "new_password",
  "old_password": "old_password"
}');
$response = $sg->client->user()->password()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a user's profile

**This endpoint allows you to update your current profile details.**

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

It should be noted that any one or more of the parameters can be updated via the PATCH /user/profile endpoint. The only requirement is that you include at least one when you PATCH.

### PATCH /user/profile


```php
$request_body = json_decode('{
  "city": "Orange",
  "first_name": "Example",
  "last_name": "User"
}');
$response = $sg->client->user()->profile()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Get a user's profile

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

### GET /user/profile


```php
$response = $sg->client->user()->profile()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Cancel or pause a scheduled send

**This endpoint allows you to cancel or pause an email that has been scheduled to be sent.**

If the maximum number of cancellations/pauses are added, HTTP 400 will
be returned.

The Cancel Scheduled Sends feature allows the customer to cancel a scheduled send based on a Batch ID included in the SMTPAPI header.Scheduled sends cancelled less than 10 minutes before the scheduled time are not guaranteed to be cancelled.

### POST /user/scheduled_sends


```php
$request_body = json_decode('{
  "batch_id": "YOUR_BATCH_ID",
  "status": "pause"
}');
$response = $sg->client->user()->scheduled_sends()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all scheduled sends

**This endpoint allows you to retrieve all cancel/paused scheduled send information.**

The Cancel Scheduled Sends feature allows the customer to cancel a scheduled send based on a Batch ID included in the SMTPAPI header.Scheduled sends cancelled less than 10 minutes before the scheduled time are not guaranteed to be cancelled.

### GET /user/scheduled_sends


```php
$response = $sg->client->user()->scheduled_sends()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update user scheduled send information

**This endpoint allows you to update the status of a scheduled send for the given `batch_id`.**

The Cancel Scheduled Sends feature allows the customer to cancel a scheduled send based on a Batch ID included in the SMTPAPI header.Scheduled sends cancelled less than 10 minutes before the scheduled time are not guaranteed to be cancelled.

### PATCH /user/scheduled_sends/{batch_id}


```php
$request_body = json_decode('{
  "status": "pause"
}');
$batch_id = "test_url_param";
$response = $sg->client->user()->scheduled_sends()->_($batch_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve scheduled send

**This endpoint allows you to retrieve the cancel/paused scheduled send information for a specific `batch_id`.**

The Cancel Scheduled Sends feature allows the customer to cancel a scheduled send based on a Batch ID included in the SMTPAPI header.Scheduled sends cancelled less than 10 minutes before the scheduled time are not guaranteed to be cancelled.

### GET /user/scheduled_sends/{batch_id}


```php
$batch_id = "test_url_param";
$response = $sg->client->user()->scheduled_sends()->_($batch_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a cancellation or pause of a scheduled send

**This endpoint allows you to delete the cancellation/pause of a scheduled send.**

The Cancel Scheduled Sends feature allows the customer to cancel a scheduled send based on a Batch ID included in the SMTPAPI header.Scheduled sends cancelled less than 10 minutes before the scheduled time are not guaranteed to be cancelled.

### DELETE /user/scheduled_sends/{batch_id}


```php
$batch_id = "test_url_param";
$response = $sg->client->user()->scheduled_sends()->_($batch_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Enforced TLS settings

**This endpoint allows you to update your current Enforced TLS settings.**

The Enforced TLS settings specify whether or not the recipient is required to support TLS or have a valid certificate. See the [SMTP Ports User Guide](https://sendgrid.com/docs/Classroom/Basics/Email_Infrastructure/smtp_ports.html) for more information on opportunistic TLS.

**Note:** If either setting is enabled and the recipient does not support TLS or have a valid certificate, we drop the message and send a block event with TLS required but not supported as the description.

### PATCH /user/settings/enforced_tls


```php
$request_body = json_decode('{
  "require_tls": true,
  "require_valid_cert": false
}');
$response = $sg->client->user()->settings()->enforced_tls()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve current Enforced TLS settings.

**This endpoint allows you to retrieve your current Enforced TLS settings.**

The Enforced TLS settings specify whether or not the recipient is required to support TLS or have a valid certificate. See the [SMTP Ports User Guide](https://sendgrid.com/docs/Classroom/Basics/Email_Infrastructure/smtp_ports.html) for more information on opportunistic TLS.

**Note:** If either setting is enabled and the recipient does not support TLS or have a valid certificate, we drop the message and send a block event with TLS required but not supported as the description.

### GET /user/settings/enforced_tls


```php
$response = $sg->client->user()->settings()->enforced_tls()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update your username

**This endpoint allows you to update the username for your account.**

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

### PUT /user/username


```php
$request_body = json_decode('{
  "username": "test_username"
}');
$response = $sg->client->user()->username()->put($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve your username

**This endpoint allows you to retrieve your current account username.**

Keeping your user profile up to date is important. This will help SendGrid to verify who you are as well as contact you should we need to.

For more information about your user profile:

* [SendGrid Account Settings](https://sendgrid.com/docs/User_Guide/Settings/account.html)

### GET /user/username


```php
$response = $sg->client->user()->username()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update Event Notification Settings

**This endpoint allows you to update your current event webhook settings.**

If an event type is marked as `true`, then the event webhook will include information about that event.

SendGrids Event Webhook will notify a URL of your choice via HTTP POST with information about events that occur as SendGrid processes your email.

Common uses of this data are to remove unsubscribes, react to spam reports, determine unengaged recipients, identify bounced email addresses, or create advanced analytics of your email program.

### PATCH /user/webhooks/event/settings


```php
$request_body = json_decode('{
  "bounce": true,
  "click": true,
  "deferred": true,
  "delivered": true,
  "dropped": true,
  "enabled": true,
  "group_resubscribe": true,
  "group_unsubscribe": true,
  "open": true,
  "processed": true,
  "spam_report": true,
  "unsubscribe": true,
  "url": "url"
}');
$response = $sg->client->user()->webhooks()->event()->settings()->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve Event Webhook settings

**This endpoint allows you to retrieve your current event webhook settings.**

If an event type is marked as `true`, then the event webhook will include information about that event.

SendGrids Event Webhook will notify a URL of your choice via HTTP POST with information about events that occur as SendGrid processes your email.

Common uses of this data are to remove unsubscribes, react to spam reports, determine unengaged recipients, identify bounced email addresses, or create advanced analytics of your email program.

### GET /user/webhooks/event/settings


```php
$response = $sg->client->user()->webhooks()->event()->settings()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Test Event Notification Settings

**This endpoint allows you to test your event webhook by sending a fake event notification post to the provided URL.**

SendGrids Event Webhook will notify a URL of your choice via HTTP POST with information about events that occur as SendGrid processes your email.

Common uses of this data are to remove unsubscribes, react to spam reports, determine unengaged recipients, identify bounced email addresses, or create advanced analytics of your email program.

### POST /user/webhooks/event/test


```php
$request_body = json_decode('{
  "url": "url"
}');
$response = $sg->client->user()->webhooks()->event()->test()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create a parse setting

**This endpoint allows you to create a new inbound parse setting.**

The inbound parse webhook allows you to have incoming emails parsed, extracting some or all of the content, and then have that content POSTed by SendGrid to a URL of your choosing. For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Webhooks/parse.html).

### POST /user/webhooks/parse/settings


```php
$request_body = json_decode('{
  "hostname": "myhostname.com",
  "send_raw": false,
  "spam_check": true,
  "url": "http://email.myhosthame.com"
}');
$response = $sg->client->user()->webhooks()->parse()->settings()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all parse settings

**This endpoint allows you to retrieve all of your current inbound parse settings.**

The inbound parse webhook allows you to have incoming emails parsed, extracting some or all of the contnet, and then have that content POSTed by SendGrid to a URL of your choosing. For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Webhooks/parse.html).

### GET /user/webhooks/parse/settings


```php
$response = $sg->client->user()->webhooks()->parse()->settings()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a parse setting

**This endpoint allows you to update a specific inbound parse setting.**

The inbound parse webhook allows you to have incoming emails parsed, extracting some or all of the contnet, and then have that content POSTed by SendGrid to a URL of your choosing. For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Webhooks/parse.html).

### PATCH /user/webhooks/parse/settings/{hostname}


```php
$request_body = json_decode('{
  "send_raw": true,
  "spam_check": false,
  "url": "http://newdomain.com/parse"
}');
$hostname = "test_url_param";
$response = $sg->client->user()->webhooks()->parse()->settings()->_($hostname)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a specific parse setting

**This endpoint allows you to retrieve a specific inbound parse setting.**

The inbound parse webhook allows you to have incoming emails parsed, extracting some or all of the contnet, and then have that content POSTed by SendGrid to a URL of your choosing. For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Webhooks/parse.html).

### GET /user/webhooks/parse/settings/{hostname}


```php
$hostname = "test_url_param";
$response = $sg->client->user()->webhooks()->parse()->settings()->_($hostname)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a parse setting

**This endpoint allows you to delete a specific inbound parse setting.**

The inbound parse webhook allows you to have incoming emails parsed, extracting some or all of the contnet, and then have that content POSTed by SendGrid to a URL of your choosing. For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Webhooks/parse.html).

### DELETE /user/webhooks/parse/settings/{hostname}


```php
$hostname = "test_url_param";
$response = $sg->client->user()->webhooks()->parse()->settings()->_($hostname)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieves Inbound Parse Webhook statistics.

**This endpoint allows you to retrieve the statistics for your Parse Webhook useage.**

SendGrid's Inbound Parse Webhook allows you to parse the contents and attachments of incomming emails. The Parse API can then POST the parsed emails to a URL that you specify. The Inbound Parse Webhook cannot parse messages greater than 20MB in size, including all attachments.

There are a number of pre-made integrations for the SendGrid Parse Webhook which make processing events easy. You can find these integrations in the [Library Index](https://sendgrid.com/docs/Integrate/libraries.html#-Webhook-Libraries).

### GET /user/webhooks/parse/stats


```php
$query_params = json_decode('{"aggregated_by": "day", "limit": "test_string", "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": "test_string"}');
$response = $sg->client->user()->webhooks()->parse()->stats()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
<a name="whitelabel"></a>
# WHITELABEL

## Create a domain whitelabel.

**This endpoint allows you to create a whitelabel for one of your domains.**

If you are creating a domain whitelabel that you would like a subuser to use, you have two options:
1. Use the "username" parameter. This allows you to create a whitelabel on behalf of your subuser. This means the subuser is able to see and modify the created whitelabel.
2. Use the Association workflow (see Associate Domain section). This allows you to assign a whitelabel created by the parent to a subuser. This means the subuser will default to the assigned whitelabel, but will not be able to see or modify that whitelabel. However, if the subuser creates their own whitelabel it will overwrite the assigned whitelabel.

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

### POST /whitelabel/domains


```php
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
```
## List all domain whitelabels.

**This endpoint allows you to retrieve a list of all domain whitelabels you have created.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)


### GET /whitelabel/domains


```php
$query_params = json_decode('{"username": "test_string", "domain": "test_string", "exclude_subusers": "true", "limit": 1, "offset": 1}');
$response = $sg->client->whitelabel()->domains()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Get the default domain whitelabel.

**This endpoint allows you to retrieve the default whitelabel for a domain.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

## URI Parameters
| URI Parameter   | Type   | Description  |
|---|---|---|
| domain | string  |The domain to find a default domain whitelabel for. |

### GET /whitelabel/domains/default


```php
$response = $sg->client->whitelabel()->domains()->default()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## List the domain whitelabel associated with the given user.

**This endpoint allows you to retrieve all of the whitelabels that have been assigned to a specific subuser.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

Domain whitelabels can be associated with (i.e. assigned to) subusers from a parent account. This functionality allows subusers to send mail using their parent's whitelabels. To associate a whitelabel with a subuser, the parent account must first create the whitelabel and validate it. The the parent may then associate the whitelabel via the subuser management tools.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

## URI Parameters
| URI Parameter   | Type  | Description  |
|---|---|---|
| username | string  | Username of the subuser to find associated whitelabels for. |

### GET /whitelabel/domains/subuser


```php
$response = $sg->client->whitelabel()->domains()->subuser()->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Disassociate a domain whitelabel from a given user.

**This endpoint allows you to disassociate a specific whitelabel from a subuser.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

Domain whitelabels can be associated with (i.e. assigned to) subusers from a parent account. This functionality allows subusers to send mail using their parent's whitelabels. To associate a whitelabel with a subuser, the parent account must first create the whitelabel and validate it. The the parent may then associate the whitelabel via the subuser management tools.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

## URI Parameters
| URI Parameter   | Type  | Required?  | Description  |
|---|---|---|---|
| username | string  | required  | Username for the subuser to find associated whitelabels for. |

### DELETE /whitelabel/domains/subuser


```php
$response = $sg->client->whitelabel()->domains()->subuser()->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a domain whitelabel.

**This endpoint allows you to update the settings for a domain whitelabel.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

### PATCH /whitelabel/domains/{domain_id}


```php
$request_body = json_decode('{
  "custom_spf": true,
  "default": false
}');
$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a domain whitelabel.

**This endpoint allows you to retrieve a specific domain whitelabel.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)


### GET /whitelabel/domains/{domain_id}


```php
$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a domain whitelabel.

**This endpoint allows you to delete a domain whitelabel.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

### DELETE /whitelabel/domains/{domain_id}


```php
$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Associate a domain whitelabel with a given user.

**This endpoint allows you to associate a specific domain whitelabel with a subuser.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

Domain whitelabels can be associated with (i.e. assigned to) subusers from a parent account. This functionality allows subusers to send mail using their parent's whitelabels. To associate a whitelabel with a subuser, the parent account must first create the whitelabel and validate it. The the parent may then associate the whitelabel via the subuser management tools.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

## URI Parameters
| URI Parameter   | Type   | Description  |
|---|---|---|
| domain_id | integer   | ID of the domain whitelabel to associate with the subuser. |

### POST /whitelabel/domains/{domain_id}/subuser


```php
$request_body = json_decode('{
  "username": "jane@example.com"
}');
$domain_id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($domain_id)->subuser()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Add an IP to a domain whitelabel.

**This endpoint allows you to add an IP address to a domain whitelabel.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

## URI Parameters
| URI Parameter   | Type  |  Description  |
|---|---|---|
| id | integer  | ID of the domain to which you are adding an IP |

### POST /whitelabel/domains/{id}/ips


```php
$request_body = json_decode('{
  "ip": "192.168.0.1"
}');
$id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($id)->ips()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Remove an IP from a domain whitelabel.

**This endpoint allows you to remove a domain's IP address from that domain's whitelabel.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

## URI Parameters
| URI Parameter   | Type  | Description  |
|---|---|---|
| id | integer  | ID of the domain whitelabel to delete the IP from. |
| ip | string | IP to remove from the domain whitelabel. |

### DELETE /whitelabel/domains/{id}/ips/{ip}


```php
$id = "test_url_param";
$ip = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($id)->ips()->_($ip)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Validate a domain whitelabel.

**This endpoint allows you to validate a domain whitelabel. If it fails, it will return an error message describing why the whitelabel could not be validated.**

A domain whitelabel allows you to remove the via or sent on behalf of message that your recipients see when they read your emails. Whitelabeling a domain allows you to replace sendgrid.net with your personal sending domain. You will be required to create a subdomain so that SendGrid can generate the DNS records which you must give to your host provider. If you choose to use Automated Security, SendGrid will provide you with 3 CNAME records. If you turn Automated Security off, you will be given 2 TXT records and 1 MX record.

For more information on whitelabeling, please see our [User Guide](https://sendgrid.com/docs/User_Guide/Settings/Whitelabel/index.html)

## URI Parameters
| URI Parameter   | Type   | Description  |
|---|---|---|
| id | integer  |ID of the domain whitelabel to validate. |

### POST /whitelabel/domains/{id}/validate


```php
$id = "test_url_param";
$response = $sg->client->whitelabel()->domains()->_($id)->validate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create an IP whitelabel

**This endpoint allows you to create an IP whitelabel.**

When creating an IP whitelable, you should use the same subdomain that you used when you created a domain whitelabel.

A IP whitelabel consists of a subdomain and domain that will be used to generate a reverse DNS record for a given IP. Once SendGrid has verified that the appropriate A record for the IP has been created, the appropriate reverse DNS record for the IP is generated.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/ips.html).

### POST /whitelabel/ips


```php
$request_body = json_decode('{
  "domain": "example.com",
  "ip": "192.168.1.1",
  "subdomain": "email"
}');
$response = $sg->client->whitelabel()->ips()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve all IP whitelabels

**This endpoint allows you to retrieve all of the IP whitelabels that have been createdy by this account.**

You may include a search key by using the "ip" parameter. This enables you to perform a prefix search for a given IP segment (e.g. "192.").

A IP whitelabel consists of a subdomain and domain that will be used to generate a reverse DNS record for a given IP. Once SendGrid has verified that the appropriate A record for the IP has been created, the appropriate reverse DNS record for the IP is generated.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/ips.html).

### GET /whitelabel/ips


```php
$query_params = json_decode('{"ip": "test_string", "limit": 1, "offset": 1}');
$response = $sg->client->whitelabel()->ips()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve an IP whitelabel

**This endpoint allows you to retrieve an IP whitelabel.**

A IP whitelabel consists of a subdomain and domain that will be used to generate a reverse DNS record for a given IP. Once SendGrid has verified that the appropriate A record for the IP has been created, the appropriate reverse DNS record for the IP is generated.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/ips.html).

### GET /whitelabel/ips/{id}


```php
$id = "test_url_param";
$response = $sg->client->whitelabel()->ips()->_($id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete an IP whitelabel

**This endpoint allows you to delete an IP whitelabel.**

A IP whitelabel consists of a subdomain and domain that will be used to generate a reverse DNS record for a given IP. Once SendGrid has verified that the appropriate A record for the IP has been created, the appropriate reverse DNS record for the IP is generated.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/ips.html).

### DELETE /whitelabel/ips/{id}


```php
$id = "test_url_param";
$response = $sg->client->whitelabel()->ips()->_($id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Validate an IP whitelabel

**This endpoint allows you to validate an IP whitelabel.**

A IP whitelabel consists of a subdomain and domain that will be used to generate a reverse DNS record for a given IP. Once SendGrid has verified that the appropriate A record for the IP has been created, the appropriate reverse DNS record for the IP is generated.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/ips.html).

### POST /whitelabel/ips/{id}/validate


```php
$id = "test_url_param";
$response = $sg->client->whitelabel()->ips()->_($id)->validate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Create a Link Whitelabel

**This endpoint allows you to create a new link whitelabel.**

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### POST /whitelabel/links


```php
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
```
## Retrieve all link whitelabels

**This endpoint allows you to retrieve all link whitelabels.**

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### GET /whitelabel/links


```php
$query_params = json_decode('{"limit": 1}');
$response = $sg->client->whitelabel()->links()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a Default Link Whitelabel

**This endpoint allows you to retrieve the default link whitelabel.**

Default link whitelabel is the actual link whitelabel to be used when sending messages. If there are multiple link whitelabels, the default is determined by the following order:
<ul>
  <li>Validated link whitelabels marked as "default"</li>
  <li>Legacy link whitelabels (migrated from the whitelabel wizard)</li>
  <li>Default SendGrid link whitelabel (i.e. 100.ct.sendgrid.net)</li>
</ul>

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### GET /whitelabel/links/default


```php
$query_params = json_decode('{"domain": "test_string"}');
$response = $sg->client->whitelabel()->links()->default()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve Associated Link Whitelabel

**This endpoint allows you to retrieve the associated link whitelabel for a subuser.**

Link whitelables can be associated with subusers from the parent account. This functionality allows
subusers to send mail using their parent's linke whitelabels. To associate a link whitelabel, the parent account
must first create a whitelabel and validate it. The parent may then associate that whitelabel with a subuser via the API or the Subuser Management page in the user interface.

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### GET /whitelabel/links/subuser


```php
$query_params = json_decode('{"username": "test_string"}');
$response = $sg->client->whitelabel()->links()->subuser()->get(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Disassociate a Link Whitelabel

**This endpoint allows you to disassociate a link whitelabel from a subuser.**

Link whitelables can be associated with subusers from the parent account. This functionality allows
subusers to send mail using their parent's linke whitelabels. To associate a link whitelabel, the parent account
must first create a whitelabel and validate it. The parent may then associate that whitelabel with a subuser via the API or the Subuser Management page in the user interface.

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### DELETE /whitelabel/links/subuser


```php
$query_params = json_decode('{"username": "test_string"}');
$response = $sg->client->whitelabel()->links()->subuser()->delete(null, $query_params);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Update a Link Whitelabel

**This endpoint allows you to update a specific link whitelabel. You can use this endpoint to change a link whitelabel's default status.**

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### PATCH /whitelabel/links/{id}


```php
$request_body = json_decode('{
  "default": true
}');
$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->patch($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Retrieve a Link Whitelabel

**This endpoint allows you to retrieve a specific link whitelabel.**

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### GET /whitelabel/links/{id}


```php
$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->get();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Delete a Link Whitelabel

**This endpoint allows you to delete a link whitelabel.**

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### DELETE /whitelabel/links/{id}


```php
$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->delete();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Validate a Link Whitelabel

**This endpoint allows you to validate a link whitelabel.**

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### POST /whitelabel/links/{id}/validate


```php
$id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($id)->validate()->post();
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```
## Associate a Link Whitelabel

**This endpoint allows you to associate a link whitelabel with a subuser account.**

Link whitelables can be associated with subusers from the parent account. This functionality allows
subusers to send mail using their parent's linke whitelabels. To associate a link whitelabel, the parent account
must first create a whitelabel and validate it. The parent may then associate that whitelabel with a subuser via the API or the Subuser Management page in the user interface.

Email link whitelabels allow all of the click-tracked links you send in your emails to include the URL of your domain instead of sendgrid.net.

For more information, please see our [User Guide](https://sendgrid.com/docs/API_Reference/Web_API_v3/Whitelabel/links.html).

### POST /whitelabel/links/{link_id}/subuser


```php
$request_body = json_decode('{
  "username": "jane@example.com"
}');
$link_id = "test_url_param";
$response = $sg->client->whitelabel()->links()->_($link_id)->subuser()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```

