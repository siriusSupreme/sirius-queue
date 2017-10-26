<?php

namespace Sirius\Queue;

use Sirius\Container\Container;
use Sirius\Queue\Connectors\SqsConnector;
use Sirius\Queue\Connectors\NullConnector;
use Sirius\Queue\Connectors\SyncConnector;
use Sirius\Queue\Connectors\RedisConnector;
use Sirius\Queue\Failed\NullFailedJobProvider;
use Sirius\Queue\Connectors\BeanstalkdConnector;
use function Sirius\Support\tap;

class QueueServiceProvider
{
  /**
   * @var null|Container
   */
  private $container=null;

  public function __construct(Container $container=null) {
    $this->container=$container??new Container;
  }

  /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();

        $this->registerConnection();

        $this->registerWorker();

        $this->registerListener();

        $this->registerFailedJobServices();
    }

    /**
     * Register the queue manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->container->singleton('queue', function ($container) {
            // Once we have an instance of the queue manager, we will register the various
            // resolvers for the queue connectors. These connectors are responsible for
            // creating the classes that accept queue configs and instantiate queues.
            return tap(new QueueManager($container), function ($manager) {
                $this->registerConnectors($manager);
            });
        });
    }

    /**
     * Register the default queue connection binding.
     *
     * @return void
     */
    protected function registerConnection()
    {
        $this->container->singleton('queue.connection', function ($container) {
            return $container['queue']->connection();
        });
    }

    /**
     * Register the connectors on the queue manager.
     *
     * @param  \Sirius\Queue\QueueManager  $manager
     * @return void
     */
    public function registerConnectors($manager)
    {
        foreach (['Null', 'Sync', 'Redis', 'Beanstalkd', 'Sqs'] as $connector) {
            $this->{"register{$connector}Connector"}($manager);
        }
    }

    /**
     * Register the Null queue connector.
     *
     * @param  \Sirius\Queue\QueueManager  $manager
     *
     * @return void
     */
    protected function registerNullConnector($manager)
    {
        $manager->addConnector('null', function () {
            return new NullConnector;
        });
    }

    /**
     * Register the Sync queue connector.
     *
     * @param  \Sirius\Queue\QueueManager  $manager
     *
     * @return void
     */
    protected function registerSyncConnector($manager)
    {
        $manager->addConnector('sync', function () {
            return new SyncConnector;
        });
    }

    /**
     * Register the Redis queue connector.
     *
     * @param  \Sirius\Queue\QueueManager  $manager
     *
     * @return void
     */
    protected function registerRedisConnector($manager)
    {
        $manager->addConnector('redis', function () {
            return new RedisConnector($this->container['redis']);
        });
    }

    /**
     * Register the Beanstalkd queue connector.
     *
     * @param  \Sirius\Queue\QueueManager  $manager
     *
     * @return void
     */
    protected function registerBeanstalkdConnector($manager)
    {
        $manager->addConnector('beanstalkd', function () {
            return new BeanstalkdConnector;
        });
    }

    /**
     * Register the Amazon SQS queue connector.
     *
     * @param  \Sirius\Queue\QueueManager  $manager
     *
     * @return void
     */
    protected function registerSqsConnector($manager)
    {
        $manager->addConnector('sqs', function () {
            return new SqsConnector;
        });
    }

    /**
     * Register the queue worker.
     *
     * @return void
     */
    protected function registerWorker()
    {
        $this->container->singleton('queue.worker', function () {
            return new Worker(
                $this->container['queue'], $this->container['events'], $this->container[ExceptionHandler::class]
            );
        });
    }

    /**
     * Register the queue listener.
     *
     * @return void
     */
    protected function registerListener()
    {
        $this->container->singleton('queue.listener', function () {
            return new Listener($this->container->basePath());
        });
    }

    /**
     * Register the failed job services.
     *
     * @return void
     */
    protected function registerFailedJobServices()
    {
        $this->container->singleton('queue.failer', function () {
            $config = $this->container['config']['queue.failed'];

            return isset($config['table'])
                        ? $this->databaseFailedJobProvider($config)
                        : new NullFailedJobProvider;
        });
    }

    /**
     * Create a new database failed job provider.
     *
     * @param  array  $config
     *
     * @return null
     */
    protected function databaseFailedJobProvider($config)
    {
        return null;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'queue', 'queue.worker', 'queue.listener',
            'queue.failer', 'queue.connection',
        ];
    }
}
