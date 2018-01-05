<?php
/**
 * Created by PhpStorm.
 * User: iGusev
 * Date: 13/04/16
 * Time: 13:55
 */

namespace TelegramBot\Api\Types;

use TelegramBot\Api\BaseType;
use TelegramBot\Api\TypeInterface;

/**
 * Class Venue
 * This object represents a venue
 *
 * @package TelegramBot\Api\Types
 */
class Venue extends BaseType implements TypeInterface
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['location', 'title', 'address'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'location' => Location::class,
        'title' => true,
        'address' => true,
        'foursquare_id' => true,
    ];

    /**
     * Venue location
     *
     * @var Location
     */
    protected $location;

    /**
     * Name of the venue
     *
     * @var string
     */
    protected $title;

    /**
     * Address of the venue
     *
     * @var string
     */
    protected $address;

    /**
     * Optional. Foursquare identifier of the venue
     *
     * @var string
     */
    protected $foursquareId;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getFoursquareId()
    {
        return $this->foursquareId;
    }

    /**
     * @param string $foursquareId
     */
    public function setFoursquareId($foursquareId)
    {
        $this->foursquareId = $foursquareId;
    }
}
