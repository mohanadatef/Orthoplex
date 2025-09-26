<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait QueryHelpers
{
    protected array $allowedFilters = [];
    protected array $allowedFields = [];
    protected array $allowedIncludes = [];

    protected function applyFilters(Builder $query, ?string $filter): void
    {
        if (!$filter) return;

        $rules = explode(';', $filter);
        foreach ($rules as $rule) {
            if (preg_match('/(.+?)(==|!=|>=|<=|=in=|=like=)(.+)/', $rule, $matches)) {
                [$all, $field, $op, $value] = $matches;
                if (!in_array($field, $this->allowedFilters)) continue;

                switch ($op) {
                    case '==': $query->where($field, $value); break;
                    case '!=': $query->where($field, '!=', $value); break;
                    case '>=': $query->where($field, '>=', $value); break;
                    case '<=': $query->where($field, '<=', $value); break;
                    case '=like=': $query->where($field, 'like', "%$value%"); break;
                    case '=in=': $query->whereIn($field, explode(',', $value)); break;
                }
            }
        }
    }

    protected function applySparseFields($collection, ?string $fields)
    {
        if (!$fields) return $collection;

        $fieldsArray = array_intersect(
            explode(',', $fields),
            $this->allowedFields
        );

        return $collection->map(function ($item) use ($fieldsArray) {
            return collect($item)->only($fieldsArray);
        });
    }

    protected function applyIncludes(Builder $query, ?string $includes): void
    {
        if (!$includes) return;

        $requested = explode(',', $includes);
        $valid = array_intersect($requested, $this->allowedIncludes);
        $query->with($valid);
    }
}
