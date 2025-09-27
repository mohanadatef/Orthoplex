<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Webhook\ReceiveWebhookRequest;
use App\Services\WebhookService;
use App\DTOs\WebhookDTO;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class WebhookController extends Controller
{
    public function __construct(private WebhookService $service) {}

    /**
     * @OA\Post(
     *   path="/api/v1/webhooks/receive",
     *   tags={"Webhooks"},
     *   summary="Receive and dispatch a webhook",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"url","event","payload"},
     *       @OA\Property(property="url", type="string"),
     *       @OA\Property(property="event", type="string"),
     *       @OA\Property(property="payload", type="object")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Accepted")
     * )
     */
    public function receive(ReceiveWebhookRequest $request): JsonResponse
    {
        $dto = new WebhookDTO(
            $request->validated('url'),
            $request->validated('event'),
            $request->validated('payload')
        );

        $webhook = $this->service->receive($dto);

        return response()->json(['id'=>$webhook->id,'status'=>$webhook->status]);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/webhooks/status/{id}",
     *   tags={"Webhooks"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Webhook status")
     * )
     */
    public function status(int $id): JsonResponse
    {
        $webhook = $this->service->getStatus($id);
        return $webhook
            ? response()->json($webhook)
            : response()->json(['message'=>'Webhook not found'],404);
    }
}
