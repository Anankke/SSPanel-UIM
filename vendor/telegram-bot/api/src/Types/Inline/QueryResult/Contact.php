<?php
/**
 * Created by PhpStorm.
 * User: iGusev
 * Date: 14/04/16
 * Time: 03:58
 */

namespace TelegramBot\Api\Types\Inline\QueryResult;

use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent;

class Contact extends AbstractInlineQueryResult
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['type', 'id', 'phone_number', 'first_name'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'type' => true,
        'id' => true,
        'phone_number' => true,
        'first_name' => true,
        'last_name' => true,
        'reply_markup' => InlineKeyboardMarkup::class,
        'input_message_content' => InputMessageContent::class,
        'thumb_url' => true,
        'thumb_width' => true,
        'thumb_height' => true,
    ];

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $type = 'contact';

    /**
     * Contact's phone number
     *
     * @var string
     */
    protected $phoneNumber;

    /**
     * Contact's first name
     *
     * @var string
     */
    protected $firstName;

    /**
     * Optional. Contact's last name
     *
     * @var string
     */
    protected $lastName;

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
     * Contact constructor.
     *
     * @param string $id
     * @param string $phoneNumber
     * @param string $firstName
     * @param string $lastName
     * @param string $thumbUrl
     * @param int $thumbWidth
     * @param int $thumbHeight
     * @param InputMessageContent|null $inputMessageContent
     * @param InlineKeyboardMarkup|null $inlineKeyboardMarkup
     */
    public function __construct(
        $id,
        $phoneNumber,
        $firstName,
        $lastName = null,
        $thumbUrl = null,
        $thumbWidth = null,
        $thumbHeight = null,
        $inputMessageContent = null,
        $inlineKeyboardMarkup = null
    ) {
        parent::__construct($id, null, $inputMessageContent, $inlineKeyboardMarkup);

        $this->phoneNumber = $phoneNumber;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->thumbUrl = $thumbUrl;
        $this->thumbWidth = $thumbWidth;
        $this->thumbHeight = $thumbHeight;
    }


    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
