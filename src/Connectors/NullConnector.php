<?php

namespace Sirius\Queue\Connectors;

use Sirius\Queue\Contracts\ConnectorInterface;
use Sirius\Queue\Queues\NullQueue;

class NullConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Sirius\Queue\Contracts\Queue
     */
    public function connect(array $config)
    {
        return new NullQueue;
    }
}
