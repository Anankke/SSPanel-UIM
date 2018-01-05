<?php
namespace SendGrid;

// If you are using Composer
require __DIR__ . '<PATH_TO>/vendor/autoload.php';


function helloEmail()
{
    $from = new Email(null, "test@example.com");
    $subject = "Hello World from the SendGrid PHP Library";
    $to = new Email(null, "test@example.com");
    $content = new Content("text/plain", "some text here");
    $mail = new Mail($from, $subject, $to, $content);
    $to = new Email(null, "test2@example.com");
    $mail->personalization[0]->addTo($to);

    //echo json_encode($mail, JSON_PRETTY_PRINT), "\n";
    return $mail;
}

function kitchenSink()
{
    $mail = new Mail();

    $email = new Email("DX", "test@example.com");
    $mail->setFrom($email);

    $mail->setSubject("Hello World from the SendGrid PHP Library");

    $personalization = new Personalization();
    $email = new Email("Example User", "test1@example.com");
    $personalization->addTo($email);
    $email = new Email("Example User", "test2@example.com");
    $personalization->addTo($email);
    $email = new Email("Example User", "test3@example.com");
    $personalization->addCc($email);
    $email = new Email("Example User", "test4@example.com");
    $personalization->addCc($email);
    $email = new Email("Example User", "test5@example.com");
    $personalization->addBcc($email);
    $email = new Email("Example User", "test6@example.com");
    $personalization->addBcc($email);
    $personalization->setSubject("Hello World from the SendGrid PHP Library");
    $personalization->addHeader("X-Test", "test");
    $personalization->addHeader("X-Mock", "true");
    $personalization->addSubstitution("%name%", "Example User");
    $personalization->addSubstitution("%city%", "Denver");
    $personalization->addCustomArg("user_id", "343");
    $personalization->addCustomArg("type", "marketing");
    $personalization->setSendAt(1443636843);
    $mail->addPersonalization($personalization);

    $personalization2 = new Personalization();
    $email = new Email("Example User", "test1@example.com");
    $personalization2->addTo($email);
    $email = new Email("Example User", "test2@example.com");
    $personalization2->addTo($email);
    $email = new Email("Example User", "test3@example.com");
    $personalization2->addCc($email);
    $email = new Email("Example User", "test4@example.com");
    $personalization2->addCc($email);
    $email = new Email("Example User", "test5@example.com");
    $personalization2->addBcc($email);
    $email = new Email("Example User", "test6@example.com");
    $personalization2->addBcc($email);
    $personalization2->setSubject("Hello World from the SendGrid PHP Library");
    $personalization2->addHeader("X-Test", "test");
    $personalization2->addHeader("X-Mock", "true");
    $personalization2->addSubstitution("%name%", "Example User");
    $personalization2->addSubstitution("%city%", "Denver");
    $personalization2->addCustomArg("user_id", "343");
    $personalization2->addCustomArg("type", "marketing");
    $personalization2->setSendAt(1443636843);
    $mail->addPersonalization($personalization2);

    $content = new Content("text/plain", "some text here");
    $mail->addContent($content);
    $content = new Content("text/html", "<html><body>some text here</body></html>");
    $mail->addContent($content);

    $attachment = new Attachment();
    $attachment->setContent("TG9yZW0gaXBzdW0gZG9sb3Igc2l0IGFtZXQsIGNvbnNlY3RldHVyIGFkaXBpc2NpbmcgZWxpdC4gQ3JhcyBwdW12");
    $attachment->setType("application/pdf");
    $attachment->setFilename("balance_001.pdf");
    $attachment->setDisposition("attachment");
    $attachment->setContentId("Balance Sheet");
    $mail->addAttachment($attachment);

    $attachment2 = new Attachment();
    $attachment2->setContent("BwdW");
    $attachment2->setType("image/png");
    $attachment2->setFilename("banner.png");
    $attachment2->setDisposition("inline");
    $attachment2->setContentId("Banner");
    $mail->addAttachment($attachment2);

    $mail->setTemplateId("439b6d66-4408-4ead-83de-5c83c2ee313a");

    # This must be a valid [batch ID](https://sendgrid.com/docs/API_Reference/SMTP_API/scheduling_parameters.html) to work
    # $mail->setBatchID("sengrid_batch_id");

    $mail->addSection("%section1%", "Substitution Text for Section 1");
    $mail->addSection("%section2%", "Substitution Text for Section 2");

    $mail->addHeader("X-Test1", "1");
    $mail->addHeader("X-Test2", "2");

    $mail->addCategory("May");
    $mail->addCategory("2016");

    $mail->addCustomArg("campaign", "welcome");
    $mail->addCustomArg("weekday", "morning");

    $mail->setSendAt(1443636842);

    $asm = new ASM();
    $asm->setGroupId(99);
    $asm->setGroupsToDisplay([4,5,6,7,8]);
    $mail->setASM($asm);

    $mail->setIpPoolName("23");

    $mail_settings = new MailSettings();
    $bcc_settings = new BccSettings();
    $bcc_settings->setEnable(true);
    $bcc_settings->setEmail("test@example.com");
    $mail_settings->setBccSettings($bcc_settings);
    $sandbox_mode = new SandBoxMode();
    $sandbox_mode->setEnable(true);
    $mail_settings->setSandboxMode($sandbox_mode);
    $bypass_list_management = new BypassListManagement();
    $bypass_list_management->setEnable(true);
    $mail_settings->setBypassListManagement($bypass_list_management);
    $footer = new Footer();
    $footer->setEnable(true);
    $footer->setText("Footer Text");
    $footer->setHtml("<html><body>Footer Text</body></html>");
    $mail_settings->setFooter($footer);
    $spam_check = new SpamCheck();
    $spam_check->setEnable(true);
    $spam_check->setThreshold(1);
    $spam_check->setPostToUrl("https://spamcatcher.sendgrid.com");
    $mail_settings->setSpamCheck($spam_check);
    $mail->setMailSettings($mail_settings);

    $tracking_settings = new TrackingSettings();
    $click_tracking = new ClickTracking();
    $click_tracking->setEnable(true);
    $click_tracking->setEnableText(true);
    $tracking_settings->setClickTracking($click_tracking);
    $open_tracking = new OpenTracking();
    $open_tracking->setEnable(true);
    $open_tracking->setSubstitutionTag("Optional tag to replace with the open image in the body of the message");
    $tracking_settings->setOpenTracking($open_tracking);
    $subscription_tracking = new SubscriptionTracking();
    $subscription_tracking->setEnable(true);
    $subscription_tracking->setText("text to insert into the text/plain portion of the message");
    $subscription_tracking->setHtml("<html><body>html to insert into the text/html portion of the message</body></html>");
    $subscription_tracking->setSubstitutionTag("Optional tag to replace with the open image in the body of the message");
    $tracking_settings->setSubscriptionTracking($subscription_tracking);
    $ganalytics = new Ganalytics();
    $ganalytics->setEnable(true);
    $ganalytics->setCampaignSource("some source");
    $ganalytics->setCampaignTerm("some term");
    $ganalytics->setCampaignContent("some content");
    $ganalytics->setCampaignName("some name");
    $ganalytics->setCampaignMedium("some medium");
    $tracking_settings->setGanalytics($ganalytics);
    $mail->setTrackingSettings($tracking_settings);

    $reply_to = new ReplyTo("test@example.com");
    $mail->setReplyTo($reply_to);

    //echo json_encode($mail, JSON_PRETTY_PRINT), "\n";
    return $mail;
}

function sendHelloEmail()
{
    $apiKey = getenv('SENDGRID_API_KEY');
    $sg = new \SendGrid($apiKey);

    $request_body = helloEmail();
    $response = $sg->client->mail()->send()->post($request_body);
    echo $response->statusCode();
    echo $response->body();
    echo $response->headers();
}

function sendKitchenSink()
{
    $apiKey = getenv('SENDGRID_API_KEY');
    $sg = new \SendGrid($apiKey);

    $request_body = kitchenSink();
    $response = $sg->client->mail()->send()->post($request_body);
    echo $response->statusCode();
    echo $response->body();
    echo $response->headers();
}

sendHelloEmail();  // this will actually send an email
sendKitchenSink(); // this will only send an email if you set SandBox Mode to false
?>


