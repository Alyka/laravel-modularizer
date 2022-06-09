<?php

namespace Modularizer\Database\Eloquent\Concerns;

use Modularizer\Database\Query\Builder;

trait CustomizesQueryBuilder
{
    /**
     * @inheritDoc
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new Builder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }
}
