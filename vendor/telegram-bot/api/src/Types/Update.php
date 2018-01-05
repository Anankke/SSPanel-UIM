<?php

namespace TelegramBot\Api\Types;

use TelegramBot\Api\BaseType;
use TelegramBot\Api\TypeInterface;
use TelegramBot\Api\Types\Inline\ChosenInlineResult;
use TelegramBot\Api\Types\Inline\InlineQuery;

/**
 * Class Update
 * This object represents an incoming update.
 * Only one of the optional parameters can be present in any given update.
 *
 * @package TelegramBot\Api\Types
 */
class Update extends BaseType implements TypeInterface
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['update_id'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'update_id' => true,
        'message' => Message::class,
        'inline_query' => InlineQuery::class,
        'chosen_inline_result' => ChosenInlineResult::class,
    ];

    /**
     * The update‘s unique identifier.
     * Update identifiers start from a certain positive number and increase sequentially.
     * This ID becomes especially handy if you’re using Webhooks, since it allows you to ignore repeated updates or
     * to restore the correct update sequence, should they get out of order.
     *
     * @var integer
     */
    protected $updateId;

    /**
     * Optional. New incoming message of any kind — text, photo, sticker, etc.
     *
     * @var Message
     */
    protected $message;

    /**
     * Optional. New incoming inline query
     *
     * @var \TelegramBot\Api\Types\Inline\InlineQuery
     */
    protected $inlineQuery;

    /**
     * Optional. The result of a inline query that was chosen by a user and sent to their chat partner
     *
     * @var \TelegramBot\Api\Types\Inline\ChosenInlineResult
     */
    protected $chosenInlineResult;

    /**
     * @return int
     */
    public function getUpdateId()
    {
        return $this->updateId;
    }

    /**
     * @param int $updateId
     */
    public function setUpdateId($updateId)
    {
        $this->updateId = $updateId;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param Message $message
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return InlineQuery
     */
    public function getInlineQuery()
    {
        return $this->inlineQuery;
    }

    /**
     * @param InlineQuery $inlineQuery
     */
    public function setInlineQuery($inlineQuery)
    {
        $this->inlineQuery = $inlineQuery;
    }

    /**
     * @return ChosenInlineResult
     */
    public function getChosenInlineResult()
    {
        return $this->chosenInlineResult;
    }

    /**
     * @param ChosenInlineResult $chosenInlineResult
     */
    public function setChosenInlineResult($chosenInlineResult)
    {
        $this->chosenInlineResult = $chosenInlineResult;
    }
}
