<?php

namespace App\Utils;

/**
 * Socket communication class.
 *
 * Originally designed for use with DirectAdmin's API, this class will fill any HTTP socket need.
 *
 * Very, very basic usage:
 *   $Socket = new HTTPSocket;
 *   echo $Socket->get('http://user:pass@somesite.com/somedir/some.file?query=string&this=that');
 *
 * @author Phi1 'l0rdphi1' Stier <l0rdphi1@liquenox.net>
 * @package HTTPSocket
 * @version 2.7.2
 * 2.7.2
 * added x-use-https header check
 * added max number of location redirects
 * added custom settable message if x-use-https is found, so users can be told where to set their scripts
 * if a redirect host is https, add ssl:// to remote_host
 * 2.7.1
 * added isset to headers['location'], line 306
 */
class HTTPSocket
{
    public $version = '2.7.2';

    /* all vars are private except $error, $query_cache, and $doFollowLocationHeader */

    public $method = 'GET';

    public $remote_host;
    public $remote_port;
    public $remote_uname;
    public $remote_passwd;

    public $result;
    public $result_header;
    public $result_body;
    public $result_status_code;

    public $lastTransferSpeed;

    public $bind_host;

    public $error = array();
    public $warn = array();
    public $query_cache = array();

    public $doFollowLocationHeader = true;
    public $redirectURL;
    public $max_redirects = 5;
    public $ssl_setting_message = 'DirectAdmin appears to be using SSL. Change your script to connect to ssl://';

    public $extra_headers = array();

    /**
     * Create server "connection".
     *
     */
    public function connect($host, $port = '')
    {
        if (!is_numeric($port)) {
            $port = 80;
        }

        $this->remote_host = $host;
        $this->remote_port = $port;
    }

    public function bind($ip = '')
    {
        if ($ip == '') {
            $ip = $_SERVER['SERVER_ADDR'];
        }

        $this->bind_host = $ip;
    }

    /**
     * Change the method being used to communicate.
     *
     * @param string|null request method. supports GET, POST, and HEAD. default is GET
     */
    public function set_method($method = 'GET')
    {
        $this->method = strtoupper($method);
    }

    /**
     * Specify a username and password.
     *
     * @param string|null username. defualt is null
     * @param string|null password. defualt is null
     */
    public function set_login($uname = '', $passwd = '')
    {
        if (strlen($uname) > 0) {
            $this->remote_uname = $uname;
        }

        if (strlen($passwd) > 0) {
            $this->remote_passwd = $passwd;
        }
    }

    /**
     * Query the server
     *
     * @param string containing properly formatted server API. See DA API docs and examples. Http:// URLs O.K. too.
     * @param string|array query to pass to url
     * @param int if connection KB/s drops below value here, will drop connection
     */
    public function query($request, $content = '', $doSpeedCheck = 0)
    {
        $this->error = $this->warn = array();
        $this->result_status_code = null;

        // is our request a http:// ... ?
        if (preg_match('!^http://!i', $request) || preg_match('!^https://!i', $request)) {
            $location = parse_url($request);
            if (preg_match('!^https://!i', $request)) {
                $this->connect('ssl://' . $location['host'], $location['port']);
            } else {
                $this->connect($location['host'], $location['port']);
            }
            $this->set_login($location['user'], $location['pass']);

            $request = $location['path'];
            $content = $location['query'];


            if (strlen($request) < 1) {
                $request = '/';
            }
        }

        $array_headers = array(
            'User-Agent' => "HTTPSocket/$this->version",
            'Host' => ($this->remote_port == 80 ? $this->remote_host : "$this->remote_host:$this->remote_port"),
            'Accept' => '*/*',
            'Connection' => 'Close');

        foreach ($this->extra_headers as $key => $value) {
            $array_headers[$key] = $value;
        }

        $this->result = $this->result_header = $this->result_body = '';

        // was content sent as an array? if so, turn it into a string
        if (is_array($content)) {
            $pairs = array();

            foreach ($content as $key => $value) {
                $pairs[] = "$key=" . urlencode($value);
            }

            $content = join('&', $pairs);
            unset($pairs);
        }

        $OK = true;

        // instance connection
        if ($this->bind_host) {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_bind($socket, $this->bind_host);

            if (!@socket_connect($socket, $this->remote_host, $this->remote_port)) {
                $OK = false;
            }
        } else {
            $socket = @fsockopen($this->remote_host, $this->remote_port, $sock_errno, $sock_errstr, 10);
        }

        if (!$socket || !$OK) {
            $this->error[] = "Can't create socket connection to $this->remote_host:$this->remote_port.";
            return 0;
        }

        // if we have a username and password, add the header
        if (isset($this->remote_uname) && isset($this->remote_passwd)) {
            $array_headers['Authorization'] = 'Basic ' . base64_encode("$this->remote_uname:$this->remote_passwd");
        }

        // for DA skins: if $this->remote_passwd is NULL, try to use the login key system
        if (isset($this->remote_uname) && $this->remote_passwd == null) {
            $array_headers['Cookie'] = "session={$_SERVER['SESSION_ID']}; key={$_SERVER['SESSION_KEY']}";
        }

        // if method is POST, add content length & type headers
        if ($this->method == 'POST') {
            $array_headers['Content-type'] = 'application/x-www-form-urlencoded';
            $array_headers['Content-length'] = strlen($content);
        } // else method is GET or HEAD. we don't support anything else right now.
        else {
            if ($content) {
                $request .= "?$content";
            }
        }

        // prepare query
        $query = "$this->method $request HTTP/1.0\r\n";
        foreach ($array_headers as $key => $value) {
            $query .= "$key: $value\r\n";
        }
        $query .= "\r\n";

        // if POST we need to append our content
        if ($this->method == 'POST' && $content) {
            $query .= "$content\r\n\r\n";
        }

        // query connection
        if ($this->bind_host) {
            socket_write($socket, $query);

            // now load results
            while ($out = socket_read($socket, 2048)) {
                $this->result .= $out;
            }
        } else {
            fwrite($socket, $query, strlen($query));

            // now load results
            $this->lastTransferSpeed = 0;
            $status = socket_get_status($socket);
            $startTime = time();
            $length = 0;
            $prevSecond = 0;
            while (!feof($socket) && !$status['timed_out']) {
                $chunk = fgets($socket, 1024);
                $length += strlen($chunk);
                $this->result .= $chunk;

                $elapsedTime = time() - $startTime;

                if ($elapsedTime > 0) {
                    $this->lastTransferSpeed = ($length / 1024) / $elapsedTime;
                }

                if ($doSpeedCheck > 0 && $elapsedTime > 5 && $this->lastTransferSpeed < $doSpeedCheck) {
                    $this->warn[] = "kB/s for last 5 seconds is below 50 kB/s (~" . (($length / 1024) / $elapsedTime) . "), dropping connection...";
                    $this->result_status_code = 503;
                    break;
                }
            }

            if ($this->lastTransferSpeed == 0) {
                $this->lastTransferSpeed = $length / 1024;
            }
        }

        list($this->result_header, $this->result_body) = preg_split("/\r\n\r\n/", $this->result, 2);

        if ($this->bind_host) {
            socket_close($socket);
        } else {
            fclose($socket);
        }

        $this->query_cache[] = $query;


        $headers = $this->fetch_header();

        // what return status did we get?
        if (!$this->result_status_code) {
            preg_match("#HTTP/1\.. (\d+)#", $headers[0], $matches);
            $this->result_status_code = $matches[1];
        }

        // did we get the full file?
        if (!empty($headers['content-length']) && $headers['content-length'] != strlen($this->result_body)) {
            $this->result_status_code = 206;
        }

        // now, if we're being passed a location header, should we follow it?
        if ($this->doFollowLocationHeader) {
            //dont bother if we didn't even setup the script correctly
            if (isset($headers['x-use-https']) && $headers['x-use-https'] == 'yes') {
                die($this->ssl_setting_message);
            }

            if (isset($headers['location'])) {
                if ($this->max_redirects <= 0) {
                    die("Too many redirects on: " . $headers['location']);
                }

                $this->max_redirects--;
                $this->redirectURL = $headers['location'];
                $this->query($headers['location']);
            }
        }
    }

    public function getTransferSpeed()
    {
        return $this->lastTransferSpeed;
    }

    /**
     * The quick way to get a URL's content :)
     *
     * @param string URL
     * @param boolean return as array? (like PHP's file() command)
     * @return string result body
     */
    public function get($location, $asArray = false)
    {
        $this->query($location);

        if ($this->get_status_code() == 200) {
            if ($asArray) {
                return preg_split("/\n/", $this->fetch_body());
            }

            return $this->fetch_body();
        }

        return false;
    }

    /**
     * Returns the last status code.
     * 200 = OK;
     * 403 = FORBIDDEN;
     * etc.
     *
     * @return int status code
     */
    public function get_status_code()
    {
        return $this->result_status_code;
    }

    /**
     * Adds a header, sent with the next query.
     *
     * @param string header name
     * @param string header value
     */
    public function add_header($key, $value)
    {
        $this->extra_headers[$key] = $value;
    }

    /**
     * Clears any extra headers.
     *
     */
    public function clear_headers()
    {
        $this->extra_headers = array();
    }

    /**
     * Return the result of a query.
     *
     * @return string result
     */
    public function fetch_result()
    {
        return $this->result;
    }

    /**
     * Return the header of result (stuff before body).
     *
     * @param string (optional) header to return
     * @return array result header
     */
    public function fetch_header($header = '')
    {
        $array_headers = preg_split("/\r\n/", $this->result_header);

        $array_return = array(0 => $array_headers[0]);
        unset($array_headers[0]);

        foreach ($array_headers as $pair) {
            list($key, $value) = preg_split("/: /", $pair, 2);
            $array_return[strtolower($key)] = $value;
        }

        if ($header != '') {
            return $array_return[strtolower($header)];
        }

        return $array_return;
    }

    /**
     * Return the body of result (stuff after header).
     *
     * @return string result body
     */
    public function fetch_body()
    {
        return $this->result_body;
    }

    /**
     * Return parsed body in array format.
     *
     * @return array result parsed
     */
    public function fetch_parsed_body()
    {
        parse_str($this->result_body, $x);
        return $x;
    }


    /**
     * Set a specifc message on how to change the SSL setting, in the event that it's not set correctly.
     */
    public function set_ssl_setting_message($str)
    {
        $this->ssl_setting_message = $str;
    }
}
