<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\OrgProvisioningService;

class OrgProvisioningController extends Controller
{
    public function __construct(private OrgProvisioningService $svc) {}

    public function handle(Request $request): JsonResponse
    {
        $this->svc->provisionInbound($request);
        return response()->json(['ok'=>true]);
    }
}
