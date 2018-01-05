<?php

namespace TelegramBot\Api\Types\Inline\QueryResult;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent;

/**
 * Class InlineQueryResultArticle
 * Represents a link to an article or web page.
 *
 * @package TelegramBot\Api\Types\Inline
 */
class Article extends AbstractInlineQueryResult
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['type', 'id', 'title', 'input_message_content'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'type' => true,
        'id' => true,
        'title' => true,
        'input_message_content' => InputMessageContent::class,
        'reply_markup' => InlineKeyboardMarkup::class,
        'url' => true,
        'hide_url' => true,
        'description' => true,
        'thumb_url' => true,
        'thumb_width' => true,
        'thumb_height' => true,
    ];

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $type = 'article';

    /**
     * Optional. URL of the result
     *
     * @var string
     */
    protected $url;

    /**
     * Optional. Pass True, if you don't want the URL to be shown in the message
     *
     * @var bool
     */
    protected $hideUrl;

    /**
     * Optional. Short description of the result
     *
     * @var string
     */
    protected $description;

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
     * InlineQueryResultArticle constructor.
     *
     * @param string $id
     * @param string $title
     * @param string|null $description
     * @param string|null $thumbUrl
     * @param int|null $thumbWidth
     * @param int|null $thumbHeight
     * @param InputMessageContent $inputMessageContent
     * @param InlineKeyboardMarkup|null $inlineKeyboardMarkup
     */
    public function __construct(
        $id,
        $title,
        $description = null,
        $thumbUrl = null,
        $thumbWidth = null,
        $thumbHeight = null,
        $inputMessageContent = null,
        $inlineKeyboardMarkup = null
    ) {
        parent::__construct($id, $title, $inputMessageContent, $inlineKeyboardMarkup);

        $this->description = $description;
        $this->thumbUrl = $thumbUrl;
        $this->thumbWidth = $thumbWidth;
        $this->thumbHeight = $thumbHeight;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return boolean
     */
    public function isHideUrl()
    {
        return $this->hideUrl;
    }

    /**
     * @param boolean $hideUrl
     */
    public function setHideUrl($hideUrl)
    {
        $this->hideUrl = $hideUrl;
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
