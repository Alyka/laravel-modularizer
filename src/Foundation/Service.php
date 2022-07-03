<?php

namespace Modularizer\Foundation;

use Illuminate\Support\Collection;

abstract class Service
{
    /**
     * The Repository instance.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Create a new instance of the service.
     *
     * @param Repository $repository
     * @return void
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing the resources
     *
     * @param array $requestAttributes
     * @return LengthAwarePaginator
     */
    public function index($requestAttributes)
    {
        return $this->repository
                    ->applyScope('index')
                    ->search(@$requestAttributes['search'])
                    ->filter($requestAttributes)
                    ->latest()
                    ->paginate(@$requestAttributes['limit']);
    }

    /**
     * Show details of the specified resource.
     *
     * @param int $id
     * @param array $requestAttributes
     * @return mixed
     */
    public function show($id, $requestAttributes)
    {
        return $this->repository
                    ->applyScope('show')
                    ->find($id, true);
    }

    /**
     * Get a list of all records.
     *
     * @param array $requestAttributes
     * @param array|string $fields
     * @return Collection
     */
    public function all($requestAttributes, $fields = '*'): Collection
    {
        return $this->repository
                    ->search(@$requestAttributes['search'])
                    ->filter($requestAttributes)
                    ->all($fields);
    }

    /**
     * Count all matched records.
     *
     * @param array $requestAttributes
     * @return int
     */
    public function count($requestAttributes): int
    {
        return $this->repository
                    ->filter($requestAttributes)
                    ->count();
    }

    /**
     * Create a new resource in storage.
     *
     * @param array $attributes
     * @return Model
     */
    public function store($attributes)
    {
        return $this->repository
                    ->create($attributes);
    }

    /**
     * Create a new resource in storage and associate it
     * with the given $associate model.
     *
     * @param array $attributes
     * @param Model $associate
     * @return Model
     */
    public function associateAndStore($attributes, $associate)
    {
        $attributes = $this->repository->merge($attributes, $associate);

        return $this->store($attributes);
    }

    /**
     * Create new, update existing and delete missing records
     * in the specified $attributes.
     *
     * @param array $attributes
     * @param array $constraints
     * @param string $key
     * @return void
     */
    public function sync($attributes, $constraints, $key = 'id')
    {
        $ids = collect($attributes)->pluck($key)->toArray();

        $recordsToBeDeleted = $this->repository
                                   ->getInverse($ids, $constraints);

        // Delete missing records.
        foreach ($recordsToBeDeleted as $model) {
            $this->destroy($model->id);
        }

        // Upate and create existing and new records.
        foreach ($attributes as $attribute) {
            if ($this->repository->find(@$attribute[$key])) {
                $this->update($attribute[$key], $attribute);
            } else {
                $this->store(array_merge($attribute, $constraints));
            }
        }
    }

    /**
     * Merge the relationship attributes of the associate
     * Model into the given $attributes, then update the
     * specified resource, in storage, with the merged attributes.
     *
     * @param int $id
     * @param array $attributes
     * @param Model $associate
     * @return Model
     */
    public function associateAndUpdate($id, $attributes, $associate)
    {
        $attributes = $this->repository->merge($attributes, $associate);

        return $this->update($id, $attributes);
    }

    /**
     * Update the specified resource.
     *
     * @param int $id
     * @param array $attributes
     * @return Model
     */
    public function update($id, $attributes)
    {
        return $this->repository
                    ->update($attributes, $id);
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param int $id
     * @param array $requestAttributes
     * @return void
     */
    public function destroy($id, $requestAttributes = [])
    {
        return $this->repository
                    ->delete($id, $requestAttributes);
    }

    /**
     * Change the ownership of the given model.
     *
     * @param Model $model
     * @param Model $newOwner
     * @param string|null $relation
     * @return mixed
     */
    public function changeOwner($model, $newOwner, $relation = null)
    {
        return $this->repository
                    ->associate($model, $newOwner, $relation)
                    ->save($model);
    }
}
