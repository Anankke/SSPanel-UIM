<?php
namespace SendGrid;

class MailTest_Mail extends \PHPUnit_Framework_TestCase
{
    public function testBaseLineExample()
    {
        $mail = new Mail();

        $email = new Email(null, "test@example.com");
        $mail->setFrom($email);

        $personalization = new Personalization();
        $email = new Email(null, "test@example.com");
        $personalization->addTo($email);
        $mail->addPersonalization($personalization);

        $mail->setSubject("Hello World from the SendGrid PHP Library");

        $content = new Content("text/plain", "some text here");
        $mail->addContent($content);
        $content = new Content("text/html", "<html><body>some text here</body></html>");
        $mail->addContent($content);

        $json = json_encode($mail);

        $this->assertEquals($json, '{"from":{"email":"test@example.com"},"personalizations":[{"to":[{"email":"test@example.com"}]}],"subject":"Hello World from the SendGrid PHP Library","content":[{"type":"text\/plain","value":"some text here"},{"type":"text\/html","value":"<html><body>some text here<\/body><\/html>"}]}');
    }

    public function testKitchenSinkExample()
    {
        $mail = new Mail();

        $email = new Email("DX", "test@example.com");
        $mail->setFrom($email);

        $mail->setSubject("Hello World from the SendGrid PHP Library");

        $personalization = new Personalization();
        $email = new Email("Example User", "test@example.com");
        $personalization->addTo($email);
        $email = new Email("Example User", "test@example.com");
        $personalization->addTo($email);
        $email = new Email("Example User", "test@example.com");
        $personalization->addCc($email);
        $email = new Email("Example User", "test@example.com");
        $personalization->addCc($email);
        $email = new Email("Example User", "test@example.com");
        $personalization->addBcc($email);
        $email = new Email("Example User", "test@example.com");
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
        $email = new Email("Example User", "test@example.com");
        $personalization2->addTo($email);
        $email = new Email("Example User", "test@example.com");
        $personalization2->addTo($email);
        $email = new Email("Example User", "test@example.com");
        $personalization2->addCc($email);
        $email = new Email("Example User", "test@example.com");
        $personalization2->addCc($email);
        $email = new Email("Example User", "test@example.com");
        $personalization2->addBcc($email);
        $email = new Email("Example User", "test@example.com");
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
        $bcc_settings->setEnable(True);
        $bcc_settings->setEmail("test@example.com");
        $mail_settings->setBccSettings($bcc_settings);
        $sandbox_mode = new SandBoxMode();
        $sandbox_mode->setEnable(True);
        $mail_settings->setSandboxMode($sandbox_mode);
        $bypass_list_management = new BypassListManagement();
        $bypass_list_management->setEnable(True);
        $mail_settings->setBypassListManagement($bypass_list_management);
        $footer = new Footer();
        $footer->setEnable(true);
        $footer->setText("Footer Text");
        $footer->setHtml("<html><body>Footer Text</body></html>");
        $mail_settings->setFooter($footer);
        $spam_check = new SpamCheck();
        $spam_check->setEnable(True);
        $spam_check->setThreshold(1);
        $spam_check->setPostToUrl("https://spamcatcher.sendgrid.com");
        $mail_settings->setSpamCheck($spam_check);
        $mail->setMailSettings($mail_settings);

        $tracking_settings = new TrackingSettings();
        $click_tracking = new ClickTracking();
        $click_tracking->setEnable(true);
        $click_tracking->setEnableText(True);
        $tracking_settings->setClickTracking($click_tracking);
        $open_tracking = new OpenTracking();
        $open_tracking->setEnable(True);
        $open_tracking->setSubstitutionTag("Optional tag to replace with the open image in the body of the message");
        $tracking_settings->setOpenTracking($open_tracking);
        $subscription_tracking = new SubscriptionTracking();
        $subscription_tracking->setEnable(True);
        $subscription_tracking->setText("text to insert into the text/plain portion of the message");
        $subscription_tracking->setHtml("<html><body>html to insert into the text/html portion of the message</body></html>");
        $subscription_tracking->setSubstitutionTag("Optional tag to replace with the open image in the body of the message");
        $tracking_settings->setSubscriptionTracking($subscription_tracking);
        $ganalytics = new Ganalytics();
        $ganalytics->setEnable(True);
        $ganalytics->setCampaignSource("some source");
        $ganalytics->setCampaignTerm("some term");
        $ganalytics->setCampaignContent("some content");
        $ganalytics->setCampaignName("some name");
        $ganalytics->setCampaignMedium("some medium");
        $tracking_settings->setGanalytics($ganalytics);
        $mail->setTrackingSettings($tracking_settings);

        $reply_to = new ReplyTo("test@example.com");
        $mail->setReplyTo($reply_to);

        $json = json_encode($mail);

        $this->assertEquals($json, '{"from":{"name":"DX","email":"test@example.com"},"personalizations":[{"to":[{"name":"Example User","email":"test@example.com"},{"name":"Example User","email":"test@example.com"}],"cc":[{"name":"Example User","email":"test@example.com"},{"name":"Example User","email":"test@example.com"}],"bcc":[{"name":"Example User","email":"test@example.com"},{"name":"Example User","email":"test@example.com"}],"subject":"Hello World from the SendGrid PHP Library","headers":{"X-Test":"test","X-Mock":"true"},"substitutions":{"%name%":"Example User","%city%":"Denver"},"custom_args":{"user_id":"343","type":"marketing"},"send_at":1443636843},{"to":[{"name":"Example User","email":"test@example.com"},{"name":"Example User","email":"test@example.com"}],"cc":[{"name":"Example User","email":"test@example.com"},{"name":"Example User","email":"test@example.com"}],"bcc":[{"name":"Example User","email":"test@example.com"},{"name":"Example User","email":"test@example.com"}],"subject":"Hello World from the SendGrid PHP Library","headers":{"X-Test":"test","X-Mock":"true"},"substitutions":{"%name%":"Example User","%city%":"Denver"},"custom_args":{"user_id":"343","type":"marketing"},"send_at":1443636843}],"subject":"Hello World from the SendGrid PHP Library","content":[{"type":"text\/plain","value":"some text here"},{"type":"text\/html","value":"<html><body>some text here<\/body><\/html>"}],"attachments":[{"content":"TG9yZW0gaXBzdW0gZG9sb3Igc2l0IGFtZXQsIGNvbnNlY3RldHVyIGFkaXBpc2NpbmcgZWxpdC4gQ3JhcyBwdW12","type":"application\/pdf","filename":"balance_001.pdf","disposition":"attachment","content_id":"Balance Sheet"},{"content":"BwdW","type":"image\/png","filename":"banner.png","disposition":"inline","content_id":"Banner"}],"template_id":"439b6d66-4408-4ead-83de-5c83c2ee313a","sections":{"%section1%":"Substitution Text for Section 1","%section2%":"Substitution Text for Section 2"},"headers":{"X-Test1":"1","X-Test2":"2"},"categories":["May","2016"],"custom_args":{"campaign":"welcome","weekday":"morning"},"send_at":1443636842,"asm":{"group_id":99,"groups_to_display":[4,5,6,7,8]},"ip_pool_name":"23","mail_settings":{"bcc":{"enable":true,"email":"test@example.com"},"bypass_list_management":{"enable":true},"footer":{"enable":true,"text":"Footer Text","html":"<html><body>Footer Text<\/body><\/html>"},"sandbox_mode":{"enable":true},"spam_check":{"enable":true,"threshold":1,"post_to_url":"https:\/\/spamcatcher.sendgrid.com"}},"tracking_settings":{"click_tracking":{"enable":true,"enable_text":true},"open_tracking":{"enable":true,"substitution_tag":"Optional tag to replace with the open image in the body of the message"},"subscription_tracking":{"enable":true,"text":"text to insert into the text\/plain portion of the message","html":"<html><body>html to insert into the text\/html portion of the message<\/body><\/html>","substitution_tag":"Optional tag to replace with the open image in the body of the message"},"ganalytics":{"enable":true,"utm_source":"some source","utm_medium":"some medium","utm_term":"some term","utm_content":"some content","utm_campaign":"some name"}},"reply_to":{"email":"test@example.com"}}');
    }
}