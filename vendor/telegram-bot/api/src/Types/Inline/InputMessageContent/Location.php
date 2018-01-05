<?php
/**
 * Created by PhpStorm.
 * User: iGusev
 * Date: 14/04/16
 * Time: 15:41
 */

namespace TelegramBot\Api\Types\Inline\InputMessageContent;

use TelegramBot\Api\TypeInterface;
use TelegramBot\Api\Types\Inline\InputMessageContent;

/**
 * Class Location
 * @see https://core.telegram.org/bots/api#inputlocationmessagecontent
 * Represents the content of a location message to be sent as the result of an inline query.
 *
 * @package TelegramBot\Api\Types\Inline
 */
class Location extends InputMessageContent implements TypeInterface
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['latitude', 'longitude'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'latitude' => true,
        'longitude' => true
    ];

    /**
     * Latitude of the location in degrees
     *
     * @var float
     */
    protected $latitude;

    /**
     * Longitude of the location in degrees
     *
     * @var float
     */
    protected $longitude;

    /**
     * Location constructor.
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
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
}
