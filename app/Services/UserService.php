<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Support\Query\RsqlFilter;

final class UserService
{
    public function list(Request $request): array
    {
        $query = User::query();
        // includes
        $includes = array_filter(explode(',', (string)$request->query('include','')));
        if (in_array('org',$includes, true)) $query->with('org');

        // filters (RSQL subset) with whitelist
        if ($rsql = (string)$request->query('filter','')) {
            $query = RsqlFilter::apply($query, $rsql, ['name'=>true,'email'=>true,'org_id'=>true,'deleted_at'=>true]);
        }

        // pagination
        $cursor = $request->query('cursor');
        $perPage = (int) $request->query('per_page', 15);
        $paginator = $query->orderBy('id')->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

        // sparse fieldsets
        $fields = array_filter(explode(',', (string)$request->query('fields.users','')));
        $data = array_map(function($u) use ($fields) {
            $arr = $u->toArray();
            if ($fields) $arr = array_intersect_key($arr, array_flip($fields));
            return $arr;
        }, $paginator->items());

        return [
            'data' => $data,
            'next_cursor' => optional($paginator->nextCursor())->encode(),
        ];
    }

    public function create(array $data): User
    {
        return DB::transaction(fn() => User::create($data));
    }

    public function find(string $id): User
    {
        $user = User::find($id);
        if (!$user) throw new ModelNotFoundException('User not found');
        return $user;
    }

    public function update(string $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->find($id);
            if (isset($data['version']) && $data['version'] !== $user->version) {
                abort(409, 'Version conflict');
            }
            $user->fill($data);
            $user->version++;
            $user->save();
            return $user;
        });
    }

    public function softDelete(string $id): void
    {
        $user = $this->find($id);
        $user->delete();
    }

    public function restore(string $id): void
    {
        $user = User::withTrashed()->find($id);
        if (!$user) abort(404, 'User not found');
        $user->restore();
    }
}
