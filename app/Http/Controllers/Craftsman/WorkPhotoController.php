<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\{Request, WorkPhoto};
use App\Services\{FileUploadService, ActivityService, NotificationService};
use Illuminate\Http\Request as HttpRequest;

class WorkPhotoController extends Controller
{
    public function store(HttpRequest $httpRequest, int $requestId)
    {
        $data = $httpRequest->validate([
            'type'          => 'required|in:before,after',
            'photos'        => 'required|array|min:1|max:5',
            'photos.*'      => 'image|max:5120',
            'description'   => 'nullable|string|max:500',
        ]);

        $request = Request::findOrFail($requestId);

        // التحقق: الحرفي هو صاحب الطلب
        abort_unless($request->craftsman_id === auth()->id(), 403);

        // التحقق: الطلب قيد التنفيذ
        abort_unless(
            in_array($request->status, ['in_progress', 'accepted']),
            400,
            'لا يمكن رفع صور في هذه المرحلة'
        );

        $uploaded = 0;
        foreach ($httpRequest->file('photos') as $photo) {
            $path = FileUploadService::upload($photo, 'work-photos');
            if ($path) {
                WorkPhoto::create([
                    'request_id'  => $request->id,
                    'type'        => $data['type'],
                    'image_path'  => $path,
                    'description' => $data['description'] ?? null,
                    'uploaded_by' => auth()->id(),
                ]);
                $uploaded++;
            }
        }

        if ($uploaded === 0) {
            return back()->with('error', '❌ فشل رفع الصور');
        }

        // إشعار العميل
        $typeLabel = $data['type'] === 'before' ? 'صور قبل التنفيذ' : 'صور بعد التنفيذ';
        NotificationService::send(
            $request->client_id,
            '📸 ' . $typeLabel,
            "رفع " . auth()->user()->full_name . " {$uploaded} صورة للطلب #" . str_pad($request->id, 4, '0', STR_PAD_LEFT),
            url("/client/request-details/{$request->id}"),
            'info'
        );

        ActivityService::log(auth()->id(), 'upload_work_photos', "رفع {$uploaded} صورة ({$data['type']}) للطلب #{$request->id}");

        return back()->with('success', "✅ تم رفع {$uploaded} صورة بنجاح");
    }

    public function destroy(WorkPhoto $photo)
    {
        abort_unless($photo->uploaded_by === auth()->id(), 403);

        \Storage::disk('public')->delete($photo->image_path);
        $photo->delete();

        ActivityService::log(auth()->id(), 'delete_work_photo', "حذف صورة من الطلب #{$photo->request_id}");

        return back()->with('success', '✅ تم حذف الصورة');
    }
}
