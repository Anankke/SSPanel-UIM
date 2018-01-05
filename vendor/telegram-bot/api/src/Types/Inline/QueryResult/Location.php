<?php
/**
 * Created by PhpStorm.
 * User: iGusev
 * Date: 18/04/16
 * Time: 04:00
 */

namespace TelegramBot\Api\Types\Inline\QueryResult;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent;

/**
 * Class Location
 *
 * @see https://core.telegram.org/bots/api#inlinequeryresultlocation
 * Represents a location on a map. By default, the location will be sent by the user.
 * Alternatively, you can use InputMessageContent to send a message with the specified content instead of the location.
 *
 * Note: This will only work in Telegram versions released after 9 April, 2016. Older clients will ignore them.
 *
 * @package TelegramBot\Api\Types\Inline\QueryResult
 */
class Location extends AbstractInlineQueryResult
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['type', 'id', 'latitude', 'longitude', 'title'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'type' => true,
        'id' => true,
        'latitude' => true,
        'longitude' => true,
        'title' => true,
        'thumb_url' => true,
        'thumb_width' => true,
        'thumb_height' => true,
        'reply_markup' => InlineKeyboardMarkup::class,
        'input_message_content' => InputMessageContent::class,
    ];

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $type = 'location';

    /**
     * Location latitude in degrees
     *
     * @var float
     */
    protected $latitude;

    /**
     * Location longitude in degrees
     *
     * @var float
     */
    protected $longitude;

    /**
     * Optional. Url of the thumbnail for the result
     *
     * @var string
     */
    protected $thumbUrl;

    /**
     * Optional. Thumbnail width
     *
     * @var int
     */
    protected $thumbWidth;

    /**
     * Optional. Thumbnail height
     *
     * @var int
     */
    protected $thumbHeight;

    /**
     * Voice constructor
     *
     * @param string $id
     * @param float $latitude
     * @param float $longitude
     * @param string $title
     * @param string $thumbUrl
     * @param int $thumbWidth
     * @param int $thumbHeight
     * @param InlineKeyboardMarkup|null $inlineKeyboardMarkup
     * @param InputMessageContent|null $inputMessageContent
     */
    public function __construct(
        $id,
        $latitude,
        $longitude,
        $title,
        $thumbUrl = null,
        $thumbWidth = null,
        $thumbHeight = null,
        $inlineKeyboardMarkup = null,
        $inputMessageContent = null
    ) {
        parent::__construct($id, $title, $inputMessageContent, $inlineKeyboardMarkup);

        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->thumbUrl = $thumbUrl;
        $this->thumbWidth = $thumbWidth;
        $this->thumbHeight = $thumbHeight;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getThumbUrl()
    {
        return $this->thumbUrl;
    }

    /**
     * @param string $thumbUrl
     */
    public function setThumbUrl($thumbUrl)
    {
        $this->thumbUrl = $thumbUrl;
    }

    /**
     * @return int
     */
    public function getThumbWidth()
    {
        return $this->thumbWidth;
    }

    /**
     * @param int $thumbWidth
     */
    public function setThumbWidth($thumbWidth)
    {
        $this->thumbWidth = $thumbWidth;
    }

    /**
     * @return int
     */
    public function getThumbHeight()
    {
        return $this->thumbHeight;
    }

    /**
     * @param int $thumbHeight
     */
    public function setThumbHeight($thumbHeight)
    {
        $this->thumbHeight = $thumbHeight;
    }
}
