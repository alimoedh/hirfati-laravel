<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount([
                'clientRequests as client_requests',
                'craftsmanRequests as craftsman_requests',
            ])
            ->latest()
            ->get();

        $stats = [
            'total'     => $users->count(),
            'admins'    => $users->where('role', 'admin')->count(),
            'clients'   => $users->where('role', 'client')->count(),
            'craftsmen' => $users->where('role', 'craftsman')->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', '❌ لا يمكن حذف حساب المدير');
        }

        $id = $user->id;
        $user->delete();
        ActivityService::log(auth()->id(), 'delete_user', "حذف مستخدم ID: {$id}");

        return back()->with('success', '✅ تم حذف المستخدم بنجاح');
    }
}
