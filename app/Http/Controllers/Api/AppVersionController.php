<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppVersion;

class AppVersionController extends Controller
{
    public function latest(Request $request)
    {
        $request->validate([
            'platform' => 'nullable|in:android,ios',
        ]);

        $latest = AppVersion::where('platform', $request->get('platform', 'android'))
            ->latest('id')
            ->first();

        if (! $latest) {
            return response()->json([
                'message' => 'Versi aplikasi belum tersedia.',
            ], 404);
        }

        return response()->json($this->buildVersionPayload($latest));
    }

    public function check(Request $request)
    {
        $request->validate([
            'platform' => 'required|in:android,ios',
            'version'  => 'required',
        ]);

        $latest = AppVersion::where('platform', $request->platform)
            ->orderByDesc('id')
            ->first();

        if (! $latest) {
            return response()->json(['update' => false]);
        }

        if (version_compare($request->version, $latest->version, '>=')) {
            return response()->json(['update' => false]);
        }

        return response()->json(array_merge([
            'update' => true,
        ], $this->buildVersionPayload($latest)));
    }

    private function buildVersionPayload(AppVersion $appVersion): array
    {
        return [
            'version' => $appVersion->version,
            'minimum_version' => $appVersion->minimum_version ?: $appVersion->version,
            'apk_url' => $appVersion->apk_url
                ? asset('storage/' . ltrim($appVersion->apk_url, '/'))
                : null,
            'force_update' => (bool) $appVersion->force_update,
            'changelog' => $appVersion->message,
        ];
    }
}
