<?php

namespace Sirius\Queue\Connectors;

use Sirius\Queue\Queues\SyncQueue;
use Sirius\Queue\Contracts\ConnectorInterface;

class SyncConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Sirius\Queue\Contracts\Queue
     */
    public function connect(array $config)
    {
        return new SyncQueue;
    }
}
