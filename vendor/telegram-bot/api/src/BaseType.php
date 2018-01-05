<?php

namespace TelegramBot\Api;

/**
 * Class BaseType
 * Base class for Telegram Types
 *
 * @package TelegramBot\Api
 */
abstract class BaseType
{
    /**
     * Array of required data params for type
     *
     * @var array
     */
    protected static $requiredParams = [];

    /**
     * Map of input data
     *
     * @var array
     */
    protected static $map = [];

    /**
     * Validate input data
     *
     * @param array $data
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public static function validate($data)
    {
        if (count(array_intersect_key(array_flip(static::$requiredParams), $data)) === count(static::$requiredParams)) {
            return true;
        }

        throw new InvalidArgumentException();
    }

    public function map($data)
    {
        foreach (static::$map as $key => $item) {
            if (isset($data[$key]) && (!is_array($data[$key]) || (is_array($data[$key]) && !empty($data[$key])))) {
                $method = 'set' . self::toCamelCase($key);
                if ($item === true) {
                    $this->$method($data[$key]);
                } else {
                    $this->$method($item::fromResponse($data[$key]));
                }
            }
        }
    }

    protected static function toCamelCase($str)
    {
        return str_replace(" ", "", ucwords(str_replace("_", " ", $str)));
    }

    public function toJson($inner = false)
    {
        $output = [];

        foreach (static::$map as $key => $item) {
            $property = lcfirst(self::toCamelCase($key));
            if (!is_null($this->$property)) {
                $output[$key] = $item === true ? $this->$property : $this->$property->toJson(true);
            }
        }

        return $inner === false ? json_encode($output) : $output;
    }

    public static function fromResponse($data)
    {
        self::validate($data);
        $instance = new static();
        $instance->map($data);

        return $instance;
    }
}
