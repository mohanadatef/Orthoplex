<?php

namespace App\Traits;

use App\Services\RsqlParser;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait QueryHelpers
{
    /**
     * @param Builder $query
     * @param string|null $filter
     * @param string[] $allowedFields
     */
    protected function applyFilters(Builder $query, ?string $filter, array $allowedFields = []): void
    {
        if (!$filter) return;

        $parser  = app(RsqlParser::class);
        $service = app(FilterService::class);

        $ast = $parser->parse($filter);
        $service->apply($query, $ast, $allowedFields);
    }


    protected function applyIncludes(Builder $query, ?string $include, array $allowedIncludes = []): void
    {
        if (!$include) return;

        $relations = array_filter(array_map('trim', explode(',', $include)));
        if (!$relations) return;

        $safe = array_values(array_intersect($relations, $allowedIncludes));
        if ($safe) {
            $query->with($safe);
        }
    }


    protected function applySparseFields(iterable $data, ?string $fields): array
    {
        if (!$fields) return is_array($data) ? $data : collect($data)->all();

        $requested = array_filter(array_map('trim', explode(',', $fields)));
        if (!$requested) return is_array($data) ? $data : collect($data)->all();

        return collect($data)->map(function ($row) use ($requested) {
            $arr = is_array($row) ? $row : (array)$row;
            return Arr::only($arr, $requested);
        })->all();
    }
}
