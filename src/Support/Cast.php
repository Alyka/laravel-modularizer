<?php

namespace Modularizer\Support;

class Cast
{
    /**
     * Cast boolean literal to tinyint.
     *
     * @param string $value
     * @return bool
     */
    public function bool($value)
    {
        return $value == 'true' ? 1 : 0;
    }
}
