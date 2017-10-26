<?php

namespace Sirius\Queue\Events;

class JobProcessed
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job instance.
     *
     * @var \Sirius\Queue\Jobs\Job
     */
    public $job;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  \Sirius\Queue\Jobs\Job  $job
     *
     */
    public function __construct($connectionName, $job)
    {
        $this->job = $job;
        $this->connectionName = $connectionName;
    }
}
