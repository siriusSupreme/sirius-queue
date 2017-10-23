<?php

namespace Sirius\Queue;

use Sirius\Container\Container;
use Sirius\Queue\Events\JobFailed;
use Sirius\Contracts\Events\Dispatcher;

class FailingJob
{
    /**
     * Delete the job, call the "failed" method, and raise the failed job event.
     *
     * @param  string  $connectionName
     * @param  \Sirius\Queue\Jobs\Job  $job
     * @param  \Exception $e
     *
     * @return void
     */
    public static function handle($connectionName, $job, $e = null)
    {
        $job->markAsFailed();

        if ($job->isDeleted()) {
            return;
        }

        try {
            // If the job has failed, we will delete it, call the "failed" method and then call
            // an event indicating the job has failed so it can be logged if needed. This is
            // to allow every developer to better keep monitor of their failed queue jobs.
            $job->delete();

            $job->failed($e);
        } finally {
            static::events()->dispatch(new JobFailed(
                $connectionName, $job, $e ?: new ManuallyFailedException
            ));
        }
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Sirius\Event\Contracts\Dispatcher
     */
    protected static function events()
    {
        return Container::getInstance()->make(Dispatcher::class);
    }
}
