<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Services\ActivityService;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['client', 'craftsman', 'request'])
            ->orderByRaw("FIELD(status, 'pending', 'reviewing', 'resolved', 'rejected')")
            ->latest()
            ->get();

        $stats = [
            'total'     => $complaints->count(),
            'pending'   => $complaints->where('status', 'pending')->count(),
            'reviewing' => $complaints->where('status', 'reviewing')->count(),
            'resolved'  => $complaints->where('status', 'resolved')->count(),
            'rejected'  => $complaints->where('status', 'rejected')->count(),
            'today'     => $complaints->filter(fn($c) => $c->created_at->isToday())->count(),
        ];

        return view('admin.complaints', compact('complaints', 'stats'));
    }

    public function action(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'action'         => 'required|in:review,resolve,reject',
            'admin_response' => 'nullable|string|max:1000',
        ]);

        $response = $data['admin_response'] ?? null;

        switch ($data['action']) {
            case 'review':
                $complaint->update(['status' => 'reviewing']);
                $msg = '📋 تم بدء مراجعة الشكوى';
                $log = 'review_complaint';
                break;
            case 'resolve':
                $complaint->update([
                    'status'         => 'resolved',
                    'resolved_at'    => now(),
                    'admin_response' => $response ?? 'تم حل الشكوى',
                ]);
                $msg = '✅ تم حل الشكوى بنجاح';
                $log = 'resolve_complaint';
                break;
            case 'reject':
                $complaint->update([
                    'status'         => 'rejected',
                    'admin_response' => $response ?? 'تم رفض الشكوى',
                ]);
                $msg = '❌ تم رفض الشكوى';
                $log = 'reject_complaint';
                break;
        }

        ActivityService::log(auth()->id(), $log, "الشكوى #{$complaint->id}");

        return back()->with('success', $msg);
    }
}
