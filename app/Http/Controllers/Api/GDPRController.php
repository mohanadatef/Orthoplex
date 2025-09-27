<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GDPR\ExportRequest;
use App\Http\Requests\GDPR\DeleteAccountRequest;
use App\DTOs\DeleteRequestDTO;
use App\Services\GDPRService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class GDPRController extends Controller
{
    public function __construct(
        private readonly GDPRService $gdprService
    ) {}

    /**
     * @OA\Post(
     *   path="/api/v1/gdpr/export",
     *   tags={"GDPR"},
     *   summary="Request export of user data",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(response=200, description="Export started")
     * )
     */
    public function export(ExportRequest $request): JsonResponse
    {
        $this->gdprService->exportUserData();
        return response()->json(['message' => 'Export started']);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/gdpr/delete-request",
     *   tags={"GDPR"},
     *   summary="Request account deletion",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(@OA\Property(property="reason", type="string"))
     *   ),
     *   @OA\Response(response=200, description="Delete request received")
     * )
     */
    public function requestDelete(DeleteAccountRequest $request): JsonResponse
    {
        $dto = new DeleteRequestDTO(auth()->id(), 'pending', $request->validated('reason'));
        $dr = $this->gdprService->requestDelete($dto);

        return response()->json(['message' => 'Delete request received', 'id' => $dr->id]);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/gdpr/delete-request/{id}/approve",
     *   tags={"GDPR"},
     *   summary="Approve account deletion request",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Delete request approved")
     * )
     */
    public function approve(int $id): JsonResponse
    {
        $this->authorize('approve', \App\Models\DeleteRequest::class);

        $req = $this->gdprService->approve($id);

        if (!$req) {
            return response()->json(['message'=>'Already processed or not found'],400);
        }

        return response()->json(['message'=>'Delete request approved']);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/gdpr/delete-request/{id}/reject",
     *   tags={"GDPR"},
     *   summary="Reject account deletion request",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Delete request rejected")
     * )
     */
    public function reject(int $id): JsonResponse
    {
        $this->authorize('approve', \App\Models\DeleteRequest::class);

        $req = $this->gdprService->reject($id);

        if (!$req) {
            return response()->json(['message'=>'Already processed or not found'],400);
        }

        return response()->json(['message'=>'Delete request rejected']);
    }
}
