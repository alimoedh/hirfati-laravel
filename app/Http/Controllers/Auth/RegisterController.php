<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, CraftsmanProfile, Category};
use App\Services\{ActivityService, NotificationService, FileUploadService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) return redirect('/');
        $categories = Category::active()->get();
        return view('auth.register', compact('categories'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email',
            'phone'     => 'required|string|max:20|unique:users,phone',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:client,craftsman',
            'category_id'       => 'nullable|required_if:role,craftsman|exists:categories,id',
            'identity_document' => 'nullable|required_if:role,craftsman|image|max:5120',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
        ]);

        if ($request->role === 'craftsman' && $request->hasFile('identity_document')) {
            $identityPath = FileUploadService::upload($request->file('identity_document'), 'identities');

            CraftsmanProfile::create([
                'user_id'           => $user->id,
                'category_id'       => $request->category_id,
                'identity_document' => $identityPath,
                'is_approved'       => false,
                'is_available'      => true,
            ]);
        }

        NotificationService::send($user->id, '🎉 مرحباً بك', 'تم إنشاء حسابك بنجاح في منصة حرفتي', null, 'success');

        ActivityService::log($user->id, 'register', 'إنشاء حساب جديد');

        Auth::login($user);

        return redirect($this->dashboardUrl($user->role));
    }

    private function dashboardUrl(string $role): string
    {
        return match($role) {
            'craftsman' => route('craftsman.dashboard'),
            default     => route('client.dashboard'),
        };
    }
}
