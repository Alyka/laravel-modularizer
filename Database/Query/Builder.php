<?php

namespace Core\Database\Query;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;

class Builder extends QueryBuilder
{
    /**
     * @inheritDoc
     */
    protected function runPaginationCountQuery($columns = ['*'])
    {
        if ($this->groups || $this->havings) {
            $clone = $this->cloneForPaginationCount();

            if (is_null($clone->columns) && ! empty($this->joins)) {
                $clone->select($this->from.'.*');
            }

            return $this->newQuery()
                ->from(new Expression('('.$clone->toSql().') as '.$this->grammar->wrap('aggregate_table')))
                ->mergeBindings($clone)
                ->setAggregate('count', $this->withoutSelectAliases($columns))
                ->get()->all();
        }

        // We are going to include our columns in the cloned query builder instance
        // so we are going to remove 'columns' from the $without array so that laravel
        // will not remove our columns during the pagination-count query.
        // Before, we had:
        // $without = $this->unions ? ['orders', 'limit', 'offset'] : ['columns', 'orders', 'limit', 'offset'];
        // But now we have:
        // $without = $this->unions ? ['orders', 'limit', 'offset'] : ['orders', 'limit', 'offset'];
        $without = $this->unions ? ['orders', 'limit', 'offset'] : ['orders', 'limit', 'offset'];

        return $this->cloneWithout($without)
                    ->cloneWithoutBindings($this->unions ? ['order'] : ['select', 'order'])
                    ->setAggregate('count', $this->withoutSelectAliases($columns))
                    ->get()->all();
    }
}
