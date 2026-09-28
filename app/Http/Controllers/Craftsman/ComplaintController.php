<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $complaints = Complaint::with(['client', 'request'])
            ->where('craftsman_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('craftsman.complaints', compact('complaints'));
    }
}
