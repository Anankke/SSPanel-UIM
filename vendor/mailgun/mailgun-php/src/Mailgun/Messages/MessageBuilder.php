<?PHP

namespace Mailgun\Messages;

use Mailgun\Messages\Exceptions\InvalidParameter;
use Mailgun\Messages\Exceptions\TooManyParameters;
use Mailgun\Messages\Exceptions\InvalidParameterType;

/*
   This class is used for composing a properly formed
   message object. Dealing with arrays can be cumbersome,
   this class makes the process easier. See the official
   documentation for usage instructions.
*/

class MessageBuilder
{

    protected $message = array();
    protected $variables = array();
    protected $files = array();
    protected $counters = array(
        'recipients' => array(
            'to'  => 0,
            'cc'  => 0,
            'bcc' => 0
        ),
        'attributes' => array(
            'attachment'    => 0,
            'campaign_id'   => 0,
            'custom_option' => 0,
            'tag'           => 0
        )
    );

    protected function safeGet($params, $key, $default)
    {
        if (array_key_exists($key, $params)) {
            return $params[$key];
        }

        return $default;
    }

    protected function getFullName($params)
    {
        if (array_key_exists("first", $params)) {
            $first = $this->safeGet($params, "first", "");
            $last  = $this->safeGet($params, "last", "");

            return trim("$first $last");
        }

        return $this->safeGet($params, "full_name", "");
    }

    protected function parseAddress($address, $variables)
    {
        if (!is_array($variables)) {
            return $address;
        }
        $fullName = $this->getFullName($variables);
        if ($fullName != null) {
            return "'$fullName' <$address>";
        }

        return $address;
    }

    protected function addRecipient($headerName, $address, $variables)
    {
        $compiledAddress = $this->parseAddress($address, $variables);

        if (isset($this->message[$headerName])) {
            array_push($this->message[$headerName], $compiledAddress);
        } elseif ($headerName == "h:reply-to") {
            $this->message[$headerName] = $compiledAddress;
        } else {
            $this->message[$headerName] = array($compiledAddress);
        }
        if (array_key_exists($headerName, $this->counters['recipients'])) {
            $this->counters['recipients'][$headerName] += 1;
        }
    }

    public function addToRecipient($address, $variables = null)
    {
        if ($this->counters['recipients']['to'] > RECIPIENT_COUNT_LIMIT) {
            throw new TooManyParameters(TOO_MANY_PARAMETERS_RECIPIENT);
        }
        $this->addRecipient("to", $address, $variables);

        return end($this->message['to']);
    }

    public function addCcRecipient($address, $variables = null)
    {
        if ($this->counters['recipients']['cc'] > RECIPIENT_COUNT_LIMIT) {
            throw new TooManyParameters(TOO_MANY_PARAMETERS_RECIPIENT);
        }
        $this->addRecipient("cc", $address, $variables);

        return end($this->message['cc']);
    }

    public function addBccRecipient($address, $variables = null)
    {
        if ($this->counters['recipients']['bcc'] > RECIPIENT_COUNT_LIMIT) {
            throw new TooManyParameters(TOO_MANY_PARAMETERS_RECIPIENT);
        }
        $this->addRecipient("bcc", $address, $variables);

        return end($this->message['bcc']);
    }

    public function setFromAddress($address, $variables = null)
    {
        $this->addRecipient("from", $address, $variables);

        return $this->message['from'];
    }

    public function setReplyToAddress($address, $variables = null)
    {
        $this->addRecipient("h:reply-to", $address, $variables);

        return $this->message['h:reply-to'];
    }

    public function setSubject($subject = null)
    {
        if ($subject == null || $subject == "") {
            $subject = " ";
        }
        $this->message['subject'] = $subject;

        return $this->message['subject'];
    }

    public function addCustomHeader($headerName, $headerData)
    {
        if (!preg_match("/^h:/i", $headerName)) {
            $headerName = "h:" . $headerName;
        }
        $this->message[$headerName] = array($headerData);

        return $this->message[$headerName];
    }

    public function setTextBody($textBody)
    {
        if ($textBody == null || $textBody == "") {
            $textBody = " ";
        }
        $this->message['text'] = $textBody;

        return $this->message['text'];
    }

    public function setHtmlBody($htmlBody)
    {
        if ($htmlBody == null || $htmlBody == "") {
            $htmlBody = " ";
        }
        $this->message['html'] = $htmlBody;

        return $this->message['html'];
    }

    public function addAttachment($attachmentPath, $attachmentName = null)
    {
        if (isset($this->files["attachment"])) {
            $attachment = array(
                'filePath'   => $attachmentPath,
                'remoteName' => $attachmentName
            );
            array_push($this->files["attachment"], $attachment);
        } else {
            $this->files["attachment"] = array(
                array(
                    'filePath'   => $attachmentPath,
                    'remoteName' => $attachmentName
                )
            );
        }

        return true;
    }

    public function addInlineImage($inlineImagePath, $inlineImageName = null)
    {
        if (preg_match("/^@/", $inlineImagePath)) {
            if (isset($this->files['inline'])) {
                $inlineAttachment = array(
                    'filePath'   => $inlineImagePath,
                    'remoteName' => $inlineImageName
                );
                array_push($this->files['inline'], $inlineAttachment);
            } else {
                $this->files['inline'] = array(
                    array(
                        'filePath'   => $inlineImagePath,
                        'remoteName' => $inlineImageName
                    )
                );
            }

            return true;
        } else {
            throw new InvalidParameter(INVALID_PARAMETER_INLINE);
        }
    }

    public function setTestMode($testMode)
    {
        if (filter_var($testMode, FILTER_VALIDATE_BOOLEAN)) {
            $testMode = "yes";
        } else {
            $testMode = "no";
        }
        $this->message['o:testmode'] = $testMode;

        return $this->message['o:testmode'];
    }

    public function addCampaignId($campaignId)
    {
        if ($this->counters['attributes']['campaign_id'] < CAMPAIGN_ID_LIMIT) {
            if (isset($this->message['o:campaign'])) {
                array_push($this->message['o:campaign'], $campaignId);
            } else {
                $this->message['o:campaign'] = array($campaignId);
            }
            $this->counters['attributes']['campaign_id'] += 1;

            return $this->message['o:campaign'];
        } else {
            throw new TooManyParameters(TOO_MANY_PARAMETERS_CAMPAIGNS);
        }
    }

    public function addTag($tag)
    {
        if ($this->counters['attributes']['tag'] < TAG_LIMIT) {
            if (isset($this->message['o:tag'])) {
                array_push($this->message['o:tag'], $tag);
            } else {
                $this->message['o:tag'] = array($tag);
            }
            $this->counters['attributes']['tag'] += 1;

            return $this->message['o:tag'];
        } else {
            throw new TooManyParameters(TOO_MANY_PARAMETERS_TAGS);
        }
    }

    public function setDkim($enabled)
    {
        if (filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) {
            $enabled = "yes";
        } else {
            $enabled = "no";
        }
        $this->message["o:dkim"] = $enabled;

        return $this->message["o:dkim"];
    }

    public function setOpenTracking($enabled)
    {
        if (filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) {
            $enabled = "yes";
        } else {
            $enabled = "no";
        }
        $this->message['o:tracking-opens'] = $enabled;

        return $this->message['o:tracking-opens'];
    }

    public function setClickTracking($enabled)
    {
        if (filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) {
            $enabled = "yes";
        } elseif ($enabled == "html") {
            $enabled = "html";
        } else {
            $enabled = "no";
        }
        $this->message['o:tracking-clicks'] = $enabled;

        return $this->message['o:tracking-clicks'];
    }

    public function setDeliveryTime($timeDate, $timeZone = null)
    {
        if (isset($timeZone)) {
            $timeZoneObj = new \DateTimeZone("$timeZone");
        } else {
            $timeZoneObj = new \DateTimeZone(\DEFAULT_TIME_ZONE);
        }

        $dateTimeObj                     = new \DateTime($timeDate, $timeZoneObj);
        $formattedTimeDate               = $dateTimeObj->format(\DateTime::RFC2822);
        $this->message['o:deliverytime'] = $formattedTimeDate;

        return $this->message['o:deliverytime'];
    }

    public function addCustomData($customName, $data)
    {
        $this->message['v:' . $customName] = json_encode($data);
    }

    public function addCustomParameter($parameterName, $data)
    {
        if (isset($this->message[$parameterName])) {
            array_push($this->message[$parameterName], $data);

            return $this->message[$parameterName];
        } else {
            $this->message[$parameterName] = array($data);

            return $this->message[$parameterName];
        }
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getFiles()
    {
        return $this->files;
    }
}
