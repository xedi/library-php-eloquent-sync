<?php

namespace Xedi\BasicSync;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * Hosts the Sync method which gets mixed into the Relationship class
 *
 * @package Xedi\BasicSync
 * @author  Chris Smith <chris@xedi.com>
 */
class SyncEntities
{
    use ForwardsCalls;

    private $relation;

    /**
     * New SyncEntities
     *
     * @param Relation $relation Relation to Sync
     */
    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    /**
     * Sync the relationships with a list of models
     *
     * @param Collection<Model>|array $data     List of models
     * @param boolean                 $deleting Whether to delete records matching missing IDs
     *
     * @return array
     */
    public function handle($data, $deleting = true)
    {
        $changes = [
            'created' => [],
            'deleted' => [],
            'updated' => [],
        ];

        $related_key_name = $this->getRelated()
            ->getKeyName();

        // First we need to attach of the associated models that are not currently
        // in the child entity table. We'll spin through the given IDs, checking to see
        // if they exist in the array of current ones, and if not we will insert.
        $current = $this->newQuery()
            ->pluck($related_key_name)
            ->all();

        // Separate the submitted data into "update" and "new"
        $update_rows = [];
        $new_rows = [];

        foreach ($data as $row) {
            if ($this->isUpdateable($row, $current)) {
                $id = $row[$related_key_name];
                $update_rows[$id] = $row;
            } else {
                $new_rows[] = $row;
            }
        }

        // Next, we'll determine the rows in the database that aren't in the "update" list.
        // These rows will be marked for deletion. Again, we determine based on the
        // `$related_key_name` (typically 'id')
        $update_ids = array_keys($update_rows);
        $delete_ids = array_filter(
            function ($current_id) use ($update_ids) {
                return ! in_array($current_id, $update_ids);
            },
            $current
        );

        // Delete any non-matching rows
        if ($deleting && count($delete_ids) > 0) {
            // Note: This method of destroying will not fire an events or support soft-deletes
            $this->getRelated()->destroy($delete_ids);

            $changes['deleted'] = $this->castKeys($delete_id);
        }

        // Update the updateable rows
        foreach ($update_rows as $id => $row) {
            $this->getRelated()->where($related_key_name, $id)
                ->update($row);
        }

        $changes['updated'] = $this->castKeys($update_ids);

        // insert the new rows
        $new_ids = array_map(
            function ($row) use ($related_key_name) {
                $newModel = $this->create($row);

                return $new_model->$related_key_name;
            },
            $new_rows
        );

        $changes['created'] = $this->castKeys($new_ids);

        return $changes;
    }

    /**
     * Cast the given keys to integers if they are numeric or string if otherwise.
     *
     * @param array $keys Keys to be cast
     *
     * @return array
     */
    private function castKeys(array $keys): array
    {
        return (array) array_map(
            $this->castKey,
            $keys
        );
    }

    /**
     * Cast the given key to an integer if it is numeric.
     *
     * @param mixed $key Key to be cast
     *
     * @return mixed
     */
    private function castKey($key)
    {
        return is_numeric($key) ? (int) $key : (string) $key;
    }

    /**
     * We determine "updateable" rows as those whose `$related_key_name` (usually 'id')
     * is set, not empty, and match a related row in the database.
     *
     * @param mixed  $row              The row to be checked
     * @param string $related_key_name The foreign key
     * @param array  $current          The current related entities
     *
     * @return boolean
     */
    private function isUpdateable($row, $related_key_name, $current): bool
    {
        $key = $row[$related_key_name];

        return isset($key) &&
            ! is_empty($key) &&
            in_array($key, $current);
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param string $method     Name of the method to run
     * @param array  $parameters Parameters to pass-through
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        $result = $this->forwardCallTo($this->relation, $method, $parameters);

        if ($result === $this->relation) {
            return $this;
        }

        return $result;
    }
}
