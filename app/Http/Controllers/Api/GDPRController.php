<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ExportUserDataJob;
use App\Models\DeleteRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Post(
 *   path="/api/v1/gdpr/export",
 *   tags={"GDPR"},
 *   summary="Request export of user data",
 *   security={{"bearerAuth":{}}},
 *   @OA\Response(response=200, description="Export started")
 * )
 *
 * @OA\Post(
 *   path="/api/v1/gdpr/delete-request",
 *   tags={"GDPR"},
 *   summary="Request account deletion",
 *   security={{"bearerAuth":{}}},
 *   @OA\RequestBody(
 *     required=false,
 *     @OA\JsonContent(@OA\Property(property="reason", type="string"))
 *   ),
 *   @OA\Response(response=200, description="Delete request received")
 * )
 */
class GDPRController extends Controller {
    public function export(Request $request) {
        $user = Auth::user();
        ExportUserDataJob::dispatch($user->id);
        return response()->json(['message' => 'Export started']);
    }

    public function requestDelete(Request $request) {
        $user = Auth::user();
        $dr = DeleteRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'reason' => $request->input('reason')
        ]);
        return response()->json(['message' => 'Delete request received', 'id' => $dr->id]);
    }
    public function approve($id)
    {
        $this->authorize('approve', \App\Models\DeleteRequest::class);

        $req = \App\Models\DeleteRequest::findOrFail($id);
        if ($req->status !== 'pending') {
            return response()->json(['message'=>'Already processed'],400);
        }

        $req->status = 'approved';
        $req->approved_by = auth()->id();
        $req->approved_at = now();
        $req->save();

        // Trigger account deletion job
        \App\Jobs\DeleteUserDataJob::dispatch($req->user);

        $req->user->notify(new \App\Notifications\DeleteRequestApprovedNotification());

        return response()->json(['message'=>'Delete request approved']);
    }

    public function reject($id)
    {
        $this->authorize('approve', \App\Models\DeleteRequest::class);

        $req = \App\Models\DeleteRequest::findOrFail($id);
        if ($req->status !== 'pending') {
            return response()->json(['message'=>'Already processed'],400);
        }

        $req->status = 'rejected';
        $req->approved_by = auth()->id();
        $req->approved_at = now();
        $req->save();

        $req->user->notify(new \App\Notifications\DeleteRequestRejectedNotification());

        return response()->json(['message'=>'Delete request rejected']);
    }
}
