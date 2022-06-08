<?php

namespace Alyka\Modularizer\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Model
{
    use Filterable,
    Searchable,
    Relation,
    JoinRelation,
    CustomizesQueryBuilder;

    /**
     * Add queries for displaying multiple records.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIndex(Builder $query)
    {
        return $query->select('*');
    }

    /**
     * Add queries for displaying details of a single record.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeShow(Builder $query)
    {
        return $query->select('*');
    }
}
