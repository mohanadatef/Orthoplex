<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invitation\InviteRequest;
use App\Http\Requests\Invitation\AcceptRequest;
use App\Services\InvitationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class InvitationController extends Controller
{
    public function __construct(private InvitationService $service) {}

    /**
     * @OA\Post(
     *   path="/api/v1/invitations",
     *   tags={"Invitations"},
     *   summary="Invite user to organization",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","role","org_id"},
     *       @OA\Property(property="email", type="string", example="user@example.com"),
     *       @OA\Property(property="role", type="string", example="member"),
     *       @OA\Property(property="org_id", type="integer", example=1)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Invitation created")
     * )
     */
    public function invite(InviteRequest $request): JsonResponse
    {
        $inv = $this->service->create(
            $request->validated('org_id'),
            $request->validated('email'),
            $request->validated('role')
        );

        return response()->json([
            'message' => 'Invitation created',
            'token'   => $inv->token
        ]);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/invitations/accept",
     *   tags={"Invitations"},
     *   summary="Accept an invitation",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"token"},
     *       @OA\Property(property="token", type="string", example="somerandomtoken123")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Invitation accepted"),
     *   @OA\Response(response=400, description="Invalid or expired invitation")
     * )
     */
    public function accept(AcceptRequest $request): JsonResponse
    {
        $userId = $request->user()->id;

        if (! $this->service->accept($request->validated('token'), $userId)) {
            return response()->json(['message'=>'Invalid or expired invitation'], 400);
        }

        return response()->json(['message'=>'Invitation accepted']);
    }
}
