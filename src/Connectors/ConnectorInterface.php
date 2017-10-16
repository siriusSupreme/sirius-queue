<?php

namespace Illuminate\Queue\Connectors;

interface ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Sirius\Queue\Contracts\Queue
     */
    public function connect(array $config);
}
