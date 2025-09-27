<?php


namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\User\{StoreUserRequest, UpdateUserRequest};

final class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->service->list($request);
        return response()->json($result);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->create($request->validated());
        return response()->json($user, 201);
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->service->find($id);
        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->service->update($id, $request->validated());
        return response()->json($user);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->softDelete($id);
        return response()->json(['ok' => true]);
    }

    public function restore(string $id): JsonResponse
    {
        $this->service->restore($id);
        return response()->json(['ok' => true]);
    }
}
