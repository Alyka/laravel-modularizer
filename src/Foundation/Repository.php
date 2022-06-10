<?php

namespace Modularizer\Foundation;

use Module\User\Models\User;
use Modularizer\Database\Eloquent\Model;
use Modularizer\Contracts\Repository as RepositoryContract;
use Modularizer\Concerns\Aggregatable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\Factory;

abstract class Repository
{
    use Aggregatable;

    /**
     * The query builder instance.
     *
     * @var Builder
     */
    public $builder;

    /**
     * The default model for the repository.
     *
     * @var Model
     */
    public $model;

    /**
     * The filters to apply to the target query.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Create new instance of the repository.
     *
     * @param Model|User $model
     * @return void
     */
    public function __construct($model)
    {
        $this->setModel($model);

        $this->setBuilder($this->newBuilder());
    }

    /**
     * Get the factory of the underlying model.
     *
     * @return Factory
     */
    public function factory() : Factory
    {
        return $this->newModel()->factory();
    }

    /**
     * Apply a given eloquent local scope on the query.
     *
     * @param string $scope
     * @return $this
     */
    public function applyScope($scope)
    {
        $this->getBuilder()->{$scope}();

        return $this;
    }

    /**
     * Filter the target query by the specified attributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function filter(array $attributes = [])
    {
        $this->filters = array_merge($this->filters, $attributes);

        return $this;
    }

    /**
     * Apply the search scope on the query.
     *
     * @param string|null $search
     * @return $this
     */
    public function search($search = null)
    {
        $this->getBuilder()->search($search);

        return $this;
    }

    /**
     * Force assign the given attributes to the given model.
     *
     * @param Model $model
     * @param array $attributes
     * @return $this
     */
    public function forceFill($model, $attributes)
    {
        $model->forceFill($attributes);

        return $this;
    }

    /**
     * Make a model using the given array of data as its attributes.
     *
     * @param array $data
     * @return Model
     */
    public function wrapWithModel(array $data)
    {
        $model = new Model;

        return $model->forceFill($data);
    }

    /**
     * Create a new resource in storage.
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes)
    {
        $model = $this->newModel()->fill($attributes);

        $this->save($model);

        return $this->toObject($model->refresh());
    }

    /**
     * Persist the current repository's model in storage.
     *
     * @param Model|null $model
     * @return bool
     */
    public function save($model = null)
    {
        return ($model ?? $this->getModel())->save();
    }

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
    public function update(array $attributes, $id)
    {
        $model = $this->getModelById($id);

        $model->fill($attributes);

        $this->setModel($model);

        $this->save();

        return $this->toObject($model->refresh());
    }

    /**
     * Update the specified resource of create
     * new one if it does not exist.
     *
     * @param array $filters
     * @param array $attributes
     * @return Model|bool|null
     */
    public function updateOrCreate(array $filters, array $attributes)
    {
        return $this->toObject(
            $this->getBuilder()->updateOrCreate($filters, $attributes)
        );
    }

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
    public function delete($id = null, $attributes = [])
    {
        $model = $this->getModelById($id);

        $model->delete();
    }

    /**
     * Retrieve the specified resource from storage.
     *
     * @param int $id Resource identifier.
     * @param bool $applyConstraints If the query builder constraints should be applied.
     * @return Model|null.
     */
    public function find($id, $applyConstraints = false)
    {
        $builder = $applyConstraints ? $this->getBuilder() : $this->newBuilder();

        return $this->toObject($builder->find($id));
    }

    /**
     * Retrieve the specified resource or throw an exception if it's not found.
     *
     * @param int $id
     * @param bool $applyConstraints If the query builder constraints should be applied.
     * @return Model.
     * @throws Exception
     */
    public function findOrFail($id, $applyConstraints = false)
    {
        $builder = $applyConstraints ? $this->getBuilder() : $this->newBuilder();

        return $this->toObject($builder->findOrFail($id));
    }

    /**
     * Retrieve the first record matching the given criteria.
     *
     * @param mixed $args Array with array key as query field
     * and array value as search value. Or two arguments, first
     * is query field and second is search value value.
     * @return Model
     */
    public function firstWhere(...$args)
    {
        $builder = $this->newBuilder();

        if (is_array($args[0])) {
            foreach ($args[0] as $key => $arg) {
                $builder->where($key, $arg);
            }

            return $builder->first();
        }

        return $this->toObject(
            $builder->where($args[0], $args[1])->first()
        );
    }

    /**
     * Show a paginated listing of all specified resources.
     *
     * @param int|null $limit
     * @return LengthAwarePaginator.
     */
    public function paginate($limit = null) : LengthAwarePaginator
    {
        return $this->getBuilder()->paginate($limit);
    }

    /**
     * Sort by latest to oldest.
     *
     * @return $this
     */
    public function latest()
    {
        $this->setBuilder($this->getBuilder()->orderBy(
            $this->getModel()->getTable().'.created_at',
            'desc'
        ));

        return $this;
    }

    /**
     * Show a list of all requested records.
     *
     * @param array $fields.
     * @return Collection
     */
    public function all($fields = '*') : Collection
    {
        return $this->getBuilder()
                    ->select($fields)
                    ->get();
    }

    /**
     * Count all matched records.
     *
     * @return int
     */
    public function count() : int
    {
        return $this->getBuilder()
                    ->count();
    }

    /**
     * Get the first database record matching the query.
     *
     * @return Model|User
     */
    public function first()
    {
        return $this->toObject($this->getBuilder()->first());
    }

    /**
     * Set the default model for the repository.
     *
     * @param Model|User $model
     * @return $this
     */
    public function setModel($model) : RepositoryContract
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Convert the given model to an array.
     *
     * @param Model|User $model
     * @return array
     */
    public function toArray($model): array
    {
        return $model->toArray();
    }

    /**
     * Transform the model to an object.
     *
     * @param mixed $model
     * @return object|null
     */
    public function toObject($model)
    {
        return $model;
    }

    /**
     * Associate two given models with each other.
     *
     * @param Model|null $child
     * @param Model $parent
     * @return Repository
     */
    public function associate($child, $parent)
    {
        $child->resolveRelationUsing('_parent', function ($child) use ($parent) {
            return $child->belongsTo(get_class($parent));
        });

        $child->_parent()->associate($parent);

        return $this;
    }

    /**
     * Get all records that are not in the specified ids.
     *
     * @param array $ids
     * @param array $constraints
     * @return Collection
     */
    public function getInverse($ids, $constraints)
    {
        $builder = $this->getBuilder();

        foreach ($constraints as $field => $value) {
            $builder->where($field, $value);
        }

        return $builder->whereNotIn('id', $ids)->get();
    }

    /**
     * Delete all records of this repositories model
     * whose ids are not contained in the given ids
     *
     * @param array $ids
     * @param array $constraints
     * @return void
     */
    public function deleteInverse($ids, $constraints)
    {
        $builder = $this->getBuilder();

        foreach ($constraints as $field => $value) {
            $builder->where($field, $value);
        }

        $builder->whereNotIn('id', $ids)->delete();
    }

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
    ) {
        $ids = collect($attributes)->pluck($idKey)->toArray();

        $this->deleteInverse($ids, $additionalAttributes);

        foreach ($attributes as $attribute) {
            $attribute = array_merge($attribute, $additionalAttributes);
            $this->newModel()->updateOrCreate(
                [$idField => $attribute[$idKey]],
                $attribute
            );
        }
    }

    /**
     * Determine if a model is the owner of another.
     *
     * @param Model $parent
     * @param Model $child
     * @return bool
     */
    public function owns($parent, $child) : bool
    {
        $foreignKey = $parent->getForeignKey();

        $localKey = $parent->getKeyName();

        return $parent->$localKey === $child->$foreignKey;
    }

    /**
     * Merge the attributes of the $associate Model into
     * the given $attributes.
     *
     * @param array $attributes
     * @param Model $associate
     * @return array
     */
    public function merge($attributes, $associate)
    {
        $child = $this->newModel();

        $this->associate($child, $associate);

        $child->fill($attributes);

        return $this->toArray($child);
    }

    /**
     * Load the specified model relations.
     *
     * @param array|...args $relations
     * @return Builder
     */
    public function with($relations)
    {
        return $this->getBuilder()->with($relations);
    }

    /**
     * Get the morph alias of the given model.
     *
     * @param Model|User $model
     * @return string
     */
    public function getMorphClass($model)
    {
        return $model->getMorphClass();
    }

    /**
     * Get new query builder instance.
     *
     * @return Builder
     */
    protected function newBuilder()
    {
        return $this->newModel()->newQuery();
    }

    /**
     * Get the model attribute of the repository.
     *
     * @return Model|User
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Get the model specified by $id.
     *
     * If $id is null, get the repository's default model.
     *
     * @param int $id
     * @return Model|User
     */
    protected function getModelById($id)
    {
        return $this->newBuilder()->find($id);
    }

    /**
     * Get new instance of the repository's model.
     *
     * @return Model|User
     */
    protected function newModel()
    {
        $class = get_class($this->getModel());

        return new $class;
    }

    /**
     * Set the query builder instance.
     *
     * @param Builder $builder
     * @return void
     */
    protected function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get the query builder instance.
     *
     * @return Builder
     */
    protected function getBuilder() : Builder
    {
        return $this->builder->filter($this->filters);
    }
}
