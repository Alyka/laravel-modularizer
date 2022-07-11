<?php

namespace Modularizer\Database\Eloquent;

use Modularizer\Database\Eloquent\Concerns\Model as ConcernsModel;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use ConcernsModel;

    /**
     * The model factory instance.
     *
     * @var string
     */
    protected $factory;

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

    /**
     * Set the factory class.
     *
     * @param string $factory
     * @return $this
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;

        return $this;
    }
}
