<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiKeys\StoreApiKeyRequest;
use App\Http\Requests\ApiKeys\RotateApiKeyRequest;
use App\Services\ApiKeyService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ApiKeyController extends Controller
{
    public function __construct(
        private readonly ApiKeyService $apiKeyService
    ) {}

    /**
     * @OA\Post(
     *   path="/api/api-keys",
     *   summary="Create a new API Key",
     *   tags={"API Keys"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="Integration Key"),
     *       @OA\Property(property="scopes", type="array", @OA\Items(type="string"), example={"users.read","users.update"}),
     *       @OA\Property(property="expires_in_days", type="integer", example=30)
     *     )
     *   ),
     *   @OA\Response(response=201, description="API key created"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(StoreApiKeyRequest $request): JsonResponse
    {
        $dto = $this->apiKeyService->create(
            $request->validated('name'),
            $request->validated('scopes') ?? [],
            $request->validated('expires_in_days')
        );

        return response()->json([
            'id' => $dto->id,
            'api_key' => $dto->key,
            'expires_at' => $dto->expires_at,
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/api/api-keys/{id}/rotate",
     *   summary="Rotate an existing API Key",
     *   tags={"API Keys"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      required=true,
     *      @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="API key rotated"),
     *   @OA\Response(response=404, description="API key not found"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function rotate(RotateApiKeyRequest $request, int $id): JsonResponse
    {
        $dto = $this->apiKeyService->rotate($id);

        if (!$dto) {
            return response()->json(['error' => 'API Key not found'], 404);
        }

        return response()->json([
            'id' => $dto->id,
            'new_key' => $dto->key,
            'rotated_at' => $dto->rotated_at,
        ]);
    }
}
