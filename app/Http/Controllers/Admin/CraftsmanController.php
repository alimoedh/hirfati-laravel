<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\{ActivityService, NotificationService};

class CraftsmanController extends Controller
{
    public function index()
    {
        $allCraftsmen = User::with(['craftsmanProfile.category'])
            ->where('role', 'craftsman')
            ->whereHas('craftsmanProfile')
            ->latest()
            ->get();

        $pending  = $allCraftsmen->filter(fn($c) => !$c->craftsmanProfile->is_approved)->values();
        $approved = $allCraftsmen->filter(fn($c) => $c->craftsmanProfile->is_approved)->values();

        return view('admin.craftsmen', [
            'pending'        => $pending,
            'approved'       => $approved,
            'total_pending'  => $pending->count(),
            'total_approved' => $approved->count(),
            'total_all'      => $allCraftsmen->count(),
        ]);
    }

    public function approve(User $user)
    {
        if (!$user->craftsmanProfile) {
            return back()->with('error', 'لا يوجد ملف حرفي');
        }

        $user->craftsmanProfile->update(['is_approved' => true]);
        $user->update(['is_verified' => true]);

        NotificationService::send($user->id, '✅ تم اعتماد حسابك', 'تم اعتماد حسابك كحرفي في منصة حرفتي', url('/craftsman/dashboard'), 'success');

        ActivityService::log(auth()->id(), 'approve_craftsman', "اعتماد حرفي ID: {$user->id}");
        return back()->with('success', '✅ تم اعتماد الحرفي بنجاح');
    }

    public function unapprove(User $user)
    {
        if (!$user->craftsmanProfile) return back()->with('error', 'لا يوجد ملف حرفي');

        $user->craftsmanProfile->update(['is_approved' => false]);
        $user->update(['is_verified' => false]);

        NotificationService::send($user->id, '❌ تم إلغاء اعتماد حسابك', 'تم إلغاء اعتماد حسابك كحرفي في منصة حرفتي', url('/auth/login'), 'danger');

        ActivityService::log(auth()->id(), 'unapprove_craftsman', "إلغاء اعتماد حرفي ID: {$user->id}");
        return back()->with('success', '❌ تم إلغاء اعتماد الحرفي');
    }

    public function reject(User $user)
    {
        $id = $user->id;
        $user->craftsmanProfile?->delete();
        $user->delete();

        ActivityService::log(auth()->id(), 'reject_craftsman', "رفض حرفي ID: {$id}");
        return back()->with('success', '❌ تم رفض الحرفي');
    }
}
