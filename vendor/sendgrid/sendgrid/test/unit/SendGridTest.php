<?php
class SendGridTest_SendGrid extends \PHPUnit_Framework_TestCase
{
    protected static $apiKey;
    protected static $sg;
    protected static $pid;

    public static function setUpBeforeClass()
    {
        self::$apiKey = "SENDGRID_API_KEY";
        $host = array('host' => 'http://localhost:4010');
        self::$sg = new SendGrid(self::$apiKey, $host);
        if( file_exists( '/usr/local/bin/prism' ) == false ) {
            if(strtoupper(substr(php_uname('s'), 0, 3)) != 'WIN'){
                try {
                    $proc_ls = proc_open("curl https://raw.githubusercontent.com/stoplightio/prism/master/install.sh",
                                        array(
                                            array("pipe","r"), //stdin
                                            array("pipe","w"), //stdout
                                            array("pipe","w")  //stderr
                                        ),
                                        $pipes);
                    $output_ls = stream_get_contents($pipes[1]);
                    fclose($pipes[0]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    $return_value_ls = proc_close($proc_ls);
                    $proc_grep = proc_open("sh",
                                            array(
                                                array("pipe","r"), //stdin
                                                array("pipe","w"), //stdout
                                                array("pipe","w")  //stderr
                                            ),
                                            $pipes);

                    fwrite($pipes[0], $output_ls);
                    fclose($pipes[0]);
                    $output_grep = stream_get_contents($pipes[1]);

                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    proc_close($proc_grep);
                } catch (Exception $e) {
                    print("Error downloading the prism binary, you can try downloading directly here (https://github.com/stoplightio/prism/releases) and place in your /user/local/bin directory: " .  $e->getMessage() . "\n");
                    exit();
                }
            } else {
                print("Please download the Windows binary (https://github.com/stoplightio/prism/releases) and place it in your /usr/local/bin directory");
                exit();
            }
        }
        print("Activating Prism (~20 seconds)\n");
        $command = 'nohup prism run -s https://raw.githubusercontent.com/sendgrid/sendgrid-oai/master/oai_stoplight.json > /dev/null 2>&1 & echo $!';
        exec($command, $op);
        self::$pid = (int)$op[0];
        sleep(15);
        print("\nPrism Started");
    }

    public function testVersion()
    {
        $this->assertEquals(SendGrid::VERSION, '5.1.2');
        $this->assertEquals(json_decode(file_get_contents(__DIR__ . '/../../composer.json'))->version, SendGrid::VERSION);
    }

    public function testSendGrid()
    {
        $apiKey = 'SENDGRID_API_KEY';
        $sg = new SendGrid($apiKey);
        $headers = array(
            'Authorization: Bearer '.$apiKey,
            'User-Agent: sendgrid/' . $sg->version . ';php',
            'Accept: application/json'
        );

        $this->assertEquals($sg->client->getHost(), 'https://api.sendgrid.com');
        $this->assertEquals($sg->client->getHeaders(), $headers);
        $this->assertEquals($sg->client->getVersion(), '/v3');

        $apiKey = 'SENDGRID_API_KEY';
        $sg2 = new SendGrid($apiKey, array('host' => 'https://api.test.com'));
        $this->assertEquals($sg2->client->getHost(), 'https://api.test.com');
    }

    public function test_access_settings_activity_get()
    {
        $query_params = json_decode('{"limit": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->access_settings()->activity()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_access_settings_whitelist_post()
    {
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
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->access_settings()->whitelist()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_access_settings_whitelist_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->access_settings()->whitelist()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_access_settings_whitelist_delete()
    {
        $request_body = json_decode('{
  "ids": [
    1,
    2,
    3
  ]
}');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->access_settings()->whitelist()->delete($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_access_settings_whitelist__rule_id__get()
    {
        $rule_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->access_settings()->whitelist()->_($rule_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_access_settings_whitelist__rule_id__delete()
    {
        $rule_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->access_settings()->whitelist()->_($rule_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_alerts_post()
    {
        $request_body = json_decode('{
  "email_to": "example@example.com",
  "frequency": "daily",
  "type": "stats_notification"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->alerts()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_alerts_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->alerts()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_alerts__alert_id__patch()
    {
        $request_body = json_decode('{
  "email_to": "example@example.com"
}');
        $alert_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->alerts()->_($alert_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_alerts__alert_id__get()
    {
        $alert_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->alerts()->_($alert_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_alerts__alert_id__delete()
    {
        $alert_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->alerts()->_($alert_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_api_keys_post()
    {
        $request_body = json_decode('{
  "name": "My API Key",
  "sample": "data",
  "scopes": [
    "mail.send",
    "alerts.create",
    "alerts.read"
  ]
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->api_keys()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_api_keys_get()
    {
        $query_params = json_decode('{"limit": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->api_keys()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_api_keys__api_key_id__put()
    {
        $request_body = json_decode('{
  "name": "A New Hope",
  "scopes": [
    "user.profile.read",
    "user.profile.update"
  ]
}');
        $api_key_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->api_keys()->_($api_key_id)->put($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_api_keys__api_key_id__patch()
    {
        $request_body = json_decode('{
  "name": "A New Hope"
}');
        $api_key_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->api_keys()->_($api_key_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_api_keys__api_key_id__get()
    {
        $api_key_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->api_keys()->_($api_key_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_api_keys__api_key_id__delete()
    {
        $api_key_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->api_keys()->_($api_key_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_asm_groups_post()
    {
        $request_body = json_decode('{
  "description": "Suggestions for products our users might like.",
  "is_default": true,
  "name": "Product Suggestions"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->asm()->groups()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_asm_groups_get()
    {
        $query_params = json_decode('{"id": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->asm()->groups()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_asm_groups__group_id__patch()
    {
        $request_body = json_decode('{
  "description": "Suggestions for items our users might like.",
  "id": 103,
  "name": "Item Suggestions"
}');
        $group_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->asm()->groups()->_($group_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_asm_groups__group_id__get()
    {
        $group_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->asm()->groups()->_($group_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_asm_groups__group_id__delete()
    {
        $group_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->asm()->groups()->_($group_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_asm_groups__group_id__suppressions_post()
    {
        $request_body = json_decode('{
  "recipient_emails": [
    "test1@example.com",
    "test2@example.com"
  ]
}');
        $group_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->asm()->groups()->_($group_id)->suppressions()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_asm_groups__group_id__suppressions_get()
    {
        $group_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->asm()->groups()->_($group_id)->suppressions()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_asm_groups__group_id__suppressions_search_post()
    {
        $request_body = json_decode('{
  "recipient_emails": [
    "exists1@example.com",
    "exists2@example.com",
    "doesnotexists@example.com"
  ]
}');
        $group_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->asm()->groups()->_($group_id)->suppressions()->search()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_asm_groups__group_id__suppressions__email__delete()
    {
        $group_id = "test_url_param";
        $email = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->asm()->groups()->_($group_id)->suppressions()->_($email)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_asm_suppressions_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->asm()->suppressions()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_asm_suppressions_global_post()
    {
        $request_body = json_decode('{
  "recipient_emails": [
    "test1@example.com",
    "test2@example.com"
  ]
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->asm()->suppressions()->global()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_asm_suppressions_global__email__get()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->asm()->suppressions()->global()->_($email)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_asm_suppressions_global__email__delete()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->asm()->suppressions()->global()->_($email)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_asm_suppressions__email__get()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->asm()->suppressions()->_($email)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_browsers_stats_get()
    {
        $query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "browsers": "test_string", "limit": "test_string", "offset": "test_string", "start_date": "2016-01-01"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->browsers()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_campaigns_post()
    {
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
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->campaigns()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_campaigns_get()
    {
        $query_params = json_decode('{"limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->campaigns()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_campaigns__campaign_id__patch()
    {
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
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->campaigns()->_($campaign_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_campaigns__campaign_id__get()
    {
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->campaigns()->_($campaign_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_campaigns__campaign_id__delete()
    {
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->campaigns()->_($campaign_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_campaigns__campaign_id__schedules_patch()
    {
        $request_body = json_decode('{
  "send_at": 1489451436
}');
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->campaigns()->_($campaign_id)->schedules()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_campaigns__campaign_id__schedules_post()
    {
        $request_body = json_decode('{
  "send_at": 1489771528
}');
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->campaigns()->_($campaign_id)->schedules()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_campaigns__campaign_id__schedules_get()
    {
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->campaigns()->_($campaign_id)->schedules()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_campaigns__campaign_id__schedules_delete()
    {
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->campaigns()->_($campaign_id)->schedules()->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_campaigns__campaign_id__schedules_now_post()
    {
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->campaigns()->_($campaign_id)->schedules()->now()->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_campaigns__campaign_id__schedules_test_post()
    {
        $request_body = json_decode('{
  "to": "your.email@example.com"
}');
        $campaign_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->campaigns()->_($campaign_id)->schedules()->test()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_categories_get()
    {
        $query_params = json_decode('{"category": "test_string", "limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->categories()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_categories_stats_get()
    {
        $query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01", "categories": "test_string"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->categories()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_categories_stats_sums_get()
    {
        $query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "start_date": "2016-01-01", "sort_by_direction": "asc"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->categories()->stats()->sums()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_clients_stats_get()
    {
        $query_params = json_decode('{"aggregated_by": "day", "start_date": "2016-01-01", "end_date": "2016-04-01"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->clients()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_clients__client_type__stats_get()
    {
        $query_params = json_decode('{"aggregated_by": "day", "start_date": "2016-01-01", "end_date": "2016-04-01"}');
        $client_type = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->clients()->_($client_type)->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_custom_fields_post()
    {
        $request_body = json_decode('{
  "name": "pet",
  "type": "text"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->contactdb()->custom_fields()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_contactdb_custom_fields_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->custom_fields()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_custom_fields__custom_field_id__get()
    {
        $custom_field_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->custom_fields()->_($custom_field_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_custom_fields__custom_field_id__delete()
    {
        $custom_field_id = "test_url_param";
        $request_headers = array("X-Mock: 202");
        $response = self::$sg->client->contactdb()->custom_fields()->_($custom_field_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 202);
    }

    public function test_contactdb_lists_post()
    {
        $request_body = json_decode('{
  "name": "your list name"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->contactdb()->lists()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_contactdb_lists_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->lists()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_lists_delete()
    {
        $request_body = json_decode('[
  1,
  2,
  3,
  4
]');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->contactdb()->lists()->delete($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_contactdb_lists__list_id__patch()
    {
        $request_body = json_decode('{
  "name": "newlistname"
}');
        $query_params = json_decode('{"list_id": 1}');
        $list_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->lists()->_($list_id)->patch($request_body, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_lists__list_id__get()
    {
        $query_params = json_decode('{"list_id": 1}');
        $list_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->lists()->_($list_id)->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_lists__list_id__delete()
    {
        $query_params = json_decode('{"delete_contacts": "true"}');
        $list_id = "test_url_param";
        $request_headers = array("X-Mock: 202");
        $response = self::$sg->client->contactdb()->lists()->_($list_id)->delete(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 202);
    }

    public function test_contactdb_lists__list_id__recipients_post()
    {
        $request_body = json_decode('[
  "recipient_id1",
  "recipient_id2"
]');
        $list_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->contactdb()->lists()->_($list_id)->recipients()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_contactdb_lists__list_id__recipients_get()
    {
        $query_params = json_decode('{"page": 1, "page_size": 1, "list_id": 1}');
        $list_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->lists()->_($list_id)->recipients()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_lists__list_id__recipients__recipient_id__post()
    {
        $list_id = "test_url_param";
        $recipient_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->contactdb()->lists()->_($list_id)->recipients()->_($recipient_id)->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_contactdb_lists__list_id__recipients__recipient_id__delete()
    {
        $query_params = json_decode('{"recipient_id": 1, "list_id": 1}');
        $list_id = "test_url_param";
        $recipient_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->contactdb()->lists()->_($list_id)->recipients()->_($recipient_id)->delete(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_contactdb_recipients_patch()
    {
        $request_body = json_decode('[
  {
    "email": "jones@example.com",
    "first_name": "Guy",
    "last_name": "Jones"
  }
]');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->contactdb()->recipients()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_contactdb_recipients_post()
    {
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
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->contactdb()->recipients()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_contactdb_recipients_get()
    {
        $query_params = json_decode('{"page": 1, "page_size": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->recipients()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_recipients_delete()
    {
        $request_body = json_decode('[
  "recipient_id1",
  "recipient_id2"
]');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->recipients()->delete($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_recipients_billable_count_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->recipients()->billable_count()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_recipients_count_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->recipients()->count()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_recipients_search_get()
    {
        $query_params = json_decode('{"{field_name}": "test_string"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->recipients()->search()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_recipients__recipient_id__get()
    {
        $recipient_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->recipients()->_($recipient_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_recipients__recipient_id__delete()
    {
        $recipient_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->contactdb()->recipients()->_($recipient_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_contactdb_recipients__recipient_id__lists_get()
    {
        $recipient_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->recipients()->_($recipient_id)->lists()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_reserved_fields_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->reserved_fields()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_segments_post()
    {
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
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->segments()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_segments_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->segments()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_segments__segment_id__patch()
    {
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
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->segments()->_($segment_id)->patch($request_body, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_segments__segment_id__get()
    {
        $query_params = json_decode('{"segment_id": 1}');
        $segment_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->segments()->_($segment_id)->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_contactdb_segments__segment_id__delete()
    {
        $query_params = json_decode('{"delete_contacts": "true"}');
        $segment_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->contactdb()->segments()->_($segment_id)->delete(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_contactdb_segments__segment_id__recipients_get()
    {
        $query_params = json_decode('{"page": 1, "page_size": 1}');
        $segment_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->contactdb()->segments()->_($segment_id)->recipients()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_devices_stats_get()
    {
        $query_params = json_decode('{"aggregated_by": "day", "limit": 1, "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->devices()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_geo_stats_get()
    {
        $query_params = json_decode('{"end_date": "2016-04-01", "country": "US", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->geo()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_get()
    {
        $query_params = json_decode('{"subuser": "test_string", "ip": "test_string", "limit": 1, "exclude_whitelabels": "true", "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_assigned_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->assigned()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_pools_post()
    {
        $request_body = json_decode('{
  "name": "marketing"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->pools()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_pools_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->pools()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_pools__pool_name__put()
    {
        $request_body = json_decode('{
  "name": "new_pool_name"
}');
        $pool_name = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->pools()->_($pool_name)->put($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_pools__pool_name__get()
    {
        $pool_name = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->pools()->_($pool_name)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_pools__pool_name__delete()
    {
        $pool_name = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->ips()->pools()->_($pool_name)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_ips_pools__pool_name__ips_post()
    {
        $request_body = json_decode('{
  "ip": "0.0.0.0"
}');
        $pool_name = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->ips()->pools()->_($pool_name)->ips()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_ips_pools__pool_name__ips__ip__delete()
    {
        $pool_name = "test_url_param";
        $ip = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->ips()->pools()->_($pool_name)->ips()->_($ip)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_ips_warmup_post()
    {
        $request_body = json_decode('{
  "ip": "0.0.0.0"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->warmup()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_warmup_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->warmup()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_warmup__ip_address__get()
    {
        $ip_address = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->warmup()->_($ip_address)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_ips_warmup__ip_address__delete()
    {
        $ip_address = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->ips()->warmup()->_($ip_address)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_ips__ip_address__get()
    {
        $ip_address = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->ips()->_($ip_address)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_batch_post()
    {
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->mail()->batch()->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_mail_batch__batch_id__get()
    {
        $batch_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail()->batch()->_($batch_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_send_post()
    {
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
        $request_headers = array("X-Mock: 202");
        $response = self::$sg->client->mail()->send()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 202);
    }

    public function test_mail_settings_get()
    {
        $query_params = json_decode('{"limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_address_whitelist_patch()
    {
        $request_body = json_decode('{
  "enabled": true,
  "list": [
    "email1@example.com",
    "example.com"
  ]
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->address_whitelist()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_address_whitelist_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->address_whitelist()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_bcc_patch()
    {
        $request_body = json_decode('{
  "email": "email@example.com",
  "enabled": false
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->bcc()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_bcc_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->bcc()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_bounce_purge_patch()
    {
        $request_body = json_decode('{
  "enabled": true,
  "hard_bounces": 5,
  "soft_bounces": 5
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->bounce_purge()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_bounce_purge_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->bounce_purge()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_footer_patch()
    {
        $request_body = json_decode('{
  "enabled": true,
  "html_content": "...",
  "plain_content": "..."
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->footer()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_footer_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->footer()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_forward_bounce_patch()
    {
        $request_body = json_decode('{
  "email": "example@example.com",
  "enabled": true
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->forward_bounce()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_forward_bounce_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->forward_bounce()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_forward_spam_patch()
    {
        $request_body = json_decode('{
  "email": "",
  "enabled": false
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->forward_spam()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_forward_spam_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->forward_spam()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_plain_content_patch()
    {
        $request_body = json_decode('{
  "enabled": false
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->plain_content()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_plain_content_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->plain_content()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_spam_check_patch()
    {
        $request_body = json_decode('{
  "enabled": true,
  "max_score": 5,
  "url": "url"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->spam_check()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_spam_check_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->spam_check()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_template_patch()
    {
        $request_body = json_decode('{
  "enabled": true,
  "html_content": "<% body %>"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->template()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mail_settings_template_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mail_settings()->template()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_mailbox_providers_stats_get()
    {
        $query_params = json_decode('{"end_date": "2016-04-01", "mailbox_providers": "test_string", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->mailbox_providers()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_partner_settings_get()
    {
        $query_params = json_decode('{"limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->partner_settings()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_partner_settings_new_relic_patch()
    {
        $request_body = json_decode('{
  "enable_subuser_statistics": true,
  "enabled": true,
  "license_key": ""
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->partner_settings()->new_relic()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_partner_settings_new_relic_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->partner_settings()->new_relic()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_scopes_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->scopes()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_senders_post()
    {
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
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->senders()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_senders_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->senders()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_senders__sender_id__patch()
    {
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
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->senders()->_($sender_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_senders__sender_id__get()
    {
        $sender_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->senders()->_($sender_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_senders__sender_id__delete()
    {
        $sender_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->senders()->_($sender_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_senders__sender_id__resend_verification_post()
    {
        $sender_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->senders()->_($sender_id)->resend_verification()->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_stats_get()
    {
        $query_params = json_decode('{"aggregated_by": "day", "limit": 1, "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers_post()
    {
        $request_body = json_decode('{
  "email": "John@example.com",
  "ips": [
    "1.1.1.1",
    "2.2.2.2"
  ],
  "password": "johns_password",
  "username": "John@example.com"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers_get()
    {
        $query_params = json_decode('{"username": "test_string", "limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers_reputations_get()
    {
        $query_params = json_decode('{"usernames": "test_string"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->reputations()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers_stats_get()
    {
        $query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "offset": 1, "start_date": "2016-01-01", "subusers": "test_string"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers_stats_monthly_get()
    {
        $query_params = json_decode('{"subuser": "test_string", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "date": "test_string", "sort_by_direction": "asc"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->stats()->monthly()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers_stats_sums_get()
    {
        $query_params = json_decode('{"end_date": "2016-04-01", "aggregated_by": "day", "limit": 1, "sort_by_metric": "test_string", "offset": 1, "start_date": "2016-01-01", "sort_by_direction": "asc"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->stats()->sums()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers__subuser_name__patch()
    {
        $request_body = json_decode('{
  "disabled": false
}');
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->subusers()->_($subuser_name)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_subusers__subuser_name__delete()
    {
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->subusers()->_($subuser_name)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_subusers__subuser_name__ips_put()
    {
        $request_body = json_decode('[
  "127.0.0.1"
]');
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->_($subuser_name)->ips()->put($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers__subuser_name__monitor_put()
    {
        $request_body = json_decode('{
  "email": "example@example.com",
  "frequency": 500
}');
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->_($subuser_name)->monitor()->put($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers__subuser_name__monitor_post()
    {
        $request_body = json_decode('{
  "email": "example@example.com",
  "frequency": 50000
}');
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->_($subuser_name)->monitor()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers__subuser_name__monitor_get()
    {
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->_($subuser_name)->monitor()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_subusers__subuser_name__monitor_delete()
    {
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->subusers()->_($subuser_name)->monitor()->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_subusers__subuser_name__stats_monthly_get()
    {
        $query_params = json_decode('{"date": "test_string", "sort_by_direction": "asc", "limit": 1, "sort_by_metric": "test_string", "offset": 1}');
        $subuser_name = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->subusers()->_($subuser_name)->stats()->monthly()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_blocks_get()
    {
        $query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->blocks()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_blocks_delete()
    {
        $request_body = json_decode('{
  "delete_all": false,
  "emails": [
    "example1@example.com",
    "example2@example.com"
  ]
}');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->blocks()->delete($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_blocks__email__get()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->blocks()->_($email)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_blocks__email__delete()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->blocks()->_($email)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_bounces_get()
    {
        $query_params = json_decode('{"start_time": 1, "end_time": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->bounces()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_bounces_delete()
    {
        $request_body = json_decode('{
  "delete_all": true,
  "emails": [
    "example@example.com",
    "example2@example.com"
  ]
}');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->bounces()->delete($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_bounces__email__get()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->bounces()->_($email)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_bounces__email__delete()
    {
        $query_params = json_decode('{"email_address": "example@example.com"}');
        $email = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->bounces()->_($email)->delete(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_invalid_emails_get()
    {
        $query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->invalid_emails()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_invalid_emails_delete()
    {
        $request_body = json_decode('{
  "delete_all": false,
  "emails": [
    "example1@example.com",
    "example2@example.com"
  ]
}');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->invalid_emails()->delete($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_invalid_emails__email__get()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->invalid_emails()->_($email)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_invalid_emails__email__delete()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->invalid_emails()->_($email)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_spam_report__email__get()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->spam_report()->_($email)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_spam_report__email__delete()
    {
        $email = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->spam_report()->_($email)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_spam_reports_get()
    {
        $query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->spam_reports()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_suppression_spam_reports_delete()
    {
        $request_body = json_decode('{
  "delete_all": false,
  "emails": [
    "example1@example.com",
    "example2@example.com"
  ]
}');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->suppression()->spam_reports()->delete($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_suppression_unsubscribes_get()
    {
        $query_params = json_decode('{"start_time": 1, "limit": 1, "end_time": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->suppression()->unsubscribes()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_templates_post()
    {
        $request_body = json_decode('{
  "name": "example_name"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->templates()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_templates_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->templates()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_templates__template_id__patch()
    {
        $request_body = json_decode('{
  "name": "new_example_name"
}');
        $template_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->templates()->_($template_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_templates__template_id__get()
    {
        $template_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->templates()->_($template_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_templates__template_id__delete()
    {
        $template_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->templates()->_($template_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_templates__template_id__versions_post()
    {
        $request_body = json_decode('{
  "active": 1,
  "html_content": "<%body%>",
  "name": "example_version_name",
  "plain_content": "<%body%>",
  "subject": "<%subject%>",
  "template_id": "ddb96bbc-9b92-425e-8979-99464621b543"
}');
        $template_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->templates()->_($template_id)->versions()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_templates__template_id__versions__version_id__patch()
    {
        $request_body = json_decode('{
  "active": 1,
  "html_content": "<%body%>",
  "name": "updated_example_name",
  "plain_content": "<%body%>",
  "subject": "<%subject%>"
}');
        $template_id = "test_url_param";
        $version_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->templates()->_($template_id)->versions()->_($version_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_templates__template_id__versions__version_id__get()
    {
        $template_id = "test_url_param";
        $version_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->templates()->_($template_id)->versions()->_($version_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_templates__template_id__versions__version_id__delete()
    {
        $template_id = "test_url_param";
        $version_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->templates()->_($template_id)->versions()->_($version_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_templates__template_id__versions__version_id__activate_post()
    {
        $template_id = "test_url_param";
        $version_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->templates()->_($template_id)->versions()->_($version_id)->activate()->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_get()
    {
        $query_params = json_decode('{"limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_click_patch()
    {
        $request_body = json_decode('{
  "enabled": true
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->click()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_click_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->click()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_google_analytics_patch()
    {
        $request_body = json_decode('{
  "enabled": true,
  "utm_campaign": "website",
  "utm_content": "",
  "utm_medium": "email",
  "utm_source": "sendgrid.com",
  "utm_term": ""
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->google_analytics()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_google_analytics_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->google_analytics()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_open_patch()
    {
        $request_body = json_decode('{
  "enabled": true
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->open()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_open_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->open()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_subscription_patch()
    {
        $request_body = json_decode('{
  "enabled": true,
  "html_content": "html content",
  "landing": "landing page html",
  "plain_content": "text content",
  "replace": "replacement tag",
  "url": "url"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->subscription()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_tracking_settings_subscription_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->tracking_settings()->subscription()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_account_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->account()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_credits_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->credits()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_email_put()
    {
        $request_body = json_decode('{
  "email": "example@example.com"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->email()->put($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_email_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->email()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_password_put()
    {
        $request_body = json_decode('{
  "new_password": "new_password",
  "old_password": "old_password"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->password()->put($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_profile_patch()
    {
        $request_body = json_decode('{
  "city": "Orange",
  "first_name": "Example",
  "last_name": "User"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->profile()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_profile_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->profile()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_scheduled_sends_post()
    {
        $request_body = json_decode('{
  "batch_id": "YOUR_BATCH_ID",
  "status": "pause"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->user()->scheduled_sends()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_user_scheduled_sends_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->scheduled_sends()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_scheduled_sends__batch_id__patch()
    {
        $request_body = json_decode('{
  "status": "pause"
}');
        $batch_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->user()->scheduled_sends()->_($batch_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_user_scheduled_sends__batch_id__get()
    {
        $batch_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->scheduled_sends()->_($batch_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_scheduled_sends__batch_id__delete()
    {
        $batch_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->user()->scheduled_sends()->_($batch_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_user_settings_enforced_tls_patch()
    {
        $request_body = json_decode('{
  "require_tls": true,
  "require_valid_cert": false
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->settings()->enforced_tls()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_settings_enforced_tls_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->settings()->enforced_tls()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_username_put()
    {
        $request_body = json_decode('{
  "username": "test_username"
}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->username()->put($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_username_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->username()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_webhooks_event_settings_patch()
    {
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
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->webhooks()->event()->settings()->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_webhooks_event_settings_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->webhooks()->event()->settings()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_webhooks_event_test_post()
    {
        $request_body = json_decode('{
  "url": "url"
}');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->user()->webhooks()->event()->test()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_user_webhooks_parse_settings_post()
    {
        $request_body = json_decode('{
  "hostname": "myhostname.com",
  "send_raw": false,
  "spam_check": true,
  "url": "http://email.myhosthame.com"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->user()->webhooks()->parse()->settings()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_user_webhooks_parse_settings_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->webhooks()->parse()->settings()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_webhooks_parse_settings__hostname__patch()
    {
        $request_body = json_decode('{
  "send_raw": true,
  "spam_check": false,
  "url": "http://newdomain.com/parse"
}');
        $hostname = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->webhooks()->parse()->settings()->_($hostname)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_webhooks_parse_settings__hostname__get()
    {
        $hostname = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->webhooks()->parse()->settings()->_($hostname)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_user_webhooks_parse_settings__hostname__delete()
    {
        $hostname = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->user()->webhooks()->parse()->settings()->_($hostname)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_user_webhooks_parse_stats_get()
    {
        $query_params = json_decode('{"aggregated_by": "day", "limit": "test_string", "start_date": "2016-01-01", "end_date": "2016-04-01", "offset": "test_string"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->user()->webhooks()->parse()->stats()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains_post()
    {
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
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->whitelabel()->domains()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_whitelabel_domains_get()
    {
        $query_params = json_decode('{"username": "test_string", "domain": "test_string", "exclude_subusers": "true", "limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains_default_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->default()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains_subuser_get()
    {
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->subuser()->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains_subuser_delete()
    {
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->whitelabel()->domains()->subuser()->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_whitelabel_domains__domain_id__patch()
    {
        $request_body = json_decode('{
  "custom_spf": true,
  "default": false
}');
        $domain_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->_($domain_id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains__domain_id__get()
    {
        $domain_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->_($domain_id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains__domain_id__delete()
    {
        $domain_id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->whitelabel()->domains()->_($domain_id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_whitelabel_domains__domain_id__subuser_post()
    {
        $request_body = json_decode('{
  "username": "jane@example.com"
}');
        $domain_id = "test_url_param";
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->whitelabel()->domains()->_($domain_id)->subuser()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_whitelabel_domains__id__ips_post()
    {
        $request_body = json_decode('{
  "ip": "192.168.0.1"
}');
        $id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->_($id)->ips()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains__id__ips__ip__delete()
    {
        $id = "test_url_param";
        $ip = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->_($id)->ips()->_($ip)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_domains__id__validate_post()
    {
        $id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->domains()->_($id)->validate()->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_ips_post()
    {
        $request_body = json_decode('{
  "domain": "example.com",
  "ip": "192.168.1.1",
  "subdomain": "email"
}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->whitelabel()->ips()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_whitelabel_ips_get()
    {
        $query_params = json_decode('{"ip": "test_string", "limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->ips()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_ips__id__get()
    {
        $id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->ips()->_($id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_ips__id__delete()
    {
        $id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->whitelabel()->ips()->_($id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_whitelabel_ips__id__validate_post()
    {
        $id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->ips()->_($id)->validate()->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_links_post()
    {
        $request_body = json_decode('{
  "default": true,
  "domain": "example.com",
  "subdomain": "mail"
}');
        $query_params = json_decode('{"limit": 1, "offset": 1}');
        $request_headers = array("X-Mock: 201");
        $response = self::$sg->client->whitelabel()->links()->post($request_body, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 201);
    }

    public function test_whitelabel_links_get()
    {
        $query_params = json_decode('{"limit": 1}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->links()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_links_default_get()
    {
        $query_params = json_decode('{"domain": "test_string"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->links()->default()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_links_subuser_get()
    {
        $query_params = json_decode('{"username": "test_string"}');
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->links()->subuser()->get(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_links_subuser_delete()
    {
        $query_params = json_decode('{"username": "test_string"}');
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->whitelabel()->links()->subuser()->delete(null, $query_params, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_whitelabel_links__id__patch()
    {
        $request_body = json_decode('{
  "default": true
}');
        $id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->links()->_($id)->patch($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_links__id__get()
    {
        $id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->links()->_($id)->get(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_links__id__delete()
    {
        $id = "test_url_param";
        $request_headers = array("X-Mock: 204");
        $response = self::$sg->client->whitelabel()->links()->_($id)->delete(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 204);
    }

    public function test_whitelabel_links__id__validate_post()
    {
        $id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->links()->_($id)->validate()->post(null, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public function test_whitelabel_links__link_id__subuser_post()
    {
        $request_body = json_decode('{
  "username": "jane@example.com"
}');
        $link_id = "test_url_param";
        $request_headers = array("X-Mock: 200");
        $response = self::$sg->client->whitelabel()->links()->_($link_id)->subuser()->post($request_body, null, $request_headers);
        $this->assertEquals($response->statusCode(), 200);
    }

    public static function tearDownAfterClass()
    {
        $command = 'kill '.self::$pid;
        exec($command);
        print("\nPrism shut down");
    }
}
