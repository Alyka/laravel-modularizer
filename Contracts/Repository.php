<?php

namespace Core\Contracts;

use Services\User\Models\User;
use Core\Database\Eloquent\Model;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\Factory;

interface Repository
{
    /**
     * Get the factory of the underlying model.
     *
     * @return Factory
     */
    public function factory() : Factory;

    /**
     * Apply a given eloquent local scope on the query.
     *
     * @param string $scope
     * @return $this
     */
    public function applyScope($scope);

    /**
     * Filter the target query by the specified attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function filter(array $attributes = []);

    /**
     * Apply the search scope on the query.
     *
     * @param string|null $search
     * @return $this
     */
    public function search($search = null);

    /**
     * Force assign the given attributes to the given model.
     *
     * @param Model $model
     * @param array $attributes
     * @return $this
     */
    public function forceFill($model, $attributes);

    /**
     * Create a new resource in storage.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes);

    /**
     * Persist the current repository's model in storage.
     *
     * @param Model|null $model
     * @return bool
     */
    public function save($model = null);

    /**
     * Update the specified resource in storage.
     *
     * If $id is null, the $model attribute of the repository
     * will be updated.
     *
     * @param array $attributes
     * @param int $id
     * @return Model|User
     */
    public function update(array $attributes, $id);

    /**
     * Update the specified resource of create
     * new one if it does not exist.
     *
     * @param array $filters
     * @param array $attributes
     * @return Model|bool|null
     */
    public function updateOrCreate(array $filters, array $attributes);

    /**
     * Delete the specified resource from storage.
     *
     * If $id is null, the $model attribute of the repository
     * will be deleted.
     *
     * @param int|null $id
     * @param array $attributes
     * @return void
     */
    public function delete($id = null, $attributes = []);

    /**
     * Retrieve the specified resource from storage.
     *
     * @param int $id Resource identifier.
     * @param bool $applyConstraints If the query builder constraints should be applied.
     * @return Model|null.
     */
    public function find($id, $applyConstraints = false);

    /**
     * Retrieve the specified resource or throw an exception if it's not found.
     *
     * @param int $id
     * @param bool $applyConstraints If the query builder constraints should be applied.
     * @return Model.
     * @throws Exception
     */
    public function findOrFail($id, $applyConstraints = false);

    /**
     * Retrieve the first record matching the given criteria.
     *
     * @param mixed $args Array with array key as query field
     * and array value as search value. Or two arguments, first
     * is query field and second is search value value.
     * @return Model
     */
    public function firstWhere(...$args);

    /**
     * Show a paginated listing of all specified resources.
     *
     * @param int|null $limit
     * @return LengthAwarePaginator.
     */
    public function paginate($limit = null) : LengthAwarePaginator;

    /**
     * Sort by latest to oldest.
     *
     * @return $this
     */
    public function latest();

    /**
     * Show a list of all requested records.
     *
     * @param array $fields.
     * @return Collection
     */
    public function all($fields = '*') : Collection;

    /**
     * Count all matched records.
     *
     * @return int
     */
    public function count() : int;

    /**
     * Get the first database record matching the query.
     *
     * @return Model|User
     */
    public function first();

    /**
     * Set the default model for the repository.
     *
     * @param Model|User $model
     * @return $this
     */
    public function setModel($model) : Repository;

    /**
     * Convert the given model to an array.
     *
     * @param Model|User $model
     * @return array
     */
    public function toArray($model): array;

    /**
     * Transform the model to an object.
     *
     * @param mixed $model
     * @return object|null
     */
    public function toObject($model);

    /**
     * Associate two given models with each other.
     *
     * @param Model|null $child
     * @param Model $parent
     * @return Repository
     */
    public function associate($child, $parent);

    /**
     * Get all records that are not in the specified ids.
     *
     * @param array $ids
     * @param array $constraints
     * @return Collection
     */
    public function getInverse($ids, $constraints);

    /**
     * Delete all records of this repositories model
     * whose ids are not contained in the given ids
     *
     * @param array $ids
     * @param array $constraints
     * @return void
     */
    public function deleteInverse($ids, $constraints);

    /**
     * Update matched items, create new items and delete
     * missing items from this repository's models.
     *
     * @param array $attributes
     * @param array $additionalAttributes
     * @param string $idKey
     * @return void
     */
    public function syncUpdate(
        array $attributes,
        array $additionalAttributes = [],
        $idKey = 'id',
        $idField = 'id'
    );

    /**
     * Determine if a model is the owner of another.
     *
     * @param Model $parent
     * @param Model $child
     * @return bool
     */
    public function owns($parent, $child) : bool;

    /**
     * Merge the relationship attributes of the associate
     * Model into the given $attributes.
     *
     * @param array $attributes
     * @param Model $associate
     * @return array
     */
    public function merge($attributes, $associate);

    /**
     * Load the specified model relations.
     *
     * @param array|...args $relations
     * @return Builder
     */
    public function with($relations);

    /**
     * Get the morph alias of the given model.
     *
     * @param Model|User $model
     * @return string
     */
    public function getMorphClass($model);
}
