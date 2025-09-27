<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserFilterRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    /**
     * @OA\Get(
     *   path="/api/v1/users",
     *   tags={"Users"},
     *   summary="List users with filters and pagination",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="List of users")
     * )
     */
    public function index(UserFilterRequest $request): JsonResponse
    {
        $result = $this->service->list(
            $request->validated(),
            $request->query('include'),
            $request->query('fields')
        );

        return response()->json($result);
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/users/{id}",
     *   tags={"Users"},
     *   summary="Soft delete a user",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="User soft-deleted successfully")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', \App\Models\User::class);

        if (! $this->service->delete($id)) {
            return response()->json(['message'=>'User not found'],404);
        }

        return response()->json(['message'=>'User soft-deleted successfully']);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/users/{id}/restore",
     *   tags={"Users"},
     *   summary="Restore a soft-deleted user",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="User restored successfully"),
     *   @OA\Response(response=400, description="User is not deleted")
     * )
     */
    public function restore(int $id): JsonResponse
    {
        $this->authorize('restore', \App\Models\User::class);

        if (! $this->service->restore($id)) {
            return response()->json(['message'=>'User not found or not deleted'],400);
        }

        return response()->json(['message'=>'User restored successfully']);
    }
}
