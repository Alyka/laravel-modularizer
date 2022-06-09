<?php

namespace Modularizer\Contracts;

use Modularizer\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface Service
{
    /**
     * Display a listing the resources
     *
     * @param array $requestAttributes
     * @return LengthAwarePaginator
     */
    public function index($requestAttributes);

    /**
     * Get a list of all records.
     *
     * @param array $requestAttributes Request requestAttributes.
     * @param array|string $fields Fields to select.
     * @return Collection
     */
    public function all($requestAttributes, $fields = '*'): Collection;

    /**
     * Create a new resource in storage.
     *
     * @param array $attributes
     * @return Model
     */
    public function store($attributes);

    /**
     * Merge the relationship attributes of the associate
     * Model into the given $attributes, then create a new
     * resource, in storage, with the merged attributes.
     *
     * @param array $attributes
     * @param Model $associate
     * @return Model
     */
    public function associateAndStore($attributes, $associate);

    /**
     * Create new, update existing and delete missing records
     * in the specified $attributes.
     *
     * @param array $attributes
     * @param array $constraints
     * @return void
     */
    public function sync($attributes, $constraints);

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
    public function associateAndUpdate($id, $attributes, $associate);

    /**
     * Update the specified resource.
     *
     * @param int $id
     * @param array $attributes
     * @return Model
     */
    public function update($id, $attributes);

    /**
     * Show details of the specified resource.
     *
     * @param int $id
     * @param array $requestAttributes
     * @return mixed
     */
    public function show($id, $requestAttributes);

    /**
     * Delete the specified resource from storage.
     *
     * @param int $id
     * @param array $requestAttributes
     * @return void
     */
    public function destroy($id, $requestAttributes = []);

    /**
     * Change the ownership of the given model.
     *
     * @param Model $model
     * @param Model $newOwner
     * @return mixed
     */
    public function changeOwner($model, $newOwner);
}
