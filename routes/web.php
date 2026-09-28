<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController,
    ResetPasswordController,
    TwoFactorController,
    VerifyController
};

// ============================================
// الصفحة الرئيسية
// ============================================
Route::get('/', function () {
    $featuredCraftsmen = \App\Models\User::with(['craftsmanProfile.category'])
        ->where('role', 'craftsman')
        ->where('is_active', true)
        ->whereHas('craftsmanProfile', fn($q) => $q->where('is_approved', true))
        ->limit(6)
        ->get();

    $categories = \App\Models\Category::active()->get();
    $totalCraftsmen = \App\Models\User::where('role', 'craftsman')->where('is_active', true)->count();
    $totalRequests  = \App\Models\Request::where('status', 'completed')->count();
    $totalClients   = \App\Models\User::where('role', 'client')->where('is_active', true)->count();

    return view('welcome', compact(
        'featuredCraftsmen', 'categories',
        'totalCraftsmen', 'totalRequests', 'totalClients'
    ));
})->name('home');
Route::get('/profile/{id}', [\App\Http\Controllers\CraftsmanPublicController::class, 'show'])
    ->where('id', '[0-9]+.*')
    ->name('craftsman.public');


// ============================================
// المصادقة
// ============================================
Route::prefix('auth')->name('auth.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);

        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);

        Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/2fa', [TwoFactorController::class, 'show'])->name('2fa');
        Route::post('/2fa', [TwoFactorController::class, 'verify']);

        Route::middleware('craftsman')->group(function () {
            Route::get('/verify', [VerifyController::class, 'show'])->name('verify');
            Route::post('/verify/send', [VerifyController::class, 'send'])->name('verify.send');
            Route::post('/verify', [VerifyController::class, 'verify']);
        });
    });
});

// ============================================
// Admin
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('craftsmen')->name('craftsmen.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CraftsmanController::class, 'index'])->name('index');
        Route::post('/{user}/approve', [\App\Http\Controllers\Admin\CraftsmanController::class, 'approve'])->name('approve');
        Route::post('/{user}/unapprove', [\App\Http\Controllers\Admin\CraftsmanController::class, 'unapprove'])->name('unapprove');
        Route::post('/{user}/reject', [\App\Http\Controllers\Admin\CraftsmanController::class, 'reject'])->name('reject');
    });

    Route::prefix('complaints')->name('complaints.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('index');
        Route::post('/{complaint}/action', [\App\Http\Controllers\Admin\ComplaintController::class, 'action'])->name('action');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
    });

    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::post('/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
        Route::get('/download/{file}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
    });
        Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::post('/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
        Route::get('/download/{file}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
        
        // ✅ جديد:
        Route::get('/preview/{file}', [\App\Http\Controllers\Admin\BackupController::class, 'preview'])->name('preview');
        Route::delete('/destroy/{file}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
    });
            Route::get('/invoice/{id}', [\App\Http\Controllers\Client\InvoiceController::class, 'show'])->name('invoice');
        Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/export/users', [\App\Http\Controllers\Admin\ReportController::class, 'exportUsers'])->name('export-users');
        Route::get('/export/requests', [\App\Http\Controllers\Admin\ReportController::class, 'exportRequests'])->name('export-requests');
        Route::get('/export/financial', [\App\Http\Controllers\Admin\ReportController::class, 'exportFinancial'])->name('export-financial');
    });



});

// ============================================
// Client
// ============================================
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {

    // الرئيسية
    Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/requests', [\App\Http\Controllers\Client\RequestController::class, 'myRequests'])->name('requests');

    // الملف الشخصي
    Route::get('/profile/{id?}', [\App\Http\Controllers\Client\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');

    // تغيير كلمة المرور
    Route::get('/change-password', [\App\Http\Controllers\Client\ChangePasswordController::class, 'show'])->name('change-password');
    Route::post('/change-password', [\App\Http\Controllers\Client\ChangePasswordController::class, 'update'])->name('change-password.update');

    // نموذج الطلب
    Route::get('/request-form', [\App\Http\Controllers\Client\RequestController::class, 'create'])->name('request-form');
    Route::post('/request-form', [\App\Http\Controllers\Client\RequestController::class, 'store'])->name('request-form.store');

    // تفاصيل الطلب
    Route::get('/request-details/{id}', [\App\Http\Controllers\Client\RequestController::class, 'show'])->name('request-details');
    Route::post('/request/{id}/cancel', [\App\Http\Controllers\Client\RequestController::class, 'cancel'])->name('request.cancel');

    // API (داخلية)
    Route::post('/api/ai-estimate', [\App\Http\Controllers\Client\RequestController::class, 'aiEstimate'])->name('request-form.ai-estimate');
    Route::get('/api/craftsmen', [\App\Http\Controllers\Client\RequestController::class, 'craftsmenByCategory'])->name('api.craftsmen-by-category');

    // البحث
    Route::get('/search-results', [\App\Http\Controllers\Client\SearchController::class, 'index'])->name('search');

    // الحجز
    Route::get('/booking/{id}', [\App\Http\Controllers\Client\BookingController::class, 'index'])->name('booking');

    // الدفع
    Route::get('/checkout/{id}', [\App\Http\Controllers\Client\CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/{id}', [\App\Http\Controllers\Client\CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/payment-success/{id}', function ($id, \Illuminate\Http\Request $r) {
        $request = \App\Models\Request::findOrFail($id);
        $txn = $r->input('txn', '');
        return view('client.payment-success', compact('request', 'txn'));
    })->name('payment-success');

    Route::get('/payment-cancel', function () {
        return view('client.payment-cancel');
    })->name('payment-cancel');

    // Chat & Messages
    Route::get('/messages', [\App\Http\Controllers\Client\ChatController::class, 'conversations'])->name('messages');
    Route::get('/chat/{request_id}', [\App\Http\Controllers\Client\ChatController::class, 'index'])->name('chat');
    Route::post('/chat/send', [\App\Http\Controllers\Client\ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/send-voice', [\App\Http\Controllers\Client\ChatController::class, 'sendVoice'])->name('chat.send-voice');
    Route::get('/chat/{request_id}/fetch', [\App\Http\Controllers\Client\ChatController::class, 'fetch'])->name('chat.fetch');

    // Installments
    Route::get('/installments', [\App\Http\Controllers\Client\InstallmentController::class, 'index'])->name('installments.index');
    Route::post('/installments/{installment}/pay', [\App\Http\Controllers\Client\InstallmentController::class, 'pay'])->name('installments.pay');
    Route::post('/installments/pay-all', [\App\Http\Controllers\Client\InstallmentController::class, 'payAll'])->name('installments.pay-all');

    // Rate
    Route::get('/rate-craftsman/{request_id}', [\App\Http\Controllers\Client\RatingController::class, 'show'])->name('rate-craftsman');
    Route::post('/rate-craftsman/{request_id}', [\App\Http\Controllers\Client\RatingController::class, 'store'])->name('rate-craftsman.store');

    // Complaint
    Route::get('/complaint-form', [\App\Http\Controllers\Client\ComplaintController::class, 'create'])->name('complaint-form');
    Route::post('/complaint-form', [\App\Http\Controllers\Client\ComplaintController::class, 'store'])->name('complaint.store');

    // Live Stream
    Route::get('/live-stream/{request_id?}', [\App\Http\Controllers\Client\LiveStreamController::class, 'show'])->name('live-stream.show');
    Route::get('/live-stream', [\App\Http\Controllers\Client\LiveStreamController::class, 'show'])->name('live-stream.index');

    // AR View
    Route::get('/ar-view', [\App\Http\Controllers\Client\ArViewController::class, 'index'])->name('ar-view');

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\Client\AnalyticsController::class, 'index'])->name('analytics');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Client\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\Client\ReportController::class, 'export'])->name('reports.export');

    // Tutorials
    Route::get('/tutorials', [\App\Http\Controllers\Client\TutorialController::class, 'index'])->name('tutorials');
           Route::get('/invoice/{id}', [\App\Http\Controllers\Client\InvoiceController::class, 'show'])->name('invoice');
               // العروض (Bidding)
    Route::post('/bids/{bid}/accept', [\App\Http\Controllers\Client\BidController::class, 'accept'])->name('bids.accept');
    Route::post('/bids/{bid}/reject', [\App\Http\Controllers\Client\BidController::class, 'reject'])->name('bids.reject');

});

// ============================================
// Craftsman
// ============================================
Route::middleware(['auth', 'craftsman'])->prefix('craftsman')->name('craftsman.')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Craftsman\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/action', [\App\Http\Controllers\Craftsman\DashboardController::class, 'action'])->name('action');

    Route::get('/requests', [\App\Http\Controllers\Craftsman\RequestController::class, 'index'])->name('requests');

    Route::get('/portfolio', [\App\Http\Controllers\Craftsman\PortfolioController::class, 'index'])->name('portfolio');

    Route::get('/ratings', [\App\Http\Controllers\Craftsman\RatingController::class, 'index'])->name('ratings');

    Route::get('/commissions', [\App\Http\Controllers\Craftsman\CommissionController::class, 'index'])->name('commissions');
    Route::post('/commissions/withdraw', [\App\Http\Controllers\Craftsman\CommissionController::class, 'withdraw'])->name('commissions.withdraw');

    Route::get('/profile', [\App\Http\Controllers\Craftsman\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Craftsman\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/complaints', [\App\Http\Controllers\Craftsman\ComplaintController::class, 'index'])->name('complaints');
        // Messages & Chat (للمحادثة مع العملاء)
    Route::get('/messages', [\App\Http\Controllers\Client\ChatController::class, 'conversations'])->name('messages');
    Route::get('/chat/{request_id}', [\App\Http\Controllers\Client\ChatController::class, 'index'])->name('chat');
    Route::post('/chat/send', [\App\Http\Controllers\Client\ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/send-voice', [\App\Http\Controllers\Client\ChatController::class, 'sendVoice'])->name('chat.send-voice');
    Route::get('/chat/{request_id}/fetch', [\App\Http\Controllers\Client\ChatController::class, 'fetch'])->name('chat.fetch');

    // Live Stream (للحرفي أيضاً)
    Route::get('/live-stream/{request_id?}', [\App\Http\Controllers\Client\LiveStreamController::class, 'show'])->name('live-stream.show');
        Route::get('/analytics', [\App\Http\Controllers\Client\AnalyticsController::class, 'index'])->name('analytics');
           Route::get('/invoice/{id}', [\App\Http\Controllers\Client\InvoiceController::class, 'show'])->name('invoice');
               // العروض (Bidding)
    Route::post('/bids/{request_id}', [\App\Http\Controllers\Craftsman\BidController::class, 'store'])->name('bids.store');
    Route::put('/bids/{bid}', [\App\Http\Controllers\Craftsman\BidController::class, 'update'])->name('bids.update');
    Route::post('/bids/{bid}/withdraw', [\App\Http\Controllers\Craftsman\BidController::class, 'withdraw'])->name('bids.withdraw');
    // صور العمل (Before/After)
    Route::post('/requests/{request_id}/work-photos', [\App\Http\Controllers\Craftsman\WorkPhotoController::class, 'store'])->name('work-photos.store');
    Route::delete('/work-photos/{photo}', [\App\Http\Controllers\Craftsman\WorkPhotoController::class, 'destroy'])->name('work-photos.destroy');


});
