<?php

namespace TelegramBot\Api\Types\Inline\QueryResult;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent;

/**
 * Class InlineQueryResultPhoto
 * Represents a link to a photo. By default, this photo will be sent by the user with optional caption.
 * Alternatively, you can provide message_text to send it instead of photo.
 *
 * @package TelegramBot\Api\Types\Inline
 */
class Photo extends AbstractInlineQueryResult
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['type', 'id', 'photo_url', 'thumb_url'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'type' => true,
        'id' => true,
        'photo_url' => true,
        'thumb_url' => true,
        'photo_width' => true,
        'photo_height' => true,
        'title' => true,
        'description' => true,
        'caption' => true,
        'input_message_content' => InputMessageContent::class,
        'reply_markup' => InlineKeyboardMarkup::class,
    ];

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $type = 'photo';

    /**
     * A valid URL of the photo. Photo size must not exceed 5MB
     *
     * @var string
     */
    protected $photoUrl;

    /**
     * Optional. Width of the photo
     *
     * @var int
     */
    protected $photoWidth;

    /**
     * Optional. Height of the photo
     *
     * @var int
     */
    protected $photoHeight;

    /**
     * URL of the thumbnail for the photo
     *
     * @var
     */
    protected $thumbUrl;

    /**
     * Optional. Short description of the result
     *
     * @var string
     */
    protected $description;

    /**
     * Optional. Caption of the photo to be sent, 0-200 characters
     *
     * @var string
     */
    protected $caption;

    /**
     * InlineQueryResultPhoto constructor.
     *
     * @param string $id
     * @param string $photoUrl
     * @param string $thumbUrl
     * @param int|null $photoWidth
     * @param int|null $photoHeight
     * @param string|null $title
     * @param string|null $description
     * @param string|null $caption
     * @param InputMessageContent|null $inputMessageContent
     * @param InlineKeyboardMarkup|null $inlineKeyboardMarkup
     */
    public function __construct(
        $id,
        $photoUrl,
        $thumbUrl,
        $photoWidth = null,
        $photoHeight = null,
        $title = null,
        $description = null,
        $caption = null,
        $inputMessageContent = null,
        $inlineKeyboardMarkup = null
    ) {
        parent::__construct($id, $title, $inputMessageContent, $inlineKeyboardMarkup);

        $this->photoUrl = $photoUrl;
        $this->thumbUrl = $thumbUrl;
        $this->photoWidth = $photoWidth;
        $this->photoHeight = $photoHeight;
        $this->description = $description;
        $this->caption = $caption;
    }


    /**
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * @param string $photoUrl
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;
    }

    /**
     * @return int
     */
    public function getPhotoWidth()
    {
        return $this->photoWidth;
    }

    /**
     * @param int $photoWidth
     */
    public function setPhotoWidth($photoWidth)
    {
        $this->photoWidth = $photoWidth;
    }

    /**
     * @return int
     */
    public function getPhotoHeight()
    {
        return $this->photoHeight;
    }

    /**
     * @param int $photoHeight
     */
    public function setPhotoHeight($photoHeight)
    {
        $this->photoHeight = $photoHeight;
    }

    /**
     * @return mixed
     */
    public function getThumbUrl()
    {
        return $this->thumbUrl;
    }

    /**
     * @param mixed $thumbUrl
     */
    public function setThumbUrl($thumbUrl)
    {
        $this->thumbUrl = $thumbUrl;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }
}
