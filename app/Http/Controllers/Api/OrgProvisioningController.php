<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Org\ProvisioningRequest;
use App\Services\OrgProvisioningService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *   path="/api/v1/orgs/provision",
 *   tags={"Orgs"},
 *   summary="Inbound org provisioning via API key + signature",
 *   security={{"apiKeyAuth":{}}},
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"org_name","users"},
 *       @OA\Property(property="org_name", type="string"),
 *       @OA\Property(property="users", type="array", @OA\Items(type="object",
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="role", type="string")
 *       ))
 *     )
 *   ),
 *   @OA\Response(response=200, description="Org provisioned successfully"),
 *   @OA\Response(response=401, description="Unauthorized (bad signature)")
 * )
 */
class OrgProvisioningController extends Controller
{
    public function __construct(private OrgProvisioningService $service) {}

    public function provision(ProvisioningRequest $request): JsonResponse
    {
        $org = $this->service->provision(
            $request->validated('org_name'),
            $request->validated('users'),
            $request->header('X-Signature'),
            $request->header('X-Api-Key')
        );

        if (! $org) {
            return response()->json(['message' => 'Invalid signature or API key'], 401);
        }

        return response()->json(['message' => 'Org provisioned successfully', 'org_id' => $org->id]);
    }
}
