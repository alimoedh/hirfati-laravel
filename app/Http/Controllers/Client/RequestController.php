<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Request, Category, User};
use App\Services\{
    ActivityService, NotificationService,
    AiEstimateService, FileUploadService, InstallmentService
};
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function create()
    {
        $categories = Category::active()->get();
        return view('client.request-form', compact('categories'));
    }

    public function store(HttpRequest $request)
    {
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'craftsman_id'      => 'nullable|exists:users,id',
            'title'             => 'required|string|max:200',
            'description'       => 'required|string',
            'address'           => 'required|string|max:255',
            'preferred_date'    => 'required|date',
            'preferred_time'    => 'nullable',
            'budget'            => 'nullable|numeric|min:0',
            'is_emergency'      => 'nullable|boolean',
            'use_installment'   => 'nullable|boolean',
            'installment_count' => 'nullable|integer|in:3,6,12',
            'problem_image'     => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();
        $problemImage = null;

        if ($request->hasFile('problem_image')) {
            $problemImage = FileUploadService::upload($request->file('problem_image'), 'requests');
        }

        $requestId = DB::transaction(function () use ($data, $user, $problemImage, $request) {
            $req = Request::create([
                'client_id'         => $user->id,
                'craftsman_id'      => $data['craftsman_id'] ?? null,
                'category_id'       => $data['category_id'],
                'title'             => $data['title'],
                'description'       => $data['description'],
                'address'           => $data['address'],
                'preferred_date'    => $data['preferred_date'],
                'preferred_time'    => $data['preferred_time'] ?? null,
                'budget'            => $data['budget'] ?? 0,
                'problem_image'     => $problemImage,
                'status'            => 'pending',
                'is_emergency'      => $request->boolean('is_emergency'),
                'use_installment'   => $request->boolean('use_installment'),
                'installment_count' => $request->boolean('use_installment')
                    ? (int) ($data['installment_count'] ?? 3) : 0,
            ]);

            if ($req->use_installment && $req->installment_count > 0) {
                $totalAmount = (float) ($req->budget > 0 ? $req->budget : 5000);
                InstallmentService::createSchedule($req->id, $totalAmount, $req->installment_count);

                NotificationService::send(
                    $user->id,
                    '💳 تم إنشاء جدول الأقساط',
                    "تم إنشاء {$req->installment_count} أقساط للطلب #" . str_pad($req->id, 4, '0', STR_PAD_LEFT),
                    url("/client/installments?request_id={$req->id}"),
                    'info'
                );
            }

            return $req->id;
        });

        NotificationService::send(
            $user->id,
            '✅ تم إنشاء الطلب',
            "تم إرسال طلبك: {$data['title']}",
            url("/client/request-details/{$requestId}"),
            'success'
        );

        if (!empty($data['craftsman_id'])) {
            NotificationService::send(
                $data['craftsman_id'],
                '📩 طلب جديد مخصص لك',
                "طلب جديد من {$user->full_name}: {$data['title']}",
                url("/craftsman/dashboard"),
                'info'
            );
        } else {
            $craftsmenIds = User::where('role', 'craftsman')
                ->whereHas('craftsmanProfile', fn($q) => $q->where('category_id', $data['category_id']))
                ->pluck('id')->toArray();

            NotificationService::sendMany(
                $craftsmenIds,
                '📩 طلب خدمة جديد',
                "طلب جديد: {$data['title']} من {$user->full_name}",
                url("/craftsman/dashboard"),
                'info'
            );
        }

        ActivityService::log($user->id, 'create_request', "إنشاء طلب #{$requestId}");

        return redirect()->route('client.dashboard')->with('success', '✅ تم إرسال الطلب بنجاح!');
    }

    public function show(int $id)
    {
        $request = Request::with([
            'client', 'craftsman', 'category',
            'installments', 'review', 'complaint', 'warranty'
        ])->findOrFail($id);

        $user = auth()->user();

        $canView = $user->isAdmin()
            || ($user->isClient() && $request->client_id === $user->id)
            || ($user->isCraftsman() && $request->craftsman_id === $user->id);

        abort_unless($canView, 403);

        $chatMessages = \App\Models\Message::with('sender:id,full_name')
            ->where('request_id', $request->id)
            ->orderBy('created_at')
            ->get();

        return view('client.request-details', compact('request', 'chatMessages', 'user'));
    }

    public function cancel(int $id)
    {
        $request = Request::findOrFail($id);
        $user = auth()->user();

        abort_unless($request->client_id === $user->id, 403);

        if (!in_array($request->status, ['pending', 'accepted'])) {
            return back()->with('error', 'لا يمكن إلغاء الطلب في هذه المرحلة');
        }

        $request->update(['status' => 'cancelled']);

        if ($request->craftsman_id) {
            NotificationService::send(
                $request->craftsman_id,
                '❌ تم إلغاء الطلب',
                "تم إلغاء الطلب #" . str_pad($request->id, 4, '0', STR_PAD_LEFT) . " من قبل العميل",
                url("/craftsman/dashboard"),
                'danger'
            );
        }

        ActivityService::log($user->id, 'cancel_request', "إلغاء الطلب #{$id}");

        return redirect()->route('client.dashboard')->with('success', 'تم إلغاء الطلب');
    }

    public function aiEstimate(HttpRequest $request, AiEstimateService $service)
    {
        $data = $request->validate([
            'category_id'  => 'required|integer|exists:categories,id',
            'description'  => 'required|string|min:5',
            'is_emergency' => 'nullable|boolean',
        ]);

        $estimate = $service->estimate(
            $data['category_id'],
            $data['description'],
            (bool) ($data['is_emergency'] ?? false)
        );

        return response()->json(['success' => true, 'data' => $estimate]);
    }

    public function craftsmenByCategory(HttpRequest $request)
    {
        $categoryId = (int) $request->input('category_id');
        if (!$categoryId) return response()->json([]);

        $craftsmen = User::query()
            ->select('users.id', 'users.full_name')
            ->join('craftsman_profiles', 'users.id', '=', 'craftsman_profiles.user_id')
            ->where('users.role', 'craftsman')
            ->where('users.is_active', true)
            ->where('craftsman_profiles.category_id', $categoryId)
            ->where('craftsman_profiles.is_approved', true)
            ->get();

        return response()->json($craftsmen);
    }
        public function myRequests()
    {
        $user = auth()->user();

        $requests = \App\Models\Request::with(['craftsman.craftsmanProfile', 'category'])
            ->where('client_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'       => $requests->count(),
            'pending'     => $requests->where('status', 'pending')->count(),
            'completed'   => $requests->where('status', 'completed')->count(),
            'in_progress' => $requests->where('status', 'in_progress')->count(),
            'cancelled'   => $requests->where('status', 'cancelled')->count(),
        ];

        return view('client.requests', compact('requests', 'stats'));
    }

}
