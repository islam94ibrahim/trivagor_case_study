<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ItemLocationCityFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereHas('location', function (Builder $query) use ($value) {
            $query->where('city', 'like', "%{$value}%");
        });
    }
}
