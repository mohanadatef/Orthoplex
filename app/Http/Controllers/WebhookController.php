<?php


namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\WebhookService;

final class WebhookController extends Controller
{
    public function __construct(private WebhookService $svc) {}

    public function deliver(Request $request): JsonResponse
    {
        $this->svc->deliver($request->all());
        return response()->json(['ok' => true]);
    }
}
