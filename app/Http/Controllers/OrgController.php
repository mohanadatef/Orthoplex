<?php


namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\OrgProvisioningService;

final class OrgController extends Controller
{
    public function __construct(private OrgProvisioningService $svc) {}

    public function provision(Request $request): JsonResponse
    {
        $this->svc->provisionInbound($request);
        return response()->json(['ok' => true]);
    }
}
