<?php

namespace Sirius\Queue\Contracts;

interface ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     *
     * @return \Sirius\Queue\Contracts\Queue
     */
    public function connect(array $config);
}
