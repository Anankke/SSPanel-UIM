[![BuildStatus](https://travis-ci.org/sendgrid/sendgrid-php.svg?branch=master)](https://travis-ci.org/sendgrid/sendgrid-php)

Please see our announcement regarding [breaking changes](https://github.com/sendgrid/sendgrid-php/issues/290). Your support is appreciated!

**This library allows you to quickly and easily use the SendGrid Web API v3 via PHP.**

Version 5.X.X of this library provides full support for all SendGrid [Web API v3](https://sendgrid.com/docs/API_Reference/Web_API_v3/index.html) endpoints, including the new [v3 /mail/send](https://sendgrid.com/blog/introducing-v3mailsend-sendgrids-new-mail-endpoint).

This library represents the beginning of a new path for SendGrid. We want this library to be community driven and SendGrid led. We need your help to realize this goal. To help make sure we are building the right things in the right order, we ask that you create [issues](https://github.com/sendgrid/sendgrid-php/issues) and [pull requests](https://github.com/sendgrid/sendgrid-php/blob/master/CONTRIBUTING.md) or simply upvote or comment on existing issues or pull requests.

Please browse the rest of this README for further detail.

We appreciate your continued support, thank you!

# Table of Contents

* [Installation](#installation)
* [Quick Start](#quick_start)
* [Usage](#usage)
* [Use Cases](#use_cases)
* [Announcements](#announcements)
* [Roadmap](#roadmap)
* [How to Contribute](#contribute)
* [Troubleshooting](#troubleshooting)
* [About](#about)

<a name="installation"></a>
# Installation

## Prerequisites

- PHP version 5.6 or 7.0
- The SendGrid service, starting at the [free level](https://sendgrid.com/free?source=sendgrid-php)

## Setup Environment Variables

Update the development environment with your [SENDGRID_API_KEY](https://app.sendgrid.com/settings/api_keys), for example:

```bash
echo "export SENDGRID_API_KEY='YOUR_API_KEY'" > sendgrid.env
echo "sendgrid.env" >> .gitignore
source ./sendgrid.env
```

## Install Package

Add SendGrid to your `composer.json` file. If you are not using [Composer](http://getcomposer.org), you should be. It's an excellent way to manage dependencies in your PHP application.

```json
{
  "require": {
    "sendgrid/sendgrid": "~5.1"
  }
}
```

Then at the top of your PHP script require the autoloader:

```bash
require 'vendor/autoload.php';
```

#### Alternative: Install package from zip

If you are not using Composer, simply download and install the **[latest packaged release of the library as a zip](https://sendgrid-open-source.s3.amazonaws.com/sendgrid-php/sendgrid-php.zip)**.

[**⬇︎ Download Packaged Library ⬇︎**](https://sendgrid-open-source.s3.amazonaws.com/sendgrid-php/sendgrid-php.zip)

Then require the library from package:

```php
require("path/to/sendgrid-php/sendgrid-php.php");
```

Previous versions of the library can be found in the [version index](https://sendgrid-open-source.s3.amazonaws.com/index.html).

## Dependencies

- The SendGrid Service, starting at the [free level](https://sendgrid.com/free?source=sendgrid-php)
- [php-HTTP-Client](https://github.com/sendgrid/php-http-client)

<a name="quick_start"></a>
# Quick Start

## Hello Email

The following is the minimum needed code to send an email with the [/mail/send Helper](https://github.com/sendgrid/sendgrid-php/tree/master/lib/helpers/mail) ([here](https://github.com/sendgrid/sendgrid-php/blob/master/examples/helpers/mail/example.php#L22) is a full example):

```php
<?php
// If you are using Composer (recommended)
require 'vendor/autoload.php';

// If you are not using Composer
// require("path/to/sendgrid-php/sendgrid-php.php");

$from = new SendGrid\Email(null, "test@example.com");
$subject = "Hello World from the SendGrid PHP Library!";
$to = new SendGrid\Email(null, "test@example.com");
$content = new SendGrid\Content("text/plain", "Hello, Email!");
$mail = new SendGrid\Mail($from, $subject, $to, $content);

$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
echo $response->headers();
echo $response->body();
```

The `SendGrid\Mail` constructor creates a [personalization object](https://sendgrid.com/docs/Classroom/Send/v3_Mail_Send/personalizations.html) for you. [Here](https://github.com/sendgrid/sendgrid-php/blob/master/examples/helpers/mail/example.php#L16) is an example of how to add to it.

### Without Mail Helper Class

The following is the minimum needed code to send an email without the /mail/send Helper ([here](https://github.com/sendgrid/sendgrid-php/blob/master/examples/mail/mail.php#L28) is a full example):

```php
<?php
// If you are using Composer (recommended)
require 'vendor/autoload.php';

// If you are not using Composer
// require("path/to/sendgrid-php/sendgrid-php.php");

$request_body = json_decode('{
  "personalizations": [
    {
      "to": [
        {
          "email": "test@example.com"
        }
      ],
      "subject": "Hello World from the SendGrid PHP Library!"
    }
  ],
  "from": {
    "email": "test@example.com"
  },
  "content": [
    {
      "type": "text/plain",
      "value": "Hello, Email!"
    }
  ]
}');

$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

$response = $sg->client->mail()->send()->post($request_body);
echo $response->statusCode();
echo $response->body();
echo $response->headers();
```

## General v3 Web API Usage (With Fluent Interface)

```php
<?php
// If you are using Composer (recommended)
require 'vendor/autoload.php';

// If you are not using Composer
// require("path/to/sendgrid-php/sendgrid-php.php");

$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

$response = $sg->client->suppressions()->bounces()->get();

print $response->statusCode();
print $response->headers();
print $response->body();
```

## General v3 Web API Usage (Without Fluent Interface)

```php
<?php
// If you are using Composer (recommended)
require 'vendor/autoload.php';

// If you are not using Composer
// require("path/to/sendgrid-php/sendgrid-php.php");

$apiKey = getenv('SENDGRID_API_KEY');
$sg = new \SendGrid($apiKey);

$response = $sg->client->_("suppression/bounces")->get();

print $response->statusCode();
print $response->headers();
print $response->body();
```

<a name="usage"></a>
# Usage

- [SendGrid Docs](https://sendgrid.com/docs/API_Reference/index.html)
- [Library Usage
    Documentation](https://github.com/sendgrid/sendgrid-php/tree/master/USAGE.md)
- [Example Code](https://github.com/sendgrid/sendgrid-php/tree/master/examples)
- [How-to: Migration from v2 to v3](https://sendgrid.com/docs/Classroom/Send/v3_Mail_Send/how_to_migrate_from_v2_to_v3_mail_send.html)
- [v3 Web API Mail Send Helper](https://github.com/sendgrid/sendgrid-php/tree/master/lib/helpers/mail/README.md) - build a request object payload for a v3 /mail/send API call.

<a name="use_cases">
# Use Cases

[Examples of common API use cases](https://github.com/sendgrid/sendgrid-php/blob/master/USE_CASES.md), such as how to send an email with a transactional template.

<a name="announcements"></a>
# Announcements

Please see our announcement regarding [breaking changes](https://github.com/sendgrid/sendgrid-php/issues/290). Your support is appreciated!

All updates to this library is documented in our [CHANGELOG](https://github.com/sendgrid/sendgrid-php/blob/master/CHANGELOG.md) and [releases](https://github.com/sendgrid/sendgrid-php/releases)

<a name="roadmap"></a>
# Roadmap

If you are interested in the future direction of this project, please take a look at our open [issues](https://github.com/sendgrid/sendgrid-php/issues) and [pull requests](https://github.com/sendgrid/sendgrid-php/pulls). We would love to hear your feedback.

<a name="contribute"></a>
# How to Contribute

We encourage contribution to our libraries (you might even score some nifty swag), please see our [CONTRIBUTING](https://github.com/sendgrid/sendgrid-php/blob/master/CONTRIBUTING.md) guide for details.

Quick links:

- [Feature Request](https://github.com/sendgrid/sendgrid-php/blob/master/CONTRIBUTING.md#feature_request)
- [Bug Reports](https://github.com/sendgrid/sendgrid-php/blob/master/CONTRIBUTING.md#submit_a_bug_report)
- [Sign the CLA to Create a Pull Request](https://github.com/sendgrid/sendgrid-php/blob/master/CONTRIBUTING.md#cla)
- [Improvements to the Codebase](https://github.com/sendgrid/sendgrid-php/blob/master/CONTRIBUTING.md#improvements_to_the_codebase)

<a name="troubleshooting"></a>
# Troubleshooting

Please see our [troubleshooting guide](https://github.com/sendgrid/sendgrid-php/blob/master/TROUBLESHOOTING.md) for common library issues.

<a name="about"></a>
# About

sendgrid-php is guided and supported by the SendGrid [Developer Experience Team](mailto:dx@sendgrid.com).

sendgrid-php is maintained and funded by SendGrid, Inc. The names and logos for sendgrid-php are trademarks of SendGrid, Inc.

![SendGrid Logo]
(https://uiux.s3.amazonaws.com/2016-logos/email-logo%402x.png)

