<?php

namespace TelegramBot\Api;

use TelegramBot\Api\Types\ArrayOfUpdates;
use TelegramBot\Api\Types\File;
use TelegramBot\Api\Types\Inline\QueryResult\AbstractInlineQueryResult;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\User;
use TelegramBot\Api\Types\UserProfilePhotos;

/**
 * Class BotApi
 *
 * @package TelegramBot\Api
 */
class BotApi
{
    /**
     * HTTP codes
     *
     * @var array
     */
    public static $codes = [
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];


    /**
     * Default http status code
     */
    const DEFAULT_STATUS_CODE = 200;

    /**
     * Not Modified http status code
     */
    const NOT_MODIFIED_STATUS_CODE = 304;

    /**
     * Limits for tracked ids
     */
    const MAX_TRACKED_EVENTS = 200;

    /**
     * Url prefixes
     */
    const URL_PREFIX = 'https://api.telegram.org/bot';

    /**
     * Url prefix for files
     */
    const FILE_URL_PREFIX = 'https://api.telegram.org/file/bot';

    /**
     * CURL object
     *
     * @var
     */
    protected $curl;

    /**
     * Bot token
     *
     * @var string
     */
    protected $token;

    /**
     * Botan tracker
     *
     * @var \TelegramBot\Api\Botan
     */
    protected $tracker;

    /**
     * list of event ids
     *
     * @var array
     */
    protected $trackedEvents = [];

    /**
     * Check whether return associative array
     *
     * @var bool
     */
    protected $returnArray = true;


    /**
     * Constructor
     *
     * @param string $token Telegram Bot API token
     * @param string|null $trackerToken Yandex AppMetrica application api_key
     */
    public function __construct($token, $trackerToken = null)
    {
        $this->curl = curl_init();
        $this->token = $token;

        if ($trackerToken) {
            $this->tracker = new Botan($trackerToken);
        }
    }

    /**
     * Set return array
     *
     * @param bool $mode
     *
     * @return $this
     */
    public function setModeObject($mode = true)
    {
        $this->returnArray = !$mode;

        return $this;
    }


    /**
     * Call method
     *
     * @param string $method
     * @param array|null $data
     *
     * @return mixed
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\HttpException
     * @throws \TelegramBot\Api\InvalidJsonException
     */
    public function call($method, array $data = null)
    {
        $options = [
            CURLOPT_URL => $this->getUrl().'/'.$method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => null,
            CURLOPT_POSTFIELDS => null,
        ];

        if ($data) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        }

        $response = self::jsonValidate($this->executeCurl($options), $this->returnArray);

        if ($this->returnArray) {
            if (!isset($response['ok'])) {
                throw new Exception($response['description'], $response['error_code']);
            }

            return $response['result'];
        }

        if (!$response->ok) {
            throw new Exception($response->description, $response->error_code);
        }

        return $response->result;
    }

    /**
     * curl_exec wrapper for response validation
     *
     * @param array $options
     *
     * @return string
     *
     * @throws \TelegramBot\Api\HttpException
     */
    protected function executeCurl(array $options)
    {
        curl_setopt_array($this->curl, $options);

        $result = curl_exec($this->curl);
        self::curlValidate($this->curl);
        if ($result === false) {
            throw new HttpException(curl_error($this->curl), curl_errno($this->curl));
        }

        return $result;
    }

    /**
     * Response validation
     *
     * @param resource $curl
     *
     * @throws \TelegramBot\Api\HttpException
     */
    public static function curlValidate($curl)
    {
        if (($httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE))
            && !in_array($httpCode, [self::DEFAULT_STATUS_CODE, self::NOT_MODIFIED_STATUS_CODE])
        ) {
            throw new HttpException(self::$codes[$httpCode], $httpCode);
        }
    }

    /**
     * JSON validation
     *
     * @param string $jsonString
     * @param boolean $asArray
     *
     * @return object|array
     * @throws \TelegramBot\Api\InvalidJsonException
     */
    public static function jsonValidate($jsonString, $asArray)
    {
        $json = json_decode($jsonString, $asArray);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidJsonException(json_last_error_msg(), json_last_error());
        }

        return $json;
    }

    /**
     * Use this method to send text messages. On success, the sent \TelegramBot\Api\Types\Message is returned.
     *
     * @param int|string $chatId
     * @param string $text
     * @param string|null $parseMode
     * @param bool $disablePreview
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function sendMessage(
        $chatId,
        $text,
        $parseMode = null,
        $disablePreview = false,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => $disablePreview,
            'reply_to_message_id' => (int)$replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send phone contacts
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param string $phoneNumber
     * @param string $firstName
     * @param string $lastName
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     */
    public function sendContact(
        $chatId,
        $phoneNumber,
        $firstName,
        $lastName = null,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendContact', [
            'chat_id' => $chatId,
            'phone_number' => $phoneNumber,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method when you need to tell the user that something is happening on the bot's side.
     * The status is set for 5 seconds or less (when a message arrives from your bot,
     * Telegram clients clear its typing status).
     *
     * We only recommend using this method when a response from the bot will take a noticeable amount of time to arrive.
     *
     * Type of action to broadcast. Choose one, depending on what the user is about to receive:
     * `typing` for text messages, `upload_photo` for photos, `record_video` or `upload_video` for videos,
     * `record_audio` or upload_audio for audio files, `upload_document` for general files,
     * `find_location` for location data.
     *
     * @param int $chatId
     * @param string $action
     *
     * @return bool
     * @throws \TelegramBot\Api\Exception
     */
    public function sendChatAction($chatId, $action)
    {
        return $this->call('sendChatAction', [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
    }

    /**
     * Use this method to get a list of profile pictures for a user.
     *
     * @param int $userId
     * @param int $offset
     * @param int $limit
     *
     * @return \TelegramBot\Api\Types\UserProfilePhotos
     * @throws \TelegramBot\Api\Exception
     */
    public function getUserProfilePhotos($userId, $offset = 0, $limit = 100)
    {
        return UserProfilePhotos::fromResponse($this->call('getUserProfilePhotos', [
            'user_id' => (int)$userId,
            'offset' => (int)$offset,
            'limit' => (int)$limit,
        ]));
    }

    /**
     * Use this method to specify a url and receive incoming updates via an outgoing webhook.
     * Whenever there is an update for the bot, we will send an HTTPS POST request to the specified url,
     * containing a JSON-serialized Update.
     * In case of an unsuccessful request, we will give up after a reasonable amount of attempts.
     *
     * @param string $url HTTPS url to send updates to. Use an empty string to remove webhook integration
     * @param \CURLFile|string $certificate Upload your public key certificate
     *                                      so that the root certificate in use can be checked
     *
     * @return string
     *
     * @throws \TelegramBot\Api\Exception
     */
    public function setWebhook($url = '', $certificate = null)
    {
        return $this->call('setWebhook', ['url' => $url, 'certificate' => $certificate]);
    }

    /**
     * A simple method for testing your bot's auth token.Requires no parameters.
     * Returns basic information about the bot in form of a User object.
     *
     * @return \TelegramBot\Api\Types\User
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function getMe()
    {
        return User::fromResponse($this->call('getMe'));
    }

    /**
     * Use this method to receive incoming updates using long polling.
     * An Array of Update objects is returned.
     *
     * Notes
     * 1. This method will not work if an outgoing webhook is set up.
     * 2. In order to avoid getting duplicate updates, recalculate offset after each server response.
     *
     * @param int $offset
     * @param int $limit
     * @param int $timeout
     *
     * @return Update[]
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function getUpdates($offset = 0, $limit = 100, $timeout = 0)
    {
        $updates = ArrayOfUpdates::fromResponse($this->call('getUpdates', [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout,
        ]));

        if ($this->tracker instanceof Botan) {
            foreach ($updates as $update) {
                $this->trackUpdate($update);
            }
        }

        return $updates;
    }

    /**
     * Use this method to send point on the map. On success, the sent Message is returned.
     *
     * @param int $chatId
     * @param float $latitude
     * @param float $longitude
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     */
    public function sendLocation(
        $chatId,
        $latitude,
        $longitude,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendLocation', [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send information about a venue. On success, the sent Message is returned.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param float $latitude
     * @param float $longitude
     * @param string $title
     * @param string $address
     * @param string|null $foursquareId
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\Exception
     */
    public function sendVenue(
        $chatId,
        $latitude,
        $longitude,
        $title,
        $address,
        $foursquareId = null,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendVenue', [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'title' => $title,
            'address' => $address,
            'foursquare_id' => $foursquareId,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send .webp stickers. On success, the sent Message is returned.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param \CURLFile|string $sticker
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function sendSticker(
        $chatId,
        $sticker,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendSticker', [
            'chat_id' => $chatId,
            'sticker' => $sticker,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send video files,
     * Telegram clients support mp4 videos (other formats may be sent as Document).
     * On success, the sent Message is returned.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param \CURLFile|string $video
     * @param int|null $duration
     * @param string|null $caption
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function sendVideo(
        $chatId,
        $video,
        $duration = null,
        $caption = null,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendVideo', [
            'chat_id' => $chatId,
            'video' => $video,
            'duration' => $duration,
            'caption' => $caption,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send audio files,
     * if you want Telegram clients to display the file as a playable voice message.
     * For this to work, your audio must be in an .ogg file encoded with OPUS
     * (other formats may be sent as Audio or Document).
     * On success, the sent Message is returned.
     * Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param \CURLFile|string $voice
     * @param int|null $duration
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function sendVoice(
        $chatId,
        $voice,
        $duration = null,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendVoice', [
            'chat_id' => $chatId,
            'voice' => $voice,
            'duration' => $duration,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to forward messages of any kind. On success, the sent Message is returned.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param int $fromChatId
     * @param int $messageId
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function forwardMessage($chatId, $fromChatId, $messageId, $disableNotification = false)
    {
        return Message::fromResponse($this->call('forwardMessage', [
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => (int)$messageId,
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send audio files,
     * if you want Telegram clients to display them in the music player.
     * Your audio must be in the .mp3 format.
     * On success, the sent Message is returned.
     * Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     *
     * For backward compatibility, when the fields title and performer are both empty
     * and the mime-type of the file to be sent is not audio/mpeg, the file will be sent as a playable voice message.
     * For this to work, the audio must be in an .ogg file encoded with OPUS.
     * This behavior will be phased out in the future. For sending voice messages, use the sendVoice method instead.
     *
     * @deprecated since 20th February. Removed backward compatibility from the method sendAudio.
     * Voice messages now must be sent using the method sendVoice.
     * There is no more need to specify a non-empty title or performer while sending the audio by file_id.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param \CURLFile|string $audio
     * @param int|null $duration
     * @param string|null $performer
     * @param string|null $title
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function sendAudio(
        $chatId,
        $audio,
        $duration = null,
        $performer = null,
        $title = null,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendAudio', [
            'chat_id' => $chatId,
            'audio' => $audio,
            'duration' => $duration,
            'performer' => $performer,
            'title' => $title,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send photos. On success, the sent Message is returned.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param \CURLFile|string $photo
     * @param string|null $caption
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function sendPhoto(
        $chatId,
        $photo,
        $caption = null,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendPhoto', [
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to send general files. On success, the sent Message is returned.
     * Bots can currently send files of any type of up to 50 MB in size, this limit may be changed in the future.
     *
     * @param int|string $chatId chat_id or @channel_name
     * @param \CURLFile|string $document
     * @param int|null $replyToMessageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     * @param bool $disableNotification
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function sendDocument(
        $chatId,
        $document,
        $replyToMessageId = null,
        $replyMarkup = null,
        $disableNotification = false
    ) {
        return Message::fromResponse($this->call('sendDocument', [
            'chat_id' => $chatId,
            'document' => $document,
            'reply_to_message_id' => $replyToMessageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
            'disable_notification' => (bool)$disableNotification,
        ]));
    }

    /**
     * Use this method to get basic info about a file and prepare it for downloading.
     * For the moment, bots can download files of up to 20MB in size.
     * On success, a File object is returned.
     * The file can then be downloaded via the link https://api.telegram.org/file/bot<token>/<file_path>,
     * where <file_path> is taken from the response.
     * It is guaranteed that the link will be valid for at least 1 hour.
     * When the link expires, a new one can be requested by calling getFile again.
     *
     * @param $fileId
     *
     * @return \TelegramBot\Api\Types\File
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function getFile($fileId)
    {
        return File::fromResponse($this->call('getFile', ['file_id' => $fileId]));
    }

    /**
     * Get file contents via cURL
     *
     * @param $fileId
     *
     * @return string
     *
     * @throws \TelegramBot\Api\HttpException
     */
    public function downloadFile($fileId)
    {
        $file = $this->getFile($fileId);
        $options = [
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPGET => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->getFileUrl().'/'.$file->getFilePath(),
        ];

        return $this->executeCurl($options);
    }

    /**
     * Use this method to send answers to an inline query. On success, True is returned.
     * No more than 50 results per query are allowed.
     *
     * @param string $inlineQueryId
     * @param AbstractInlineQueryResult[] $results
     * @param int $cacheTime
     * @param bool $isPersonal
     * @param string $nextOffset
     *
     * @return mixed
     * @throws Exception
     */
    public function answerInlineQuery($inlineQueryId, $results, $cacheTime = 300, $isPersonal = false, $nextOffset = '')
    {
        $results = array_map(function ($item) {
            /* @var AbstractInlineQueryResult $item */

            return json_decode($item->toJson(), true);
        }, $results);

        return $this->call('answerInlineQuery', [
            'inline_query_id' => $inlineQueryId,
            'results' => json_encode($results),
            'cache_time' => $cacheTime,
            'is_personal' => $isPersonal,
            'next_offset' => $nextOffset,
        ]);
    }

    /**
     * Use this method to kick a user from a group or a supergroup.
     * In the case of supergroups, the user will not be able to return to the group
     * on their own using invite links, etc., unless unbanned first.
     * The bot must be an administrator in the group for this to work. Returns True on success.
     *
     * @param int|string $chatId Unique identifier for the target group
     * or username of the target supergroup (in the format @supergroupusername)
     * @param int $userId Unique identifier of the target user
     *
     * @return bool
     */
    public function kickChatMember($chatId, $userId)
    {
        return $this->call('kickChatMember', [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Use this method to unban a previously kicked user in a supergroup.
     * The user will not return to the group automatically, but will be able to join via link, etc.
     * The bot must be an administrator in the group for this to work. Returns True on success.
     *
     * @param int|string $chatId Unique identifier for the target group
     * or username of the target supergroup (in the format @supergroupusername)
     * @param int $userId Unique identifier of the target user
     *
     * @return bool
     */
    public function unbanChatMember($chatId, $userId)
    {
        return $this->call('unbanChatMember', [
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Use this method to send answers to callback queries sent from inline keyboards.
     * The answer will be displayed to the user as a notification at the top of the chat screen or as an alert.
     *
     * @param $callbackQueryId
     * @param null $text
     * @param bool $showAlert
     *
     * @return bool
     */
    public function answerCallbackQuery($callbackQueryId, $text = null, $showAlert = false)
    {
        return $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => (bool)$showAlert,
        ]);
    }


    /**
     * Use this method to edit text messages sent by the bot or via the bot
     *
     * @param int|string $chatId
     * @param int $messageId
     * @param string $text
     * @param string|null $parseMode
     * @param bool $disablePreview
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function editMessageText(
        $chatId,
        $messageId,
        $text,
        $parseMode = null,
        $disablePreview = false,
        $replyMarkup = null
    ) {
        return Message::fromResponse($this->call('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => $disablePreview,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
        ]));
    }

    /**
     * Use this method to edit text messages sent by the bot or via the bot
     *
     * @param int|string $chatId
     * @param int $messageId
     * @param string|null $caption
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function editMessageCaption(
        $chatId,
        $messageId,
        $caption = null,
        $replyMarkup = null
    ) {
        return Message::fromResponse($this->call('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'caption' => $caption,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
        ]));
    }

    /**
     * Use this method to edit only the reply markup of messages sent by the bot or via the bot
     *
     * @param int|string $chatId
     * @param int $messageId
     * @param Types\ReplyKeyboardMarkup|Types\ReplyKeyboardHide|Types\ForceReply|null $replyMarkup
     *
     * @return \TelegramBot\Api\Types\Message
     * @throws \TelegramBot\Api\InvalidArgumentException
     * @throws \TelegramBot\Api\Exception
     */
    public function editMessageReplyMarkup(
        $chatId,
        $messageId,
        $replyMarkup = null
    ) {
        return Message::fromResponse($this->call('editMessageText', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => is_null($replyMarkup) ? $replyMarkup : $replyMarkup->toJson(),
        ]));
    }

    /**
     * Close curl
     */
    public function __destruct()
    {
        $this->curl && curl_close($this->curl);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return self::URL_PREFIX.$this->token;
    }

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return self::FILE_URL_PREFIX.$this->token;
    }

    /**
     * @param \TelegramBot\Api\Types\Update $update
     * @param string $eventName
     *
     * @throws \TelegramBot\Api\Exception
     */
    public function trackUpdate(Update $update, $eventName = 'Message')
    {
        if (!in_array($update->getUpdateId(), $this->trackedEvents)) {
            $this->trackedEvents[] = $update->getUpdateId();

            $this->track($update->getMessage(), $eventName);

            if (count($this->trackedEvents) > self::MAX_TRACKED_EVENTS) {
                $this->trackedEvents = array_slice($this->trackedEvents, round(self::MAX_TRACKED_EVENTS / 4));
            }
        }
    }

    /**
     * Wrapper for tracker
     *
     * @param \TelegramBot\Api\Types\Message $message
     * @param string $eventName
     *
     * @throws \TelegramBot\Api\Exception
     */
    public function track(Message $message, $eventName = 'Message')
    {
        if ($this->tracker instanceof Botan) {
            $this->tracker->track($message, $eventName);
        }
    }
}
