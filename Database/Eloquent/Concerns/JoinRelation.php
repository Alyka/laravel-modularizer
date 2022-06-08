<?php

namespace Core\Database\Eloquent\Concerns;

use BadMethodCallException;
use Closure;
use Core\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;

trait JoinRelation
{
    /**
     * Join using eloquent relationships.
     *
     * @param Builder $query
     * @param string $relationNames
     * @param Closure|Closure[]|null $closures
     * @param array|string|null $fields
     * @param Model|null $model
     * @param string $method
     * @return void
     */
    public function scopeJoinRelation(
        Builder $query,
        $relationNames,
        $closures = null,
        $model = null,
        $method = 'leftJoin'
    ) {
        $model = $model ?? $query->getModel();

        $relationNamesArray = explode('.', $relationNames);
        $relationName = array_shift($relationNamesArray);
        $relation = $this->getModelRelation($relationName, $model);

        $firstJoin = null;
        $firstJoinTable = $this->getFirstJoinTable($relation, $query);

        // We will join the first related table.
        $query->{$method}(
            $firstJoinTable,
            function (JoinClause $join) use ($query, $relation, $closures, &$firstJoin, $relationName) {

            // Here we will call the closure only if this join is not happening
                // on a pivot/intermediate table. We are doing this so that any
                // constraints the developer applies on the query will not be
                // applied on an intermediate table.
                if (! $this->relationUsesPivotTable($relation)) {
                    $this->callClosures($closures, $join, $relationName, $query);
                }

                $firstJoin = $this->performFirstJoin($join, $relation);
            }
        );

        // If the relation uses a pivot/intermediate table, we
        // will assume the first table we joined to be the pivot table
        // and will have to join the second related table which is actually
        // the target table of the relationship.
        if ($firstJoin && $this->relationUsesPivotTable($relation)) {
            $secondJoinTable = $this->getSecondJoinTable($relation, $query);

            $query->{$method}(
                $secondJoinTable,
                function (JoinClause $join) use ($query, $relation, $closures, $firstJoin, $relationName) {
                    $this->callClosures($closures, $join, $relationName, $query);

                    $this->performSecondJoin($join, $firstJoin, $relation);
                }
            );
        }

        // Qualify all columns in the select and where clauses prior
        // to query execution.
        $query->getQuery()->beforeQuery(function ($query) use ($relation) {
            $this->qualifySelectColumns($query, $this->getParentTable($relation));

            $this->qualifyWhereColumns(
                $query,
                $this->getParentTable($relation),
                $query->columns
            );
        });

        $relatedModel = $relation->getRelated();

        // Join on nested relationships.
        if ($relatedModel && $relationName) {
            $relationNames = implode('.', $relationNamesArray);

            if (! $relationNames) {
                return $query;
            }

            return $query->joinRelation(
                $relationNames,
                $closures,
                $relatedModel,
                $method
            );
        }

        return $query;
    }

    /**
     * Perform the join using relationships.
     *
     * @param JoinClause $join
     * @param Relation $relation
     * @return JoinClause
     */
    protected function performFirstJoin($join, $relation)
    {
        $parentTable = $this->getParentTable($relation);
        $firstRelatedTable = $this->getJoinTable($join);

        if ($relation instanceof HasOneOrMany || $relation instanceof MorphOneOrMany) {
            $first = $parentTable.'.'.$relation->getLocalKeyName();
            $operator = '=';
            $second = $firstRelatedTable.'.'.$relation->getForeignKeyName();

            if ($relation instanceof MorphOneOrMany) {
                $join->where(
                    $firstRelatedTable.'.'.$relation->getMorphType(),
                    $relation->getMorphClass()
                );
            }
        } elseif ($relation instanceof BelongsTo) {
            $first = $firstRelatedTable.'.'.$relation->getOwnerKeyName();
            $operator = '=';
            $second = $parentTable.'.'.$relation->getForeignKeyName();
        } elseif ($relation instanceof BelongsToMany || $relation instanceof MorphToMany) {
            $first = $parentTable.'.'.$relation->getParentKeyName();
            $operator = '=';
            $second = $firstRelatedTable.'.'.$relation->getForeignPivotKeyName();

            if ($relation instanceof MorphToMany) {
                $join->where(
                    $firstRelatedTable.'.'.$relation->getMorphType(),
                    $relation->getMorphClass()
                );
            }
        } elseif ($relation instanceof HasManyThrough) {
            $first = $parentTable.'.'.$relation->getLocalKeyName();
            $operator = '=';
            $second = $firstRelatedTable.'.'.$relation->getFirstKeyName();
        }

        $this->mergeWhereConstraints(
            $relation->getBaseQuery(),
            $join,
            $firstRelatedTable
        );

        return $join->on($first, $operator, $second);
    }

    /**
     * Perform join on an intermediate/pivot table.
     *
     * @param JoinClause $secondJoin
     * @param JoinClause $firstJoin
     * @param Relation $relation
     * @return void
     */
    protected function performSecondJoin($secondJoin, $firstJoin, $relation)
    {
        $firstTable = $this->getJoinTable($firstJoin);
        $secondTable = $this->getJoinTable($secondJoin);
        $operator = '=';

        if ($relation instanceof BelongsToMany) {
            $first = "{$firstTable}.".$relation->getRelatedPivotKeyName();
            $second = $secondTable.'.'.$relation->getRelatedKeyName();
        } elseif ($relation instanceof HasManyThrough) {
            $first = $firstTable.'.'.$relation->getSecondLocalKeyName();
            $second = $secondTable.'.'.$relation->getForeignKeyName();
        }

        $secondJoin->on($first, $operator, $second);
    }

    /**
     * Call the user defined callbacks.
     *
     * If $closures is an instance of closure, we will call it.
     * But if $closures is an array, we will assume the closure
     * to be the array item whose key is referenced by $relation
     * and we will call that array item as the closure.
     *
     * @param array|Closure $closures
     * @param JoinClause $join
     * @param string $relationName
     * @param Builder $query
     * @return void
     */
    protected function callClosures($closures, $join, $relationName, $query)
    {
        $table = $this->getJoinTable($join);

        if ($closures instanceof Closure) {
            $closures($query, $join, $table);
        } elseif (is_array($closures)) {
            if ($relationClosure = @$closures[$relationName]) {
                if (is_array($relationClosure)) {
                    foreach ($relationClosure as $closure) {
                        $closure($query, $join, $table);
                    }
                } else {
                    $relationClosure($query, $join, $table);
                }
            }
        }
    }

    /**
     * Determine if the given relation uses a pivot table.
     *
     * @param Relation $relation
     * @return bool
     */
    protected function relationUsesPivotTable($relation)
    {
        return $relation instanceof BelongsToMany
        || $relation instanceof HasManyThrough;
    }

    /**
     * Get the parent table of the given relation.
     *
     * @param Relation $relation
     * @return string
     */
    protected function getParentTable($relation)
    {
        return $relation->getParent()->getTable();
    }

    /**
     * Get the first foreign table of the relationship for joins.
     *
     * @param Relation $relation
     * @param Builder $query
     * @return string
     */
    protected function getFirstJoinTable($relation, $query)
    {
        $table = $this->getRelationTable($relation);

        return $this->getUniqueTableAlias($table, $query);
    }

    /**
     * Get the second foreign table of the relationship for joins.
     *
     * @param Relation $relation
     * @param Builder $query
     * @return string|null
     */
    protected function getSecondJoinTable($relation, $query)
    {
        $table = $relation->getRelated()->getTable();

        return $this->getUniqueTableAlias($table, $query);
    }

    /**
     * Get a unique table alias for the specified table.
     *
     * @param string $table
     * @param Builder $query
     * @return string
     */
    protected function getUniqueTableAlias($table, $query)
    {
        static $aliasWith = 'a';

        if ($this->tableExists($table, $query)) {
            if (Str::contains($table, ' as ')) {
                $table = Str::before($table, ' as ');
            }

            $table = "{$table} as {$table}_{$aliasWith}";

            if ($this->tableExists($table, $query)) {
                $aliasWith ++;
                return $this->getUniqueTableAlias($table, $query);
            } else {
                return $table;
            }
        }

        return $table;
    }

    /**
     * Determine if the given table name is already exists in the join clauses.
     *
     * @param string $table
     * @param Builder $query
     * @return boolean
     */
    protected function tableExists($table, $query)
    {
        return in_array($table, $this->getJoinedTables($query))
        || $query->getModel()->getTable() === $table;
    }

    /**
     * Determine if the given column already exists in the select clause.
     *
     * @param string $column
     * @param Builder $query
     * @return boolean
     */
    protected function columnExists($column, $query)
    {
        return in_array($column, ($query->getQuery()->columns) ?? []);
    }

    /**
     * Get all the joined tables in the given query.
     *
     * @param Builder $query
     * @return array
     */
    protected function getJoinedTables($query)
    {
        return collect($query->getQuery()->joins)
        ->map(function (JoinClause $join) {
            return $join->table;
        })
        ->toArray();
    }

    /**
     * Get the table of the specified relation.
     *
     * @param Relation $relation
     * @return string
     */
    protected static function getRelationTable(Relation $relation)
    {
        return method_exists($relation, 'getTable')
        ? $relation->getTable()
        : $relation->getRelated()->getTable();
    }

    /**
     * Get the table of the specified join instance.
     *
     * @param JoinClause $join
     * @return string
     */
    public function getJoinTable(JoinClause $join)
    {
        return Str::after($join->table, ' as ');
    }

    /**
     * Copy the where contraints from the given query
     * to the given join.
     *
     * @param QueryBuilder $query
     * @param JoinClause $join
     * @param string $table
     * @return void
     */
    protected function mergeWhereConstraints($query, $join, $table)
    {
        $wheres = $query->wheres;
        $whereBindings = $query->getRawBindings()['where'] ?? [];

        // We will transform the column names of the wheres to
        // fully qualified column names by prepending their table name.
        foreach ($wheres as $index => $where) {
            if ($where['type'] === 'Nested') {
                $this->mergeWhereConstraints($where['query'], $join, $table);
            } elseif (array_key_exists('column', $where)) {
                $wheres[$index]['column'] = $this->getQualifiedColumnName(
                    $where['column'],
                    $table
                );
            }
        }

        // Here we have some other query that we want to merge the
        // where constraints from. We will copy over any where constraints
        // on the query. Then we will return ourselves with the finished merging.
        return $join->mergeWheres(
            $wheres,
            $whereBindings
        );
    }

    /**
     * Transform the column names of the query's select clauses to
     * fully qualified column names by prepending their table names.
     *
     * @param QueryBuilder $query
     * @param string $table
     * @return void
     */
    protected function qualifySelectColumns(QueryBuilder $query, $table)
    {
        if (! $query->columns) {
            return;
        }

        foreach ($query->columns as $index => $column) {
            $query->columns[$index] = $this->getQualifiedColumnName($column, $table);
        }
    }

    /**
     * Transform the column names of the query's where clauses to
     * fully qualified column names by prepending their table names.
     *
     * @param QueryBuilder $query
     * @param string $table
     * @param array $columns
     * @return void
     */
    protected function qualifyWhereColumns(QueryBuilder $query, $table, $columns)
    {
        $wheres = $query->wheres;
        $whereBindings = $query->getRawBindings()['where'] ?? [];

        // We will transform the column names of the wheres to
        // fully qualified column names by prepending their table name.
        foreach ($wheres as $index => $where) {

            // Run recursively on nested queries.
            if ($where['type'] === 'Nested') {
                $this->qualifyWhereColumns($where['query'], $table, $columns);
            } elseif (array_key_exists('column', $where)) {
                $column = $this->reverseColumnAlias($where['column'], $columns);

                $wheres[$index]['column'] = $this->getQualifiedColumnName(
                    $column,
                    $table
                );
            }
        }

        $query->wheres = [];
        $query->bindings['where'] = $whereBindings;

        $query->wheres = $wheres;
    }

    /**
     * Get the fully qualified column name from a given alias.
     *
     * @param string $columnAlias
     * @param array $columns
     * @return string
     */
    protected function reverseColumnAlias($columnAlias, $columns)
    {
        foreach ($columns as $column) {
            $search = ' as ';
            if (Str::contains($column, $search)) {
                $originalColumn = Str::before($column, $search);
                $alias = Str::afterLast($column, $search);

                if ($alias === $columnAlias) {
                    return $originalColumn;
                }
            }
        }

        return $columnAlias;
    }

    /**
     * Get an instance of the specified relationship (without constraints)
     * of the specified model.
     *
     * @param string $relationName
     * @param Model $model
     * @return Relation
     *
     * @throws RelationNotFoundException
     */
    protected static function getModelRelation($relationName, $model): Relation
    {
        return Relation::noConstraints(function () use ($relationName, $model) {
            try {
                return $model->newInstance()->$relationName();
            } catch (BadMethodCallException $e) {
                throw RelationNotFoundException::make($model, $relationName);
            }
        });
    }

    /**
     * Add fields from another related table to a parent table
     * using relationship joins.
     *
     * @param Builder $query.
     * @param string $relation.
     * @param array|string|null $fields.
     * @return Builder
     */
    public function scopeJoinSelect(
        Builder $query,
        $relation,
        $fields = null
    ) {
        $closures = null;
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $key => $field) {
            // If the $key is a string, we will use it to key the
            // closure that will be called on the corresponding relation.
            if (is_string($key)) {
                $closures[$key] = function ($query, $join, $table) use ($field) {
                    if (is_array($field)) {
                        foreach ($field as $column) {
                            $this->addUniqueSelect($query, $column, $table);
                        }
                    } else {
                        $this->addUniqueSelect($query, $field, $table);
                    }
                };

            // If the $key is an integer, we will select $field
            // from the last or right-most relationship's table.
            } else {
                $rightMostRelation = last(explode('.', $relation));
                $closures[$rightMostRelation][] = function ($query, $join, $table) use ($field) {
                    $this->addUniqueSelect($query, $field, $table);
                };
            }
        }

        return $query->joinRelation($relation, $closures);
    }

    /**
     * Get the fully qualified column name of the table.
     *
     * @param string $column
     * @param string|null $table
     * @return string
     */
    protected function getQualifiedColumnName($column, $table = null)
    {
        if (! $table || Str::contains($column, '.')) {
            return $column;
        }

        return "{$table}.$column";
    }

    /**
     * Add the select clause to the query only if had not been added.
     *
     * @param Builder $query
     * @param string|array $column
     * @param string|null $table
     * @return Builder
     */
    protected function addUniqueSelect($query, $column, $table = null)
    {
        if (is_array($column)) {
            foreach ($column as $value) {
                $this->addUniqueSelect($query, $value, $table);
            }

            return $query;
        }

        $column = $this->getQualifiedColumnName($column, $table);

        // Prevent duplicate selection of the same column.
        if ($this->columnExists($column, $query)) {
            return $query;
        }

        return $query->addSelect($column);
    }
}
