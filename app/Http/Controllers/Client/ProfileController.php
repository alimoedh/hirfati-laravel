<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\{ActivityService, FileUploadService};
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(?int $id = null)
    {
        $user = auth()->user();

        if ($id && $id !== $user->id && $user->isAdmin()) {
            $viewUser = User::find($id);
            if ($viewUser) $user = $viewUser;
        }

        return view('client.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email,' . $user->id,
            'phone'     => 'required|string|max:20|unique:users,phone,' . $user->id,
            'avatar'    => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = FileUploadService::upload($request->file('avatar'), 'avatars');
            if ($avatarPath) $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        ActivityService::log($user->id, 'update_profile', 'تحديث الملف الشخصي');

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح!');
    }
}
