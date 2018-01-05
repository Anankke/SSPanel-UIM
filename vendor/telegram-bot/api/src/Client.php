<?php

namespace TelegramBot\Api;

use Closure;
use ReflectionFunction;
use TelegramBot\Api\Events\EventCollection;
use TelegramBot\Api\Types\Update;

/**
 * Class Client
 *
 * @package TelegramBot\Api
 */
class Client
{
    /**
     * RegExp for bot commands
     */
    const REGEXP = '/^\/([^\s@]+)(@\S+)?\s?(.*)$/';

    /**
     * @var \TelegramBot\Api\BotApi
     */
    protected $api;

    /**
     * @var \TelegramBot\Api\Events\EventCollection
     */
    protected $events;

    /**
     * Client constructor
     *
     * @param string $token Telegram Bot API token
     * @param string|null $trackerToken Yandex AppMetrica application api_key
     */
    public function __construct($token, $trackerToken = null)
    {
        $this->api = new BotApi($token);
        $this->events = new EventCollection($trackerToken);
    }

    /**
     * Use this method to add command. Parameters will be automatically parsed and passed to closure.
     *
     * @param string $name
     * @param \Closure $action
     *
     * @return \TelegramBot\Api\Client
     */
    public function command($name, Closure $action)
    {
        return $this->on(self::getEvent($action), self::getChecker($name));
    }

    public function inlineQuery(Closure $action)
    {
        return $this->on(self::getInlineQueryEvent($action), self::getInlineQueryChecker());
    }

    /**
     * Use this method to add an event.
     * If second closure will return true (or if you are passed null instead of closure), first one will be executed.
     *
     * @param \Closure $event
     * @param \Closure|null $checker
     *
     * @return \TelegramBot\Api\Client
     */
    public function on(Closure $event, Closure $checker = null)
    {
        $this->events->add($event, $checker);

        return $this;
    }

    /**
     * Handle updates
     *
     * @param Update[] $updates
     */
    public function handle(array $updates)
    {
        foreach ($updates as $update) {
            /* @var \TelegramBot\Api\Types\Update $update */
            $this->events->handle($update);
        }
    }

    /**
     * Webhook handler
     *
     * @return array
     * @throws \TelegramBot\Api\InvalidJsonException
     */
    public function run()
    {
        if ($data = BotApi::jsonValidate($this->getRawBody(), true)) {
            $this->handle([Update::fromResponse($data)]);
        }
    }

    public function getRawBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * Returns event function to handling the command.
     *
     * @param \Closure $action
     *
     * @return \Closure
     */
    protected static function getEvent(Closure $action)
    {
        return function (Update $update) use ($action) {
            $message = $update->getMessage();
            if (!$message) {
                return true;
            }

            preg_match(self::REGEXP, $message->getText(), $matches);

            if (isset($matches[3]) && !empty($matches[3])) {
                $parameters = str_getcsv($matches[3], chr(32));
            } else {
                $parameters = [];
            }

            array_unshift($parameters, $message);

            $action = new ReflectionFunction($action);

            if (count($parameters) >= $action->getNumberOfRequiredParameters()) {
                $action->invokeArgs($parameters);
            }

            return false;
        };
    }

    protected static function getInlineQueryEvent(Closure $action)
    {
        return function (Update $update) use ($action) {
            if (!$update->getInlineQuery()) {
                return true;
            }

            $reflectionAction = new ReflectionFunction($action);
            $reflectionAction->invokeArgs([$update->getInlineQuery()]);
            return false;
        };
    }

    /**
     * Returns check function to handling the command.
     *
     * @param string $name
     *
     * @return \Closure
     */
    protected static function getChecker($name)
    {
        return function (Update $update) use ($name) {
            $message = $update->getMessage();
            if (is_null($message) || !strlen($message->getText())) {
                return false;
            }

            preg_match(self::REGEXP, $message->getText(), $matches);

            return !empty($matches) && $matches[1] == $name;
        };
    }

    /**
     * Returns check function to handling the inline queries.
     *
     * @return Closure
     */
    protected static function getInlineQueryChecker()
    {
        return function (Update $update) {
            return !is_null($update->getInlineQuery());
        };
    }


    public function __call($name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        } elseif (method_exists($this->api, $name)) {
            return call_user_func_array([$this->api, $name], $arguments);
        }
        throw new BadMethodCallException("Method {$name} not exists");
    }
}
