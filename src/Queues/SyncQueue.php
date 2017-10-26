<?php

namespace Sirius\Queue\Queues;

use Exception;
use Sirius\Queue\Jobs\FailingJob;
use Sirius\Queue\Jobs\SyncJob;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class SyncQueue extends Queue
{
    /**
     * Get the size of the queue.
     *
     * @param  string  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return 0;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return mixed
     *
     * @throws \Exception|\Throwable
     */
    public function push($job, $data = '', $queue = null)
    {
        $queueJob = $this->resolveJob($this->createPayload($job, $data), $queue);

        try {
            $queueJob->fire();
        } catch (Exception $e) {
            $this->handleException($queueJob, $e);
        } catch (Throwable $e) {
            $this->handleException($queueJob, new FatalThrowableError($e));
        }

        return 0;
    }

    /**
     * Resolve a Sync job instance.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @return \Sirius\Queue\Jobs\SyncJob
     */
    protected function resolveJob($payload, $queue)
    {
        return new SyncJob($this->container, $payload, $this->connectionName, $queue);
    }

    /**
     * Handle an exception that occurred while processing a job.
     *
     * @param  \Sirius\Queue\Jobs\Job  $queueJob
     * @param  \Exception  $e
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function handleException($queueJob, $e)
    {
        FailingJob::handle($this->connectionName, $queueJob, $e);

        throw $e;
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @param  array   $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        //
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string  $queue
     * @return \Sirius\Queue\Contracts\Job|null
     */
    public function pop($queue = null)
    {
        //
    }
}
