<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;

final class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analytics) {}

    public function topLogins(Request $request): JsonResponse
    {
        $data = $this->analytics->topLogins($request->user(), (string)$request->query('window', '7d'));
        return response()->json($data);
    }

    public function inactive(Request $request): JsonResponse
    {
        $data = $this->analytics->inactiveUsers($request->user(), (string)$request->query('window', 'week'));
        return response()->json($data);
    }
}
