<?php
/**
 * Created by PhpStorm.
 * User: iGusev
 * Date: 17/04/16
 * Time: 02:12
 */

namespace TelegramBot\Api\Types\Inline\QueryResult;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent;

/**
 * Class Voice
 *
 * @see https://core.telegram.org/bots/api#inlinequeryresultvoice
 * Represents a link to an mp3 audio file. By default, this audio file will be sent by the user.
 * Alternatively, you can use InputMessageContent to send a message with the specified content instead of the audio.
 *
 * Note: This will only work in Telegram versions released after 9 April, 2016. Older clients will ignore them.
 *
 * @package TelegramBot\Api\Types\Inline\QueryResult
 */
class Voice extends AbstractInlineQueryResult
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['type', 'id', 'voice_url', 'title'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'type' => true,
        'id' => true,
        'voice_url' => true,
        'title' => true,
        'voice_duration' => true,
        'reply_markup' => InlineKeyboardMarkup::class,
        'input_message_content' => InputMessageContent::class,
    ];

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $type = 'voice';

    /**
     * A valid URL for the audio file
     *
     * @var string
     */
    protected $voiceUrl;


    /**
     * Optional. Audio duration in seconds
     *
     * @var int
     */
    protected $voiceDuration;

    /**
     * Voice constructor
     *
     * @param string $id
     * @param string $voiceUrl
     * @param string $title
     * @param int|null $voiceDuration
     * @param InlineKeyboardMarkup|null $inlineKeyboardMarkup
     * @param InputMessageContent|null $inputMessageContent
     */
    public function __construct(
        $id,
        $voiceUrl,
        $title,
        $voiceDuration = null,
        $inlineKeyboardMarkup = null,
        $inputMessageContent = null
    ) {
        parent::__construct($id, $title, $inputMessageContent, $inlineKeyboardMarkup);

        $this->voiceUrl = $voiceUrl;
        $this->voiceDuration = $voiceDuration;
        $this->replyMarkup = $inlineKeyboardMarkup;
        $this->inputMessageContent = $inputMessageContent;
    }

    /**
     * @return string
     */
    public function getVoiceUrl()
    {
        return $this->voiceUrl;
    }

    /**
     * @param string $voiceUrl
     */
    public function setVoiceUrl($voiceUrl)
    {
        $this->voiceUrl = $voiceUrl;
    }

    /**
     * @return int
     */
    public function getVoiceDuration()
    {
        return $this->voiceDuration;
    }

    /**
     * @param int $voiceDuration
     */
    public function setVoiceDuration($voiceDuration)
    {
        $this->voiceDuration = $voiceDuration;
    }
}
