<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\InactiveUsersRequest;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService
    )
    {
    }

    /**
     * @OA\Get(
     *   path="/api/users/top-logins",
     *   summary="Get users with most logins",
     *   tags={"Analytics"},
     *   @OA\Response(response=200, description="Top users by login count")
     * )
     */
    public function topLogins(): JsonResponse
    {
        $data = $this->analyticsService->topLogins();
        return response()->json($data);
    }

    /**
     * @OA\Get(
     *   path="/api/users/inactive",
     *   summary="Get inactive users",
     *   tags={"Analytics"},
     *   @OA\Parameter(
     *      name="days",
     *      in="query",
     *      description="Number of days without login",
     *      required=false,
     *      @OA\Schema(type="integer", default=30)
     *   ),
     *   @OA\Response(response=200, description="List of inactive users")
     * )
     */
    public function inactive(InactiveUsersRequest $request): JsonResponse
    {
        $days = $request->validated('days', 30);
        $data = $this->analyticsService->inactive($days);

        return response()->json($data);
    }
}
