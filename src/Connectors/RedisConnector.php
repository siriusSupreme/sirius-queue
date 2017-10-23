<?php

namespace Sirius\Queue\Connectors;

use Sirius\Queue\Queues\RedisQueue;
use Sirius\Redis\Contracts\Factory as Redis;
use Sirius\Queue\Contracts\ConnectorInterface;

class RedisConnector implements ConnectorInterface
{
    /**
     * The Redis database instance.
     *
     * @var \Sirius\Redis\Contracts\Factory
     */
    protected $redis;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new Redis queue connector instance.
     *
     * @param  \Sirius\Redis\Contracts\Factory  $redis
     * @param  string|null  $connection
     *
     */
    public function __construct(Redis $redis, $connection = null)
    {
        $this->redis = $redis;
        $this->connection = $connection;
    }

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Sirius\Queue\Contracts\Queue
     */
    public function connect(array $config)
    {
        return new RedisQueue(
            $this->redis, $config['queue'],
            $config['connection'] ?? $this->connection,
            $config['retry_after'] ?? 60
        );
    }
}
