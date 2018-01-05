<?php

namespace TelegramBot\Api\Events;

use Closure;
use ReflectionFunction;
use TelegramBot\Api\Botan;
use TelegramBot\Api\Types\Update;

class EventCollection
{
    /**
     * Array of events.
     *
     * @var array
     */
    protected $events;

    /**
     * Botan tracker
     *
     * @var \TelegramBot\Api\Botan
     */
    protected $tracker;

    /**
     * EventCollection constructor.
     *
     * @param string $trackerToken
     */
    public function __construct($trackerToken = null)
    {
        if ($trackerToken) {
            $this->tracker = new Botan($trackerToken);
        }
    }


    /**
     * Add new event to collection
     *
     * @param Closure $event
     * @param Closure|null $checker
     *
     * @return \TelegramBot\Api\Events\EventCollection
     */
    public function add(Closure $event, $checker = null)
    {
        $this->events[] = !is_null($checker) ? new Event($event, $checker)
            : new Event($event, function () {
            });

        return $this;
    }

    /**
     * @param \TelegramBot\Api\Types\Update
     */
    public function handle(Update $update)
    {
        foreach ($this->events as $event) {
            /* @var \TelegramBot\Api\Events\Event $event */
            if ($event->executeChecker($update) === true) {
                if (false === $event->executeAction($update)) {
                    if (!is_null($this->tracker)) {
                        $checker = new ReflectionFunction($event->getChecker());
                        $this->tracker->track($update->getMessage(), $checker->getStaticVariables()['name']);
                    }
                    break;
                }
            }
        }
    }
}
