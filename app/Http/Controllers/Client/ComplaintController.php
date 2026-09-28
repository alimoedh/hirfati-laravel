<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Services\{ActivityService, FileUploadService};
use Illuminate\Http\Request as HttpRequest;

class ComplaintController extends Controller
{
    public function create(HttpRequest $request)
    {
        $user = auth()->user();
        $requestId = (int) $request->input('request_id', 0);
        $isCraftsman = $request->boolean('craftsman');

        $serviceRequest = Request::with(['client', 'craftsman'])->findOrFail($requestId);

        $canComplain = ($user->isClient() && $serviceRequest->client_id === $user->id)
                    || ($user->isCraftsman() && $serviceRequest->craftsman_id === $user->id);
        abort_unless($canComplain, 403);

        if ($isCraftsman && $user->isCraftsman()) {
            $targetName = $serviceRequest->client->full_name ?? 'العميل';
            $backUrl = route('craftsman.dashboard');
        } else {
            $targetName = $serviceRequest->craftsman->full_name ?? 'الحرفي';
            $backUrl = route('client.dashboard');
        }

        return view('client.complaint-form', compact('serviceRequest', 'targetName', 'isCraftsman', 'backUrl'));
    }

    public function store(HttpRequest $httpRequest)
    {
        $data = $httpRequest->validate([
            'request_id'     => 'required|integer|exists:requests,id',
            'reason'         => 'required|in:quality,delay,price,behavior,other',
            'details'        => 'required|string|max:2000',
            'evidence_image' => 'nullable|image|max:5120',
            'is_craftsman'   => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $serviceRequest = Request::findOrFail($data['request_id']);
        $isCraftsman = $httpRequest->boolean('is_craftsman');

        $evidenceImage = null;
        if ($httpRequest->hasFile('evidence_image')) {
            $evidenceImage = FileUploadService::upload($httpRequest->file('evidence_image'), 'complaints');
        }

        $clientId    = $isCraftsman ? $serviceRequest->client_id : $user->id;
        $craftsmanId = $isCraftsman ? $user->id : $serviceRequest->craftsman_id;

        \App\Models\Complaint::create([
            'request_id'     => $serviceRequest->id,
            'client_id'      => $clientId,
            'craftsman_id'   => $craftsmanId,
            'reason'         => $data['reason'],
            'details'        => $data['details'],
            'evidence_image' => $evidenceImage,
            'status'         => 'pending',
        ]);

        ActivityService::log($user->id, 'create_complaint', "تقديم شكوى ضد الطلب #{$serviceRequest->id}");

        return redirect($isCraftsman ? route('craftsman.dashboard') : route('client.dashboard'))
            ->with('success', '✅ تم إرسال الشكوى بنجاح!');
    }
}
