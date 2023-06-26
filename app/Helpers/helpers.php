<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

function get_fillable(string $modelClass): array|null
{
    if (class_exists($modelClass)) {
        $instance = new $modelClass;
        if (($instance instanceof Model)) {
            return $instance->getFillable();
        }
    }

    return null;
}
