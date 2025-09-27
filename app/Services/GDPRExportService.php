<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;

class GDPRExportService
{
    public function __construct(
        private readonly string $disk = 'private'
    ) {}

    /**
     * يبني ZIP فيه ملفات JSON متعددة: user.json, orgs.json, roles.json, api_keys.json, logins.json, audit.json
     * ويرجّع [disk, path] لمسار الملف النهائي.
     */
    public function buildZipForUser(User $user): array
    {
        // 1) جهّز مجلد عمل مؤقت
        $tmpBase = storage_path('app/tmp/gdpr/' . $user->id . '/' . Str::uuid());
        if (! is_dir($tmpBase)) {
            mkdir($tmpBase, 0775, true);
        }

        // 2) حضّر البيانات (عدّل/وسّع حسب احتياجك)
        file_put_contents($tmpBase . '/user.json', json_encode([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

        // علاقات (إن وُجدت)
        $orgs = $user->orgs()->select('orgs.id','orgs.name','orgs.webhook_url')->get();
        file_put_contents($tmpBase . '/orgs.json', $orgs->toJson(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

        if (method_exists($user, 'roles')) {
            $roles = $user->roles->pluck('name');
            file_put_contents($tmpBase . '/roles.json', $roles->toJson(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }

        if (class_exists(\App\Models\ApiKey::class)) {
            $keys = \App\Models\ApiKey::where('user_id',$user->id)->get(['id','name','scopes','created_at','expires_at','rotated_at']);
            file_put_contents($tmpBase . '/api_keys.json', $keys->toJson(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }

        if (class_exists(\App\Models\LoginEvent::class)) {
            $logins = \App\Models\LoginEvent::where('user_id',$user->id)
                ->latest('id')->limit(1000)->get(['id','ip_address','user_agent','created_at']);
            file_put_contents($tmpBase . '/logins.json', $logins->toJson(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }

        if (class_exists(\App\Models\AuditLog::class)) {
            $audit = \App\Models\AuditLog::where('user_id',$user->id)
                ->latest('id')->limit(1000)->get(['id','action','ip_address','user_agent','metadata','created_at']);
            file_put_contents($tmpBase . '/audit.json', $audit->toJson(JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }

        // 3) اصنع ZIP محلياً ثم ارفعه للـ disk الخاص
        $zipName = 'gdpr_export_' . $user->id . '_' . now()->format('Ymd_His') . '.zip';
        $zipFullPath = $tmpBase . '/' . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Unable to create ZIP for GDPR export');
        }

        foreach (glob($tmpBase . '/*.json') as $jsonFile) {
            $zip->addFile($jsonFile, basename($jsonFile));
        }
        $zip->close();

        // 4) خزّنه على disk خاص
        $stream = fopen($zipFullPath, 'r');
        $targetPath = 'gdpr/' . $user->id . '/' . $zipName;
        Storage::disk($this->disk)->put($targetPath, $stream);
        fclose($stream);

        // تنظيف الملفات المؤقتة (اختياري)
        @array_map('unlink', glob($tmpBase . '/*.json'));
        @unlink($zipFullPath);
        @rmdir($tmpBase); // لو فاضي

        return [$this->disk, $targetPath];
    }
}
