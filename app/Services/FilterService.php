<?php

namespace App\Services;

use App\DTOs\FilterConditionDTO;
use App\DTOs\FilterGroupDTO;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class FilterService
{
    /** @param string[] $allowedFields */
    public function apply(Builder $query, FilterGroupDTO $ast, array $allowedFields = []): Builder
    {
        if (!$ast->orGroups) {
            return $query;
        }

        foreach ($ast->orGroups as $orGroup) {
            $query->where(function (Builder $q) use ($orGroup, $allowedFields) {
                foreach ($orGroup as $idx => $cond) {
                    $this->assertAllowedField($cond, $allowedFields);

                    $method = $idx === 0 ? 'where' : 'orWhere';

                    $this->applyCondition($q, $cond, $method);
                }
            });
        }

        return $query;
    }

    private function assertAllowedField(FilterConditionDTO $cond, array $allowedFields): void
    {
        if ($allowedFields && ! in_array($cond->field, $allowedFields, true)) {
            throw new InvalidArgumentException("Filtering by '{$cond->field}' is not allowed.");
        }
    }

    private function applyCondition(Builder $q, FilterConditionDTO $cond, string $method): void
    {
        $field = $cond->field;
        $op    = $cond->operator;
        $val   = $cond->value;

        switch ($op) {
            case '==':
                if (is_string($val) && (str_contains($val, '*'))) {
                    $like = str_replace('*', '%', $val);
                    $q->{$method}($field, 'LIKE', $like);
                } else {
                    $q->{$method}($field, '=', $val);
                }
                break;

            case '!=':
                if (is_string($val) && (str_contains($val, '*'))) {
                    $like = str_replace('*', '%', $val);
                    // not like
                    $q->{$method}($field, 'NOT LIKE', $like);
                } else {
                    $q->{$method}($field, '!=', $val);
                }
                break;

            case '=gt=': $q->{$method}($field, '>',  $val); break;
            case '=ge=': $q->{$method}($field, '>=', $val); break;
            case '=lt=': $q->{$method}($field, '<',  $val); break;
            case '=le=': $q->{$method}($field, '<=', $val); break;

            case '=in=':
                $q->{$method.'In'}($field, is_array($val) ? $val : [$val]);
                break;

            case '=out=':
                $q->{$method.'NotIn'}($field, is_array($val) ? $val : [$val]);
                break;

            default:
                throw new InvalidArgumentException("Unsupported operator '{$op}'");
        }
    }
}
