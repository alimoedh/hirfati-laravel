<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AutoReplyService;
use Illuminate\Http\Request;

class AutoReplyController extends Controller
{
    public function reply(Request $request, AutoReplyService $service)
    {
        $request->validate(['message' => 'required|string|max:500']);
        $reply = $service->getReply($request->input('message'));
        return response()->json(['reply' => $reply]);
    }
}
