<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\GdprExport;

class GdprDownloadController extends Controller
{
    public function download(Request $request, string $token): BinaryFileResponse
    {
        $exp = GdprExport::where('token',$token)->firstOrFail();
        if ($exp->downloaded_at || ($exp->available_until && now()->greaterThan($exp->available_until))) {
            abort(410, 'Link expired');
        }
        $exp->downloaded_at = now();
        $exp->save();

        return response()->download(storage_path('app/'.$exp->path))->deleteFileAfterSend(false);
    }
}
