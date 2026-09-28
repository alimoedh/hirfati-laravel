<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanProfile;
use App\Services\{ActivityService, FileUploadService};
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $profile = CraftsmanProfile::with('category')->where('user_id', $user->id)->first();

        if (!$profile) {
            $profile = CraftsmanProfile::create([
                'user_id'     => $user->id,
                'category_id' => 1,
                'is_approved' => false,
            ]);
            $profile->load('category');
        }

        return view('craftsman.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'experience_years'  => 'nullable|integer|min:0|max:50',
            'bio'               => 'nullable|string|max:1000',
            'hourly_rate'       => 'nullable|numeric|min:0',
            'is_emergency'      => 'nullable|boolean',
            'emergency_phone'   => 'nullable|string|max:20',
            'avatar'            => 'nullable|image|max:5120',
        ]);

        CraftsmanProfile::where('user_id', $user->id)->update([
            'experience_years' => $data['experience_years'] ?? 0,
            'bio'              => $data['bio'] ?? '',
            'hourly_rate'      => $data['hourly_rate'] ?? 0,
            'is_emergency'     => $request->boolean('is_emergency'),
            'emergency_phone'  => $data['emergency_phone'] ?? '',
        ]);

        if ($request->hasFile('avatar')) {
            $avatar = FileUploadService::upload($request->file('avatar'), 'avatars');
            if ($avatar) $user->update(['avatar' => $avatar]);
        }

        ActivityService::log($user->id, 'update_craftsman_profile', 'تحديث ملف الحرفي');

        return back()->with('success', '✅ تم تحديث الملف الشخصي بنجاح!');
    }
}
