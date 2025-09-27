<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Org\OrgStoreRequest;
use App\Http\Requests\Org\OrgUpdateRequest;
use App\Services\OrgService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="Orgs",
 *   description="Organization management endpoints (internal, JWT + RBAC)"
 * )
 */
class OrgController extends Controller
{
    public function __construct(private OrgService $service) {}

    /**
     * @OA\Get(
     *   path="/api/v1/orgs",
     *   tags={"Orgs"},
     *   summary="List organizations current user belongs to",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="List of orgs")
     * )
     */
    public function index(): JsonResponse
    {
        $orgs = $this->service->list(auth()->id());
        return response()->json($orgs);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/orgs",
     *   tags={"Orgs"},
     *   summary="Create a new organization",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=201, description="Org created")
     * )
     */
    public function store(OrgStoreRequest $request): JsonResponse
    {
        $org = $this->service->create($request->validated(), auth()->id());
        return response()->json(['org' => $org], 201);
    }

    /**
     * @OA\Put(
     *   path="/api/v1/orgs/{id}",
     *   tags={"Orgs"},
     *   summary="Update organization",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Org updated")
     * )
     */
    public function update(OrgUpdateRequest $request, int $id): JsonResponse
    {
        $this->authorize('update', \App\Models\Org::class);

        $org = $this->service->update($id, $request->validated());
        if (! $org) return response()->json(['message' => 'Org not found'], 404);

        return response()->json(['org' => $org]);
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/orgs/{id}",
     *   tags={"Orgs"},
     *   summary="Delete (soft delete) organization",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Org deleted")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', \App\Models\Org::class);

        if (! $this->service->delete($id)) {
            return response()->json(['message' => 'Org not found'], 404);
        }

        return response()->json(['message' => 'Org deleted']);
    }
}
