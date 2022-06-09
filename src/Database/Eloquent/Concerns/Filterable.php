<?php

namespace Modularizer\Database\Eloquent\Concerns;

use Illuminate\Support\Str;

trait Filterable
{
    /**
     * filter the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $attributes)
    {
        if (! empty($attributes)) {
            foreach ($attributes as $param => $value) {
                $paramKeyStud = Str::studly($param);
                $method = "filterBy{$paramKeyStud}";

                // ensure that only fields found in the model's
                // 'filterable' property, or whose filterBy methods
                // are defined, are effective in the filtering operation.
                if (method_exists($this, $method)) {
                    $this->$method($query, $value);
                } elseif (in_array($param, ($this->filterable ?? []))) {
                    $query->where($param, $value);
                }
            }
        }

        return $query;
    }
}
