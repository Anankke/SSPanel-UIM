<?php
/**
 * Created by PhpStorm.
 * User: iGusev
 * Date: 14/04/16
 * Time: 16:53
 */

namespace TelegramBot\Api\Types\Inline\QueryResult;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent;

/**
 * Class Audio
 *
 * @see https://core.telegram.org/bots/api#inlinequeryresultaudio
 * Represents a link to an mp3 audio file. By default, this audio file will be sent by the user.
 * Alternatively, you can use InputMessageContent to send a message with the specified content instead of the audio.
 *
 * Note: This will only work in Telegram versions released after 9 April, 2016. Older clients will ignore them.
 *
 * @package TelegramBot\Api\Types\Inline\QueryResult
 */
class Audio extends AbstractInlineQueryResult
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['type', 'id', 'audio_url', 'title'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'type' => true,
        'id' => true,
        'audio_url' => true,
        'title' => true,
        'performer' => true,
        'audio_duration' => true,
        'reply_markup' => InlineKeyboardMarkup::class,
        'input_message_content' => InputMessageContent::class,
    ];

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $type = 'audio';

    /**
     * A valid URL for the audio file
     *
     * @var string
     */
    protected $audioUrl;

    /**
     * Optional. Performer
     *
     * @var string
     */
    protected $performer;

    /**
     * Optional. Audio duration in seconds
     *
     * @var int
     */
    protected $audioDuration;

    /**
     * Audio constructor.
     *
     * @param string $id
     * @param string $audioUrl
     * @param string $title
     * @param string|null $performer
     * @param int|null $audioDuration
     * @param InputMessageContent|null $inputMessageContent
     * @param InlineKeyboardMarkup|null $inlineKeyboardMarkup
     */
    public function __construct(
        $id,
        $audioUrl,
        $title,
        $performer = null,
        $audioDuration = null,
        $inputMessageContent = null,
        $inlineKeyboardMarkup = null
    ) {
        parent::__construct($id, $title, $inputMessageContent, $inlineKeyboardMarkup);

        $this->audioUrl = $audioUrl;
        $this->performer = $performer;
        $this->audioDuration = $audioDuration;
    }

    /**
     * @return string
     */
    public function getAudioUrl()
    {
        return $this->audioUrl;
    }

    /**
     * @param string $audioUrl
     */
    public function setAudioUrl($audioUrl)
    {
        $this->audioUrl = $audioUrl;
    }

    /**
     * @return string
     */
    public function getPerformer()
    {
        return $this->performer;
    }

    /**
     * @param string $performer
     */
    public function setPerformer($performer)
    {
        $this->performer = $performer;
    }

    /**
     * @return int
     */
    public function getAudioDuration()
    {
        return $this->audioDuration;
    }

    /**
     * @param int $audioDuration
     */
    public function setAudioDuration($audioDuration)
    {
        $this->audioDuration = $audioDuration;
    }
}
