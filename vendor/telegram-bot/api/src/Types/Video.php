<?php

namespace TelegramBot\Api\Types;

use TelegramBot\Api\BaseType;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\TypeInterface;

/**
 * Class Video
 * This object represents a video file.
 *
 * @package TelegramBot\Api\Types
 */
class Video extends BaseType implements TypeInterface
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['file_id', 'width', 'height', 'duration'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'file_id' => true,
        'width' => true,
        'height' => true,
        'duration' => true,
        'thumb' => PhotoSize::class,
        'mime_type' => true,
        'file_size' => true
    ];

    /**
     * Unique identifier for this file
     *
     * @var string
     */
    protected $fileId;

    /**
     * Video width as defined by sender
     *
     * @var int
     */
    protected $width;

    /**
     * Video height as defined by sender
     *
     * @var int
     */
    protected $height;

    /**
     * Duration of the video in seconds as defined by sender
     *
     * @var int
     */
    protected $duration;

    /**
     * Video thumbnail
     *
     * @var PhotoSize
     */
    protected $thumb;


    /**
     * Optional. Mime type of a file as defined by sender
     *
     * @var string
     */
    protected $mimeType;

    /**
     * Optional. File size
     *
     * @var int
     */
    protected $fileSize;

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @throws InvalidArgumentException
     */
    public function setDuration($duration)
    {
        if (is_integer($duration)) {
            $this->duration = $duration;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @param string $fileId
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     *
     * @throws InvalidArgumentException
     */
    public function setFileSize($fileSize)
    {
        if (is_integer($fileSize)) {
            $this->fileSize = $fileSize;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     *
     * @throws InvalidArgumentException
     */
    public function setHeight($height)
    {
        if (is_integer($height)) {
            $this->height = $height;
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return PhotoSize
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param PhotoSize $thumb
     */
    public function setThumb(PhotoSize $thumb)
    {
        $this->thumb = $thumb;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @throws InvalidArgumentException
     */
    public function setWidth($width)
    {
        if (is_integer($width)) {
            $this->width = $width;
        } else {
            throw new InvalidArgumentException();
        }
    }
}
