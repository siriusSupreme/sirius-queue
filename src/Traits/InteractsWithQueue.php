<?php

namespace Sirius\Queue\Traits;

use Sirius\Queue\Jobs\Job;
use Sirius\Queue\Jobs\FailingJob;

trait InteractsWithQueue
{
    /**
     * The underlying queue job instance.
     *
     * @var \Sirius\Queue\Jobs\Job
     */
    protected $job;

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return $this->job ? $this->job->attempts() : 1;
    }

    /**
     * Delete the job from the queue.
     *
     */
    public function delete()
    {
        if ($this->job) {
          $this->job->delete();
        }
    }

    /**
     * Fail the job from the queue.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function fail($exception = null)
    {
        if ($this->job) {
            FailingJob::handle($this->job->getConnectionName(), $this->job, $exception);
        }
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int   $delay
     * @return void
     */
    public function release($delay = 0)
    {
        if ($this->job) {
            $this->job->release($delay);
        }
    }

    /**
     * Set the base queue job instance.
     *
     * @param  \Sirius\Queue\Jobs\Job  $job
     * @return $this
     */
    public function setJob(Job $job)
    {
        $this->job = $job;

        return $this;
    }
}
