<?php

namespace TelegramBot\Api\Types;

use TelegramBot\Api\BaseType;

/**
 * Class ReplyKeyboardMarkup
 * This object represents a custom keyboard with reply options (see Introduction to bots for details and examples).
 *
 * @package TelegramBot\Api\Types
 */
class ReplyKeyboardMarkup extends BaseType
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['keyboard'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'keyboard' => true,
        'one_time_keyboard' => true,
        'resize_keyboard' => true,
        'selective' => true
    ];

    /**
     * Array of button rows, each represented by an Array of Strings
     * Array of Array of String
     *
     * @var array
     */
    protected $keyboard;

    /**
     * Optional. Requests clients to resize the keyboard vertically for optimal fit
     * (e.g., make the keyboard smaller if there are just two rows of buttons).
     * Defaults to false, in which case the custom keyboard is always of the same height as the app's standard keyboard.
     *
     * @var bool
     */
    protected $resizeKeyboard;

    /**
     * Optional. Requests clients to hide the keyboard as soon as it's been used. Defaults to false.
     *
     * @var bool
     */
    protected $oneTimeKeyboard;

    /**
     * Optional. Use this parameter if you want to show the keyboard to specific users only.
     * Targets:
     * 1) users that are @mentioned in the text of the Message object;
     * 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     *
     * @var bool
     */
    protected $selective;

    public function __construct($keyboard, $oneTimeKeyboard = null, $resizeKeyboard = null, $selective = null)
    {
        $this->keyboard = $keyboard;
        $this->oneTimeKeyboard = $oneTimeKeyboard;
        $this->resizeKeyboard = $resizeKeyboard;
        $this->selective = $selective;
    }

    /**
     * @return array
     */
    public function getKeyboard()
    {
        return $this->keyboard;
    }

    /**
     * @param array $keyboard
     */
    public function setKeyboard($keyboard)
    {
        $this->keyboard = $keyboard;
    }

    /**
     * @return boolean
     */
    public function isOneTimeKeyboard()
    {
        return $this->oneTimeKeyboard;
    }

    /**
     * @param boolean $oneTimeKeyboard
     */
    public function setOneTimeKeyboard($oneTimeKeyboard)
    {
        $this->oneTimeKeyboard = $oneTimeKeyboard;
    }

    /**
     * @return boolean
     */
    public function isResizeKeyboard()
    {
        return $this->resizeKeyboard;
    }

    /**
     * @param boolean $resizeKeyboard
     */
    public function setResizeKeyboard($resizeKeyboard)
    {
        $this->resizeKeyboard = $resizeKeyboard;
    }

    /**
     * @return boolean
     */
    public function isSelective()
    {
        return $this->selective;
    }

    /**
     * @param boolean $selective
     */
    public function setSelective($selective)
    {
        $this->selective = $selective;
    }
}
