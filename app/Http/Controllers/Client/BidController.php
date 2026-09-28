<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Services\BidService;

class BidController extends Controller
{
    public function accept(Bid $bid)
    {
        try {
            BidService::accept($bid);
            return back()->with('success', '✅ تم قبول العرض — يمكنك الدفع الآن');
        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }

    public function reject(Bid $bid)
    {
        try {
            BidService::reject($bid);
            return back()->with('success', '✅ تم رفض العرض');
        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }
}
