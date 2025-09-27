<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Org\OrgStoreRequest;
use App\Http\Requests\Org\OrgUpdateRequest;
use App\Services\OrgService;
use App\DTOs\OrgDTO;
use App\Models\Org;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class OrgController extends Controller
{
    public function __construct(private OrgService $service) {}

    /**
     * @OA\Post(
     *   path="/api/v1/orgs",
     *   tags={"Orgs"},
     *   summary="Create new organization",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="webhook_url", type="string"),
     *       @OA\Property(property="webhook_secret", type="string")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Org created")
     * )
     */
    public function store(OrgStoreRequest $request): JsonResponse
    {
        $dto = new OrgDTO(
            $request->validated('name'),
            $request->validated('webhook_url'),
            $request->validated('webhook_secret'),
        );

        $org = $this->service->create($dto);

        return response()->json($org,201);
    }

    /**
     * @OA\Put(
     *   path="/api/v1/orgs/{id}",
     *   tags={"Orgs"},
     *   summary="Update organization",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Org updated")
     * )
     */
    public function update(OrgUpdateRequest $request, Org $org): JsonResponse
    {
        $dto = new OrgDTO(
            $request->validated('name') ?? $org->name,
            $request->validated('webhook_url') ?? $org->webhook_url,
            $request->validated('webhook_secret') ?? $org->webhook_secret,
        );

        $this->service->update($org,$dto);

        return response()->json(['message'=>'Org updated successfully']);
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/orgs/{id}",
     *   tags={"Orgs"},
     *   summary="Delete organization",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Org deleted")
     * )
     */
    public function destroy(Org $org): JsonResponse
    {
        $this->service->delete($org);
        return response()->json(['message'=>'Org deleted successfully']);
    }
}
