<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use OpenApi\Annotations as OA;

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
 *
 * @OA\Get(
 *   path="/api/v1/webhooks/status/{id}",
 *   tags={"Webhooks"},
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *   @OA\Response(response=200, description="Webhook status")
 * )
 */

class WebhookController extends Controller {
    public function receive(Request $request) {
        $data = $request->validate([
            'url' => 'required|url',
            'event' => 'required|string',
            'payload' => 'required|array',
        ]);
        $webhook = Webhook::create([
            'url' => $data['url'],
            'event' => $data['event'],
            'payload' => $data['payload'],
            'status' => 'pending',
        ]);

        try {
            $res = Http::timeout(8)->post($webhook->url, $webhook->payload);
            $webhook->attempts = 1;
            if ($res->successful()) {
                $webhook->status = 'delivered';
            } else {
                $webhook->status = 'failed';
                $webhook->last_error = $res->body();
                $webhook->next_attempt_at = now()->addMinutes(5);
            }
            $webhook->save();
        } catch (\Exception $e) {
            $webhook->status = 'failed';
            $webhook->last_error = $e->getMessage();
            $webhook->attempts = 1;
            $webhook->next_attempt_at = now()->addMinutes(5);
            $webhook->save();
        }
        return response()->json(['id'=>$webhook->id,'status'=>$webhook->status]);
    }

    public function status($id) {
        $webhook = Webhook::findOrFail($id);
        return response()->json($webhook);
    }
}
