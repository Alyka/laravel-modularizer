<?php

namespace Modularizer\Concerns;

use Illuminate\Support\Facades\DB;

trait Aggregatable
{
    /**
     * Get the sum of values on the specified field(s).
     *
     * @param array|...string ...$fields Fields to sum.
     * @return int
     */
    public function sum(...$fields)
    {
        $fields = is_array($fields[0]) ? $fields[0] : $fields;

        $fields = DB::raw(implode('+', $fields));

        return $this->getBuilder()->sum($fields);
    }
}
