<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\{Bid, Request};
use App\Services\BidService;
use Illuminate\Http\Request as HttpRequest;

class BidController extends Controller
{
    public function store(HttpRequest $httpRequest, int $requestId)
    {
        $data = $httpRequest->validate([
            'amount'        => 'required|numeric|min:1|max:999999',
            'duration_days' => 'required|integer|min:1|max:365',
            'message'       => 'nullable|string|max:1000',
        ]);

        $request = Request::findOrFail($requestId);

        try {
            BidService::submit($request, auth()->id(), $data);
            return back()->with('success', '✅ تم إرسال عرضك بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }

    public function update(HttpRequest $httpRequest, Bid $bid)
    {
        $data = $httpRequest->validate([
            'amount'        => 'required|numeric|min:1|max:999999',
            'duration_days' => 'required|integer|min:1|max:365',
            'message'       => 'nullable|string|max:1000',
        ]);

        try {
            BidService::update($bid, $data);
            return back()->with('success', '✅ تم تعديل عرضك');
        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }

    public function withdraw(Bid $bid)
    {
        try {
            BidService::withdraw($bid);
            return back()->with('success', '✅ تم سحب عرضك');
        } catch (\Exception $e) {
            return back()->with('error', '❌ ' . $e->getMessage());
        }
    }
}
