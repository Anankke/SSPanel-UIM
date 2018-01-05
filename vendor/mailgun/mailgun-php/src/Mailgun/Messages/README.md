Mailgun - Messages
====================

This is the Mailgun PHP *Message* utilities. 

The below assumes you've already installed the Mailgun PHP SDK in to your 
project. If not, go back to the master README for instructions.

There are two utilities included, Message Builder and Batch Message. 

Message Builder: Allows you to build a message object by calling methods for 
each MIME attribute. 
Batch Message: Inherits Message Builder and allows you to iterate through 
recipients from a list. Messages will fire after the 1,000th recipient has been 
added. 

Usage - Message Builder
-----------------------
Here's how to use Message Builder to build your Message. 

```php
# First, instantiate the SDK with your API credentials and define your domain. 
$mg = new Mailgun("key-example");
$domain = "example.com";

# Next, instantiate a Message Builder object from the SDK.
$messageBldr = $mg->MessageBuilder();

# Define the from address.
$messageBldr->setFromAddress("me@example.com", array("first"=>"PHP", "last" => "SDK"));
# Define a to recipient.
$messageBldr->addToRecipient("john.doe@example.com", array("first" => "John", "last" => "Doe"));
# Define a cc recipient.
$messageBldr->addCcRecipient("sally.doe@example.com", array("first" => "Sally", "last" => "Doe"));
# Define the subject. 
$messageBldr->setSubject("A message from the PHP SDK using Message Builder!");
# Define the body of the message.
$messageBldr->setTextBody("This is the text body of the message!");

# Other Optional Parameters.
$messageBldr->addCampaignId("My-Awesome-Campaign");
$messageBldr->addCustomHeader("Customer-Id", "12345");
$messageBldr->addAttachment("@/tron.jpg");
$messageBldr->setDeliveryTime("tomorrow 8:00AM", "PST");
$messageBldr->setClickTracking(true);

# Finally, send the message.
$mg->post("{$domain}/messages", $messageBldr->getMessage(), $messageBldr->getFiles());
```

Available Functions
-----------------------------------------------------

`string addToRecipient(string $address, array $attributes)` 

`string addCcRecipient(string $address, array $attributes)`  

`string addBccRecipient(string $address, array $attributes)`  

`string setFromAddress(string $address, array $attributes)`  

`string setSubject(string $subject)`  

`string setTextBody(string $textBody)`  

`string setHtmlBody(string $htmlBody)`  

`bool addAttachment(string $attachmentPath)`  

`bool addInlineImage(string $inlineImagePath)`  

`string setTestMode(bool $testMode)`  

`string addCampaignId(string $campaignId)`  

`string setDkim(bool $enabled)`  

`string setOpenTracking($enabled)`  

`string setClickTracking($enabled)`  

`string setDeliveryTime(string $timeDate, string $timeZone)`  

`string addCustomData(string $optionName, string $data)`  

`string addCustomParameter(string $parameterName, string $data)`

`array getMessage()`  

`array getFiles()`  


Usage - Batch Message
---------------------
Here's how to use Batch Message to easily handle batch sending jobs. 

```php
# First, instantiate the SDK with your API credentials and define your domain. 
$mg = new Mailgun("key-example");
$domain = "example.com";

# Next, instantiate a Message Builder object from the SDK, pass in your sending 
domain.
$batchMsg = $mg->BatchMessage($domain);

# Define the from address.
$batchMsg->setFromAddress("me@example.com", array("first"=>"PHP", "last" => "SDK"));
# Define the subject. 
$batchMsg->setSubject("A Batch Message from the PHP SDK!");
# Define the body of the message.
$batchMsg->setTextBody("This is the text body of the message!");

# Next, let's add a few recipients to the batch job.
$batchMsg->addToRecipient("john.doe@example.com", array("first" => "John", "last" => "Doe"));
$batchMsg->addToRecipient("sally.doe@example.com", array("first" => "Sally", "last" => "Doe"));
$batchMsg->addToRecipient("mike.jones@example.com", array("first" => "Mike", "last" => "Jones"));
...
// After 1,000 recipeints, Batch Message will automatically post your message to 
the messages endpoint. 

// Call finalize() to send any remaining recipients still in the buffer.
$batchMsg->finalize();

```

Available Functions (Inherits all Batch Message and Messages Functions)
-----------------------------------------------------------------------

`addToRecipient(string $address, string $attributes)`  

`sendMessage(array $message, array $files)` 
 
`array finalize()`  

More Documentation
------------------
See the official [Mailgun Docs](http://documentation.mailgun.com/api-sending.html) 
for more information.
