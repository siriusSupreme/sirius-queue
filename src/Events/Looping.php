<?php

namespace Sirius\Queue\Events;

class Looping
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The queue name.
     *
     * @var string
     */
    public $queue;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     *
     */
    public function __construct($connectionName, $queue)
    {
        $this->queue = $queue;
        $this->connectionName = $connectionName;
    }
}
