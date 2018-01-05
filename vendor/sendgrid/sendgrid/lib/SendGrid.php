<?php
/**
  * This library allows you to quickly and easily send emails through SendGrid using PHP.
  *
  * PHP version 5.3
  *
  * @author    Elmer Thomas <dx@sendgrid.com>
  * @copyright 2016 SendGrid
  * @license   https://opensource.org/licenses/MIT The MIT License
  * @version   GIT: <git_id>
  * @link      http://packagist.org/packages/sendgrid/sendgrid
  */

/**
  * Interface to the SendGrid Web API
  */
class SendGrid
{
    const VERSION = '5.1.2';

    /**
     *
     * @var string
     */
    protected $namespace = 'SendGrid';

    /**
     * @var \SendGrid\Client
     */
    public $client;

    /**
     * @var string
     */
    public $version = self::VERSION;

    /**
      * Setup the HTTP Client
      *
      * @param string $apiKey  your SendGrid API Key.
      * @param array  $options an array of options, currently only "host" is implemented.
      */
    public function __construct($apiKey, $options = array())
    {
        $headers = array(
            'Authorization: Bearer '.$apiKey,
            'User-Agent: sendgrid/' . $this->version . ';php',
            'Accept: application/json'
            );
        $host = isset($options['host']) ? $options['host'] : 'https://api.sendgrid.com';
        $this->client = new \SendGrid\Client($host, $headers, '/v3', null);
    }
}
