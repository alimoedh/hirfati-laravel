<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('client.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'كلمة المرور الحالية غير صحيحة');
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        ActivityService::log($user->id, 'change_password', 'تغيير كلمة المرور');

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}
