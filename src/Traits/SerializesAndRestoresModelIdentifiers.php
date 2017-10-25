<?php

namespace Sirius\Queue\Traits;

use Sirius\Queue\Contracts\QueueableEntity;
use Sirius\Queue\Contracts\QueueableCollection;

trait SerializesAndRestoresModelIdentifiers
{
    /**
     * Get the property value prepared for serialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getSerializedPropertyValue($value)
    {
        if ($value instanceof QueueableCollection) {
            return new ModelIdentifier(
                $value->getQueueableClass(),
                $value->getQueueableIds(),
                $value->getQueueableConnection()
            );
        }

        if ($value instanceof QueueableEntity) {
            return new ModelIdentifier(
                get_class($value),
                $value->getQueueableId(),
                $value->getQueueableConnection()
            );
        }

        return $value;
    }

    /**
     * Get the restored property value after deserialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getRestoredPropertyValue($value)
    {
        if (! $value instanceof ModelIdentifier) {
            return $value;
        }

        return is_array($value->id)
                ? $this->restoreCollection($value)
                : $this->getQueryForModelRestoration((new $value->class)->setConnection($value->connection))
                    ->useWritePdo()->findOrFail($value->id);
    }

    /**
     * Restore a queueable collection instance.
     *
     * @param
     * @return
     */
    protected function restoreCollection($value)
    {
        if (! $value->class || count($value->id) === 0) {
            return new EloquentCollection;
        }

        $model = (new $value->class)->setConnection($value->connection);

        return $this->getQueryForModelRestoration($model)->useWritePdo()
                    ->whereIn($model->getQualifiedKeyName(), $value->id)->get();
    }

    /**
     * Get the query for restoration.
     *
     * @param    $model
     * @return
     */
    protected function getQueryForModelRestoration($model)
    {
        return $model->newQueryWithoutScopes();
    }
}
