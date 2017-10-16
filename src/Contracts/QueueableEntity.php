<?php

namespace Sirius\Queue\Contracts;

interface QueueableEntity
{
    /**
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId();

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection();
}
