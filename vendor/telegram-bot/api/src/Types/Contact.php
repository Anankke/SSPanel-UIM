<?php

namespace TelegramBot\Api\Types;

use TelegramBot\Api\BaseType;
use TelegramBot\Api\TypeInterface;

/**
 * Class Contact
 * This object represents a sticker.
 *
 * @package TelegramBot\Api\Types
 */
class Contact extends BaseType implements TypeInterface
{
    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $requiredParams = ['phone_number', 'first_name'];

    /**
     * {@inheritdoc}
     *
     * @var array
     */
    static protected $map = [
        'phone_number' => true,
        'first_name' => true,
        'last_name' => true,
        'user_id' => true
    ];

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
     * Optional. Contact's user identifier in Telegram
     *
     * @var int
     */
    protected $userId;

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
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
