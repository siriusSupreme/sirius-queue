<?php

namespace Sirius\Queue\Contracts;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string  $name
     *
     * @return \Sirius\Queue\Contracts\Queue
     */
    public function connection($name = null);
}
