<?php

namespace Core\Database\Eloquent\Concerns;

use Core\Database\Query\Builder;

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
