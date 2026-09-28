<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('setting_value', 'setting_key')->toArray();
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $newSettings = $request->input('settings', []);

        foreach ($newSettings as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityService::log(auth()->id(), 'update_settings', 'تحديث إعدادات المنصة');

        return back()->with('success', '✅ تم تحديث الإعدادات بنجاح!');
    }
}
