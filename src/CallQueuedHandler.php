<?php

namespace Sirius\Queue;

use Exception;
use ReflectionClass;
use Sirius\Queue\Contracts\Job;
use Sirius\Queue\Jobs\FailingJob;
use Sirius\Queue\Traits\InteractsWithQueue;
use Sirius\Bus\Contracts\Dispatcher;
use function Sirius\Support\class_uses_recursive;

class CallQueuedHandler
{
    /**
     * The bus dispatcher implementation.
     *
     * @var \Sirius\Bus\Contracts\Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new handler instance.
     *
     * @param  \Sirius\Bus\Contracts\Dispatcher  $dispatcher
     *
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the queued job.
     *
     * @param  \Sirius\Queue\Contracts\Job  $job
     * @param  array  $data
     *
     */
    public function call(Job $job, array $data)
    {
        try {
            $command = $this->setJobInstanceIfNecessary(
                $job, unserialize($data['command'])
            );
        } catch (\RuntimeException $e) {
            return $this->handleModelNotFound($job, $e);
        }

        $this->dispatcher->dispatchNow(
            $command, $this->resolveHandler($job, $command)
        );

        if (! $job->hasFailed() && ! $job->isReleased()) {
            $this->ensureNextJobInChainIsDispatched($command);
        }

        if (! $job->isDeletedOrReleased()) {
            $job->delete();
        }
    }

    /**
     * Resolve the handler for the given command.
     *
     * @param  \Sirius\Queue\Contracts\Job  $job
     * @param  mixed  $command
     * @return mixed
     */
    protected function resolveHandler($job, $command)
    {
        $handler = $this->dispatcher->getCommandHandler($command) ?: null;

        if ($handler) {
            $this->setJobInstanceIfNecessary($job, $handler);
        }

        return $handler;
    }

    /**
     * Set the job instance of the given class if necessary.
     *
     * @param  \Sirius\Queue\Contracts\Job  $job
     * @param  mixed  $instance
     * @return mixed
     */
    protected function setJobInstanceIfNecessary(Job $job, $instance)
    {
        if (in_array(InteractsWithQueue::class, class_uses_recursive(get_class($instance)))) {
            $instance->setJob($job);
        }

        return $instance;
    }

    /**
     * Ensure the next job in the chain is dispatched if applicable.
     *
     * @param  mixed  $command
     * @return void
     */
    protected function ensureNextJobInChainIsDispatched($command)
    {
        if (method_exists($command, 'dispatchNextJobInChain')) {
            $command->dispatchNextJobInChain();
        }
    }

    /**
     * Handle a model not found exception.
     *
     * @param  \Sirius\Queue\Contracts\Job  $job
     * @param  \Exception  $e
     *
     */
    protected function handleModelNotFound(Job $job, $e)
    {
        $class = $job->resolveName();

        try {
            $shouldDelete = (new ReflectionClass($class))
                    ->getDefaultProperties()['deleteWhenMissingModels'] ?? false;
        } catch (Exception $e) {
            $shouldDelete = false;
        }

        if ($shouldDelete) {
            return $job->delete();
        }

        FailingJob::handle(
            $job->getConnectionName(), $job, $e
        );
    }

    /**
     * Call the failed method on the job instance.
     *
     * The exception that caused the failure will be passed.
     *
     * @param  array  $data
     * @param  \Exception  $e
     * @return void
     */
    public function failed(array $data, $e)
    {
        $command = unserialize($data['command']);

        if (method_exists($command, 'failed')) {
            $command->failed($e);
        }
    }
}
