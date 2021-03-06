<?php

namespace Sirius\Queue\Connectors;

use Pheanstalk\Connection;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Sirius\Queue\Contracts\ConnectorInterface;
use Sirius\Queue\Queues\BeanstalkdQueue;

class BeanstalkdConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Sirius\Queue\Contracts\Queue
     */
    public function connect(array $config)
    {
        $retryAfter = $config['retry_after'] ?? Pheanstalk::DEFAULT_TTR;

        return new BeanstalkdQueue($this->pheanstalk($config), $config['queue'], $retryAfter);
    }

    /**
     * Create a Pheanstalk instance.
     *
     * @param  array  $config
     * @return \Pheanstalk\Pheanstalk
     */
    protected function pheanstalk(array $config)
    {
        return new Pheanstalk(
            $config['host'],
            $config['port'] ?? PheanstalkInterface::DEFAULT_PORT,
            $config['timeout'] ?? Connection::DEFAULT_CONNECT_TIMEOUT,
            $config['persistent'] ?? false
        );
    }
}
