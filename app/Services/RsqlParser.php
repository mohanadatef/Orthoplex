<?php

namespace App\Services;

use App\DTOs\FilterConditionDTO;
use App\DTOs\FilterGroupDTO;
use Illuminate\Support\Str;
use InvalidArgumentException;

class RsqlParser
{
    /** @var string[] */
    private array $supported = ['==','!=','=gt=','=ge=','=lt=','=le=','=in=','=out='];

    public function parse(?string $filter): FilterGroupDTO
    {
        if (!$filter) {
            return new FilterGroupDTO(orGroups: []);
        }

        // AND-split by ';'
        $andParts = array_filter(array_map('trim', explode(';', $filter)), fn($p) => $p !== '');

        $orGroupsPerAnd = [];

        foreach ($andParts as $andPart) {
            // OR-split by ','
            $orParts = array_filter(array_map('trim', $andPart !== '' ? explode(',', $andPart) : []), fn($p) => $p !== '');
            if (!$orParts) {
                continue;
            }

            $group = [];
            foreach ($orParts as $expr) {
                $group[] = $this->parseExpression($expr);
            }
            $orGroupsPerAnd[] = $group;
        }

        return new FilterGroupDTO(orGroups: $orGroupsPerAnd);
    }

    private function parseExpression(string $expr): FilterConditionDTO
    {
        $op = null;
        foreach ($this->supported as $candidate) {
            if (Str::contains($expr, $candidate)) {
                $op = $candidate;
                break;
            }
        }
        if (!$op) {
            throw new InvalidArgumentException("Unsupported or missing operator in '{$expr}'");
        }

        [$left, $right] = explode($op, $expr, 2);
        $field = trim($left);
        $rawValue = trim($right);

        $value = $this->parseValue($op, $rawValue);

        return new FilterConditionDTO($field, $op, $value);
    }

    private function parseValue(string $op, string $raw): mixed
    {
        // in/out lists: value must be like: (a,b,c)
        if (in_array($op, ['=in=', '=out='], true)) {
            if (!Str::startsWith($raw, '(') || !Str::endsWith($raw, ')')) {
                throw new InvalidArgumentException("List value must be in parentheses for '{$op}'");
            }
            $inside = trim(Str::of($raw)->substr(1, Str::length($raw) - 2));
            if ($inside === '') return [];
            $parts = array_map('trim', explode(',', $inside));
            return array_map([$this, 'castScalar'], $parts);
        }

        // scalar:
        return $this->castScalar($raw);
    }

    private function castScalar(string $raw): mixed
    {
        // quoted strings
        if ((Str::startsWith($raw, '"') && Str::endsWith($raw, '"')) ||
            (Str::startsWith($raw, "'") && Str::endsWith($raw, "'"))) {
            return Str::of($raw)->substr(1, Str::length($raw) - 2)->__toString();
        }

        $lower = strtolower($raw);
        if ($lower === 'null') return null;
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;

        // numeric (int/float)
        if (is_numeric($raw)) {
            return Str::contains($raw, '.') ? (float)$raw : (int)$raw;
        }

        // unquoted string
        return $raw;
    }
}
