<?PHP

namespace Mailgun;

require_once 'Constants/Constants.php';

use Mailgun\Messages\Messages;
use Mailgun\Messages\Exceptions;
use Mailgun\Connection\RestClient;
use Mailgun\Messages\BatchMessage;
use Mailgun\Lists\OptInHandler;
use Mailgun\Messages\MessageBuilder;

/*
   This class is the base class for the Mailgun SDK.
   See the official documentation for usage instructions.
*/

class Mailgun{
    protected $workingDomain;
    protected $restClient;
    protected $apiKey;
    
    public function __construct($apiKey = null, $apiEndpoint = "api.mailgun.net", $apiVersion = "v2", $ssl = true){
        $this->apiKey = $apiKey;
        $this->restClient = new RestClient($apiKey, $apiEndpoint, $apiVersion, $ssl);
    }

    public function sendMessage($workingDomain, $postData, $postFiles = array()){
        /*
         *  This function allows the sending of a fully formed message OR a custom
         *  MIME string. If sending MIME, the string must be passed in to the 3rd
         *  position of the function call.
        */
        if(is_array($postFiles)){
            return $this->post("$workingDomain/messages", $postData, $postFiles);
        }
        else if(is_string($postFiles)){

            $tempFile = tempnam(sys_get_temp_dir(), "MG_TMP_MIME");
            $fileHandle = fopen($tempFile, "w");
            fwrite($fileHandle, $postFiles);

            $result = $this->post("$workingDomain/messages.mime", $postData, array("message" => $tempFile));
            fclose($fileHandle);
            unlink($tempFile);
            return $result;
        }
        else{
            throw new Exceptions\MissingRequiredMIMEParameters(EXCEPTION_MISSING_REQUIRED_MIME_PARAMETERS);
        }
    }
    
    public function verifyWebhookSignature($postData = NULL) {
        /*
         * This function checks the signature in a POST request to see if it is
         * authentic.
         *
         * Pass an array of parameters.  If you pass nothing, $_POST will be
         * used instead.
         *
         * If this function returns FALSE, you must not process the request.
         * You should reject the request with status code 403 Forbidden.
        */
        if(is_null($postData)) {
            $postData = $_POST;
        }
        $hmac = hash_hmac('sha256', "{$postData["timestamp"]}{$postData["token"]}", $this->apiKey);
        $sig = $postData['signature'];
        if(function_exists('hash_equals')) {
            // hash_equals is constant time, but will not be introduced until PHP 5.6
            return hash_equals($hmac, $sig);
        }
        else {
            return ($hmac == $sig);
        }
    }

    public function post($endpointUrl, $postData = array(), $files = array()){
        return $this->restClient->post($endpointUrl, $postData, $files);
    }

    public function get($endpointUrl, $queryString = array()){
        return $this->restClient->get($endpointUrl, $queryString);
    }

    public function delete($endpointUrl){
        return $this->restClient->delete($endpointUrl);
    }

    public function put($endpointUrl, $putData){
        return $this->restClient->put($endpointUrl, $putData);
    }

    public function MessageBuilder(){
        return new MessageBuilder();
    }

    public function OptInHandler(){
        return new OptInHandler();
    }

    public function BatchMessage($workingDomain, $autoSend = true){
        return new BatchMessage($this->restClient, $workingDomain, $autoSend);
    }
}
