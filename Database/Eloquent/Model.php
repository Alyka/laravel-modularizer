<?php

namespace Alyka\Modularizer\Database\Eloquent;

use Alyka\Modularizer\Database\Eloquent\Concerns\Model as ConcernsModel;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{
    use ConcernsModel;

    /**
     * List of fields to be searched during a search query.
     *
     * @var string[]
     */
    protected $searchable = [];

    /**
     * Attributes the model can be filtered by.
     *
     * @var array[]
     */
    protected $filterable = [];
}
