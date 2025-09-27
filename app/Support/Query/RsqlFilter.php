<?php
namespace App\Support\Query;

use Illuminate\Database\Eloquent\Builder;

final class RsqlFilter
{
    /** @param array<string,bool> $whitelist */
    public static function apply(Builder $q, string $rsql, array $whitelist): Builder
    {
        // Very small safe subset: field==value; field!=value; and/or with ;
        // No arbitrary operators; only whitelisted fields.
        $tokens = preg_split('/;|,/', $rsql) ?: [];
        foreach ($tokens as $tok) {
            $tok = trim($tok);
            if ($tok === '') continue;
            if (str_contains($tok, '==')) {
                [$f,$v] = array_map('trim', explode('==',$tok,2));
                if (!isset($whitelist[$f])) continue;
                $q->where($f, $v);
            } elseif (str_contains($tok, '!=')) {
                [$f,$v] = array_map('trim', explode('!=',$tok,2));
                if (!isset($whitelist[$f])) continue;
                $q->where($f, '!=', $v);
            }
        }
        return $q;
    }
}
