<?php

namespace TelegramBot\Api\Types;

use TelegramBot\Api\BaseType;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\TypeInterface;

/**
 * Class UserProfilePhotos
 * This object represent a user's profile pictures.
 *
 * @package TelegramBot\Api\Types
 */
class UserProfilePhotos extends BaseType implements TypeInterface
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['total_count', 'photos'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'total_count' => true,
        'photos' => ArrayOfArrayOfPhotoSize::class,
    ];

    /**
     * Total number of profile pictures the target user has
     *
     * @var Integer
     */
    protected $totalCount;

    /**
     * Requested profile pictures (in up to 4 sizes each).
     * Array of Array of \TelegramBot\Api\Types\PhotoSize
     *
     * @var array
     */
    protected $photos;

    /**
     * @return array
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param array $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     *
     * @throws InvalidArgumentException
     */
    public function setTotalCount($totalCount)
    {
        if (is_integer($totalCount)) {
            $this->totalCount = $totalCount;
        } else {
            throw new InvalidArgumentException();
        }
    }
}
