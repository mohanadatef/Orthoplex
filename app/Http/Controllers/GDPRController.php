<?php


namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\GDPRService;

final class GDPRController extends Controller
{
    public function __construct(private GDPRService $gdpr) {}

    public function export(Request $request, string $id): JsonResponse
    {
        $this->gdpr->enqueueExport($request->user(), $id);
        return response()->json(['queued' => true]);
    }

    public function requestDelete(Request $request, string $id): JsonResponse
    {
        $this->gdpr->requestDeletion($request->user(), $id);
        return response()->json(['queued' => true]);
    }
}
