<?php

namespace TelegramBot\Api\Types;

use TelegramBot\Api\BaseType;

/**
 * Class ForceReply
 * Upon receiving a message with this object, Telegram clients will display a reply interface to the user
 * (act as if the user has selected the bot‘s message and tapped ’Reply').This can be extremely useful
 * if you want to create user-friendly step-by-step interfaces without having to sacrifice privacy mode.
 *
 * @package TelegramBot\Api\Types
 */
class ForceReply extends BaseType
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['force_reply'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'force_reply' => true,
        'selective' => true
    ];

    /**
     * Shows reply interface to the user, as if they manually selected the bot‘s message and tapped ’Reply'
     *
     * @var bool
     */
    protected $forceReply;

    /**
     * Optional. Use this parameter if you want to show the keyboard to specific users only.
     * Targets:
     * 1) users that are @mentioned in the text of the Message object;
     * 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     *
     * @var bool
     */
    protected $selective;

    public function __construct($forceReply = true, $selective = null)
    {
        $this->forceReply = $forceReply;
        $this->selective = $selective;
    }

    /**
     * @return boolean
     */
    public function isForceReply()
    {
        return $this->forceReply;
    }

    /**
     * @param boolean $forceReply
     */
    public function setForceReply($forceReply)
    {
        $this->forceReply = $forceReply;
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
