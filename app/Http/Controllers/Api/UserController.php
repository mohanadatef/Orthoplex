<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\QueryHelpers;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use QueryHelpers;

    public function index(Request $request)
    {
        $query = User::query();

        $this->applyFilters($query, $request->query('filter'));
        $this->applyIncludes($query, $request->query('include'));

        $users = $query->orderBy('id')->cursorPaginate(15);

        $data = collect($users->items())->map(fn($user) => $user->toArray());
        $data = $this->applySparseFields($data, $request->query('fields'));

        return response()->json([
            'data' => $data,
            'meta' => [
                'next_cursor' => $users->nextCursor()?->encode(),
                'prev_cursor' => $users->previousCursor()?->encode(),
            ]
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['message' => 'User soft-deleted successfully']);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('restore', $user);

        if ($user->trashed()) {
            $user->restore();
            return response()->json(['message' => 'User restored successfully']);
        }

        return response()->json(['message' => 'User is not deleted'], 400);
    }
}
