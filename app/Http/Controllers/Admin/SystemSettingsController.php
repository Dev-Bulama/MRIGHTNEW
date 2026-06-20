<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppDownload;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        $downloads = AppDownload::orderBy('platform')->get();
        return view('admin.system-settings.index', compact('settings', 'downloads'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'reward_phone' => ['nullable', 'string', 'max:20'],
            'reward_message' => ['nullable', 'string', 'max:500'],
            'search_result_case1_message' => ['nullable', 'string', 'max:1000'],
            'search_result_case2_message' => ['nullable', 'string', 'max:1000'],
            'search_result_case3_message' => ['nullable', 'string', 'max:1000'],
            'enable_camera_capture' => ['nullable', 'boolean'],
            'enable_intelligence_collection' => ['nullable', 'boolean'],
            'enable_whatsapp_otp' => ['nullable', 'boolean'],
            'otp_expiry_minutes' => ['nullable', 'integer', 'min:5', 'max:60'],
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                SystemSetting::set($key, $value);
            }
        }

        Cache::flush();

        return back()->with('success', 'Settings updated successfully.');
    }

    public function storeDownload(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'platform' => ['required', 'in:android,ios,windows,other'],
            'version' => ['nullable', 'string', 'max:20'],
            'external_url' => ['nullable', 'url'],
            'file' => ['nullable', 'file', 'max:102400'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('downloads', 'public');
        }

        AppDownload::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'platform' => $validated['platform'],
            'version' => $validated['version'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        return back()->with('success', 'App download added successfully.');
    }

    public function destroyDownload(AppDownload $download)
    {
        $download->delete();
        return back()->with('success', 'Download removed.');
    }
}
