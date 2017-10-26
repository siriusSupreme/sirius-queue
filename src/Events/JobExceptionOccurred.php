<?php

namespace Sirius\Queue\Events;

class JobExceptionOccurred
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
     * The exception instance.
     *
     * @var \Exception
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  \Sirius\Queue\Jobs\Job  $job
     * @param  \Exception  $exception
     *
     */
    public function __construct($connectionName, $job, $exception)
    {
        $this->job = $job;
        $this->exception = $exception;
        $this->connectionName = $connectionName;
    }
}
