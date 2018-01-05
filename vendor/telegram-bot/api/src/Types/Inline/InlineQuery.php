<?php

namespace TelegramBot\Api\Types\Inline;

use TelegramBot\Api\BaseType;
use TelegramBot\Api\Types\Location;
use TelegramBot\Api\Types\User;

/**
 * Class InlineQuery
 * This object represents an incoming inline query.
 * When the user sends an empty query, your bot could return some default or trending results.
 *
 * @package TelegramBot\Api\Types
 */
class InlineQuery extends BaseType
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['id', 'from', 'query', 'offset'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'id' => true,
        'from' => User::class,
        'location' => Location::class,
        'query' => true,
        'offset' => true,
    ];

    /**
     * Unique identifier for this query
     *
     * @var string
     */
    protected $id;

    /**
     * Sender
     *
     * @var User
     */
    protected $from;


    /**
     * Optional. Sender location, only for bots that request user location
     *
     * @var Location
     */
    protected $location;

    /**
     * Text of the query
     *
     * @var string
     */
    protected $query;

    /**
     * Offset of the results to be returned, can be controlled by the bot
     *
     * @var string
     */
    protected $offset;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param User $from
     */
    public function setFrom(User $from)
    {
        $this->from = $from;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param string $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }
}
