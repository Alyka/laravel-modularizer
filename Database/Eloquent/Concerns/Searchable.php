<?php

namespace Alyka\Modularizer\Database\Eloquent\Concerns;

trait Searchable
{
    /**
     * Call this method before executing the search query.
     *
     * @param \Illminate\Database\Eloquent\Builder $query
     * @return \Illminate\Database\Eloquent\Builder
     */
    protected function beforeSearch($query)
    {
        return $query;
    }

    /**
     * Add search scope to the query.
     *
     * @param \Illminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search = null)
    {
        $params = $this->searchable;

        if (! empty($params) && ! is_null($search)) {
            $this->beforeSearch($query);

            $query->where(function ($query) use ($params, $search) {
                foreach ($params as $param) {
                    $query->orWhere($param, 'LIKE', '%'.$search.'%');
                }
            });
        }

        return $query;
    }
}
