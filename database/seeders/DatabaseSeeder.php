<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Category, Setting, User, CraftsmanProfile, CraftsmanWallet,
    Request, Message, Review, Complaint, Notification, Installment,
    WalletTransaction, Tutorial, Reward
};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 بدء إضافة البيانات الوهمية...');
        $this->command->newLine();

        $this->seedCategories();
        $this->seedSettings();
        $this->seedUsers();
        $this->seedCraftsmenProfiles();
        $this->seedWallets();
        $this->seedRequests();
        $this->seedMessages();
        $this->seedReviews();
        $this->seedComplaints();
        $this->seedNotifications();
        $this->seedInstallments();
        $this->seedTutorials();
        $this->seedRewards();

        $this->command->newLine();
        $this->command->info('✅ اكتملت البيانات الوهمية!');
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👑 Admin:     admin@hirfati.com     / admin123');
        $this->command->info('👤 Client:    client@hirfati.com    / client123');
        $this->command->info('🔧 Craftsman: craftsman@hirfati.com / craftsman123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('🔑 كل الحرفيين الآخرين كلمة سر: craftsman123');
        $this->command->info('🔑 كل العملاء الآخرين كلمة سر: client123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    // ==========================================
    // 1) التصنيفات
    // ==========================================
    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'سباكة',  'icon' => 'fa-wrench'],
            ['name' => 'كهرباء', 'icon' => 'fa-bolt'],
            ['name' => 'نجارة',  'icon' => 'fa-hammer'],
            ['name' => 'دهانات', 'icon' => 'fa-paintbrush'],
            ['name' => 'تكييف',  'icon' => 'fa-snowflake'],
            ['name' => 'حدادة',  'icon' => 'fa-cogs'],
        ];
        foreach ($categories as $c) {
            Category::updateOrCreate(['name' => $c['name']], $c);
        }
        $this->command->line('   ✓ 6 تصنيفات');
    }

    // ==========================================
    // 2) الإعدادات
    // ==========================================
    private function seedSettings(): void
    {
        $settings = [
            'site_name'                 => 'حرفتي',
            'site_description'          => 'منصة تجمع الحرفيين والعملاء',
            'commission_percentage'     => '5',
            'min_withdrawal'            => '1000',
            'contact_email'             => 'info@hirfati.com',
            'contact_phone'             => '0770000000',
            'emergency_enabled'         => 'true',
            'installment_enabled'       => 'true',
            'warranty_days'             => '30',
            'loyalty_points_per_order'  => '10',
            'loyalty_points_per_review' => '5',
        ];
        foreach ($settings as $k => $v) {
            Setting::updateOrCreate(['setting_key' => $k], ['setting_value' => $v]);
        }
        $this->command->line('   ✓ 11 إعداد');
    }

    // ==========================================
    // 3) المستخدمون
    // ==========================================
    private function seedUsers(): void
    {
        // ─────── Admin ───────
        User::updateOrCreate(
            ['email' => 'admin@hirfati.com'],
            [
                'full_name'   => 'مدير المنصة',
                'phone'       => '0770000000',
                'password'    => Hash::make('admin123'),
                'role'        => 'admin',
                'is_active'   => true,
                'is_verified' => true,
            ]
        );

        // ─────── العملاء (10) ───────
        $clients = [
            ['name' => 'أحمد محمد',      'email' => 'client@hirfati.com',  'phone' => '0771111111', 'points' => 50],
            ['name' => 'فاطمة علي',      'email' => 'fatima@hirfati.com',   'phone' => '0771111112', 'points' => 35],
            ['name' => 'محمد سعيد',      'email' => 'mohammed@hirfati.com', 'phone' => '0771111113', 'points' => 80],
            ['name' => 'سارة إبراهيم',   'email' => 'sara@hirfati.com',     'phone' => '0771111114', 'points' => 25],
            ['name' => 'خالد العمري',    'email' => 'khaled@hirfati.com',   'phone' => '0771111115', 'points' => 100],
            ['name' => 'نورا أحمد',      'email' => 'noura@hirfati.com',    'phone' => '0771111116', 'points' => 15],
            ['name' => 'يوسف الحسن',     'email' => 'yousef@hirfati.com',   'phone' => '0771111117', 'points' => 60],
            ['name' => 'ريم عبدالله',    'email' => 'reem@hirfati.com',     'phone' => '0771111118', 'points' => 40],
            ['name' => 'عمر الشامي',     'email' => 'omar@hirfati.com',     'phone' => '0771111119', 'points' => 20],
            ['name' => 'هدى المقطري',    'email' => 'huda@hirfati.com',     'phone' => '0771111120', 'points' => 75],
        ];
        foreach ($clients as $c) {
            User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'full_name'           => $c['name'],
                    'phone'               => $c['phone'],
                    'password'            => Hash::make('client123'),
                    'role'                => 'client',
                    'is_active'           => true,
                    'is_verified'         => true,
                    'loyalty_points'      => $c['points'],
                    'total_points_earned' => $c['points'],
                ]
            );
        }

        // ─────── الحرفيون (12) ───────
        $craftsmen = [
            ['name' => 'حرفي تجريبي',         'email' => 'craftsman@hirfati.com', 'phone' => '0772222222', 'cat' => 1],
            ['name' => 'علي السباك',           'email' => 'ali@hirfati.com',       'phone' => '0772222223', 'cat' => 1],
            ['name' => 'حسن الكهربائي',        'email' => 'hasan@hirfati.com',     'phone' => '0772222224', 'cat' => 2],
            ['name' => 'محمود الكهربائي',      'email' => 'mahmoud@hirfati.com',   'phone' => '0772222225', 'cat' => 2],
            ['name' => 'سامي النجار',          'email' => 'sami@hirfati.com',      'phone' => '0772222226', 'cat' => 3],
            ['name' => 'عبدالله النجار',       'email' => 'abdullah@hirfati.com',  'phone' => '0772222227', 'cat' => 3],
            ['name' => 'نبيل الدهان',          'email' => 'nabil@hirfati.com',     'phone' => '0772222228', 'cat' => 4],
            ['name' => 'كريم الدهان',          'email' => 'kareem@hirfati.com',    'phone' => '0772222229', 'cat' => 4],
            ['name' => 'طارق التكييف',         'email' => 'tareq@hirfati.com',     'phone' => '0772222230', 'cat' => 5],
            ['name' => 'ماهر التكييف',         'email' => 'maher@hirfati.com',     'phone' => '0772222231', 'cat' => 5],
            ['name' => 'زكريا الحداد',         'email' => 'zakaria@hirfati.com',   'phone' => '0772222232', 'cat' => 6],
            ['name' => 'إبراهيم الحداد',       'email' => 'ibrahim@hirfati.com',   'phone' => '0772222233', 'cat' => 6],
        ];
        foreach ($craftsmen as $i => $c) {
            User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'full_name'   => $c['name'],
                    'phone'       => $c['phone'],
                    'password'    => Hash::make('craftsman123'),
                    'role'        => 'craftsman',
                    'is_active'   => true,
                    'is_verified' => true,
                ]
            );
        }

        $this->command->line('   ✓ 1 مدير + 10 عملاء + 12 حرفي');
    }

    // ==========================================
    // 4) ملفات الحرفيين
    // ==========================================
    private function seedCraftsmenProfiles(): void
    {
        $craftsmen = User::where('role', 'craftsman')->get();

        $bios = [
            'خبرة سنوات في السباكة وإصلاح التسريبات وتركيب الأدوات الصحية',
            'متخصص في الكهرباء المنزلية والصناعية وتركيب الإنارة',
            'نجار محترف في تصنيع الأثاث والأبواب والنوافذ',
            'متخصص في الدهانات والديكورات الحديثة',
            'فني تكييف وتبريد معتمد من الشركات الكبرى',
            'حداد محترف في تصنيع الأبواب والشبابيك والدرابزين',
        ];

        $categoryBios = [
            1 => $bios[0],
            2 => $bios[1],
            3 => $bios[2],
            4 => $bios[3],
            5 => $bios[4],
            6 => $bios[5],
        ];

        $categoryFor = [
            'craftsman@hirfati.com' => 1, 'ali@hirfati.com'       => 1,
            'hasan@hirfati.com'     => 2, 'mahmoud@hirfati.com'   => 2,
            'sami@hirfati.com'      => 3, 'abdullah@hirfati.com'  => 3,
            'nabil@hirfati.com'     => 4, 'kareem@hirfati.com'    => 4,
            'tareq@hirfati.com'     => 5, 'maher@hirfati.com'     => 5,
            'zakaria@hirfati.com'   => 6, 'ibrahim@hirfati.com'   => 6,
        ];

        foreach ($craftsmen as $user) {
            $categoryId = $categoryFor[$user->email] ?? 1;

            CraftsmanProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id'      => $categoryId,
                    'experience_years' => rand(2, 15),
                    'bio'              => $categoryBios[$categoryId],
                    'hourly_rate'      => rand(15, 50) * 100,
                    'is_approved'      => true,
                    'is_available'     => true,
                    'is_emergency'     => rand(0, 1) === 1,
                    'emergency_phone'  => $user->phone,
                    'rating_avg'       => 0, // سيتم تحديثها بعد التقييمات
                    'total_reviews'    => 0,
                ]
            );
        }
        $this->command->line('   ✓ 12 ملف حرفي');
    }

    // ==========================================
    // 5) المحافظ
    // ==========================================
    private function seedWallets(): void
    {
        $craftsmen = User::where('role', 'craftsman')->get();

        foreach ($craftsmen as $c) {
            CraftsmanWallet::updateOrCreate(
                ['craftsman_id' => $c->id],
                [
                    'balance'          => rand(0, 50000),
                    'escrow_balance'   => rand(0, 10000),
                    'total_earned'     => rand(20000, 200000),
                    'total_commission' => rand(1000, 10000),
                    'total_withdrawn'  => rand(0, 50000),
                ]
            );
        }
        $this->command->line('   ✓ 12 محفظة');
    }

    // ==========================================
    // 6) الطلبات (40 طلب)
    // ==========================================
    private function seedRequests(): void
    {
        $existing = Request::count();
        if ($existing > 0) {
            $this->command->line('   ⊘ الطلبات موجودة مسبقاً، تم التخطي');
            return;
        }

        $clients    = User::where('role', 'client')->get();
        $craftsmen  = User::where('role', 'craftsman')->get();
        $categories = Category::all();

        // عناوين وأوصاف جاهزة حسب التصنيف
        $titlesByCat = [
            1 => ['إصلاح تسريب مياه', 'تركيب مغسلة جديدة', 'صيانة سخان ماء', 'تركيب خلاط مطبخ'],
            2 => ['تركيب مصابيح LED', 'إصلاح ماس كهربائي', 'تمديد خط كهرباء لغرفة', 'تركيب مفتاح أمان'],
            3 => ['تصنيع خزانة ملابس', 'تركيب باب خشبي', 'إصلاح كرسي مكسور', 'تصنيع طاولة طعام'],
            4 => ['دهان غرفة نوم', 'دهان صالة كبيرة', 'تجديد دهان مطبخ', 'دهان خارجي للبيت'],
            5 => ['صيانة مكيف سبليت', 'تركيب مكيف جديد', 'تنظيف مكيف مركزي', 'إصلاح تلفزيون'],
            6 => ['تصنيع باب حديد', 'إصلاح بوابة خارجية', 'تصنيع درابزين سلم', 'تركيب شبك نوافذ'],
        ];

        $addresses = [
            'صنعاء - شارع الزبيري', 'صنعاء - شارع تعز', 'صنعاء - حي السنينة',
            'عدن - المنصورة', 'عدن - كريتر', 'تعز - شارع جمال',
            'الحديدة - شارع صنعاء', 'إب - شارع تعز',
        ];

        $statuses = ['pending', 'pending', 'pending', 'accepted', 'in_progress', 'completed', 'completed', 'completed', 'cancelled'];

        for ($i = 1; $i <= 40; $i++) {
            $category = $categories->random();
            $status   = $statuses[array_rand($statuses)];
            $titles   = $titlesByCat[$category->id] ?? ['خدمة عامة'];
            $client   = $clients->random();

            // الحرفي يكون مسند فقط لو الحالة مش pending
            $craftsmanId = null;
            if ($status !== 'pending') {
                $craftsmanId = $craftsmen->where('craftsmanProfile.category_id', $category->id)->first()?->id
                    ?? $craftsmen->random()->id;
            }

            // حالة الطوارئ والتقسيط
            $isEmergency   = rand(1, 10) > 9; // 10%
            $useInstallment = rand(1, 10) > 8; // 20%
            $installmentCount = $useInstallment ? [3, 6, 12][array_rand([3, 6, 12])] : 0;

            $budget = rand(5, 50) * 500;
            if ($isEmergency) $budget += 2000;

            $createdAt = now()->subDays(rand(1, 180));
            $updatedAt = $status === 'completed' ? $createdAt->copy()->addDays(rand(2, 10)) : $createdAt;

            $request = Request::create([
                'client_id'         => $client->id,
                'craftsman_id'      => $craftsmanId,
                'category_id'       => $category->id,
                'title'             => $titles[array_rand($titles)],
                'description'       => 'المشكلة كالتالي: ' . fake()->realText(200),
                'address'           => $addresses[array_rand($addresses)],
                'preferred_date'    => $createdAt->copy()->addDays(rand(2, 15)),
                'preferred_time'    => sprintf('%02d:00:00', rand(8, 18)),
                'budget'            => $budget,
                'ai_estimate'       => rand(1, 5) === 1 ? $budget + rand(-500, 500) : null,
                'status'            => $status,
                'is_emergency'      => $isEmergency,
                'use_installment'   => $useInstallment,
                'installment_count' => $installmentCount,
                'has_warranty'      => $status === 'completed',
                'warranty_end_date' => $status === 'completed' ? $updatedAt->copy()->addDays(30) : null,
                'created_at'        => $createdAt,
                'updated_at'        => $updatedAt,
            ]);

            // للأقساط
            if ($useInstallment && $installmentCount > 0) {
                $perAmount = round($budget / $installmentCount, 2);
                for ($n = 1; $n <= $installmentCount; $n++) {
                    $paidCount = $status === 'completed' ? $installmentCount : ($status === 'in_progress' ? $installmentCount : rand(0, $installmentCount));
                    Installment::create([
                        'request_id'          => $request->id,
                        'total_amount'        => $budget,
                        'paid_amount'         => $n <= $paidCount ? $perAmount : 0,
                        'remaining_amount'    => $n <= $paidCount ? 0 : $perAmount,
                        'installment_count'   => $installmentCount,
                        'current_installment' => $n,
                        'due_date'            => $createdAt->copy()->addMonths($n - 1),
                        'status'              => $n <= $paidCount ? 'paid' : 'pending',
                        'created_at'          => $createdAt,
                        'updated_at'          => $updatedAt,
                    ]);
                }
            }

            // معاملات مالية مبدئية
            if ($craftsmanId) {
                $wallet = CraftsmanWallet::where('craftsman_id', $craftsmanId)->first();
                if ($wallet) {
                    $type = match($status) {
                        'in_progress', 'accepted' => 'escrow_in',
                        'completed' => 'escrow_release',
                        default => 'escrow_in',
                    };
                    WalletTransaction::create([
                        'craftsman_id'  => $craftsmanId,
                        'request_id'    => $request->id,
                        'type'          => $type,
                        'amount'        => $budget,
                        'balance_after' => $wallet->balance,
                        'description'   => "معاملة للطلب #" . str_pad($request->id, 4, '0', STR_PAD_LEFT),
                        'status'        => 'completed',
                        'created_at'    => $updatedAt,
                        'updated_at'    => $updatedAt,
                    ]);
                }
            }
        }

        $this->command->line('   ✓ 40 طلب + معاملات مالية + أقساط');
    }

    // ==========================================
    // 7) الرسائل
    // ==========================================
    private function seedMessages(): void
    {
        $existing = Message::count();
        if ($existing > 0) return;

        $requests = Request::whereNotNull('craftsman_id')->inRandomOrder()->take(20)->get();

        $sampleMessages = [
            'السلام عليكم، متى يمكنك البدء؟',
            'وعليكم السلام، بكرة الصباح إن شاء الله',
            'ممتاز، سأكون في المنزل من الساعة 9',
            'تمام، سأتواصل معك قبل الوصول',
            'شكراً جزيلاً على سرعة الاستجابة',
            'العفو، هذا واجبنا',
            'هل المشكلة تحتاج قطع غيار؟',
            'نعم، سأشتريها وأحسبها مع التكلفة',
            'ممكن صور أوضح للمشكلة؟',
            'سأرسل لك الآن',
        ];

        foreach ($requests as $req) {
            $count = rand(3, 8);
            $createdAt = $req->created_at->copy()->addHours(2);

            for ($i = 0; $i < $count; $i++) {
                $isClient = $i % 2 === 0;
                Message::create([
                    'request_id'  => $req->id,
                    'sender_id'   => $isClient ? $req->client_id : $req->craftsman_id,
                    'receiver_id' => $isClient ? $req->craftsman_id : $req->client_id,
                    'message'     => $sampleMessages[array_rand($sampleMessages)],
                    'is_read'     => rand(0, 1) === 1,
                    'is_voice'    => false,
                    'created_at'  => $createdAt->copy()->addMinutes($i * 15),
                    'updated_at'  => $createdAt->copy()->addMinutes($i * 15),
                ]);
            }
        }
        $this->command->line('   ✓ محادثات لـ 20 طلب');
    }

    // ==========================================
    // 8) التقييمات
    // ==========================================
    private function seedReviews(): void
    {
        $existing = Review::count();
        if ($existing > 0) return;

        $completedRequests = Request::where('status', 'completed')
            ->whereNotNull('craftsman_id')
            ->whereDoesntHave('review')
            ->inRandomOrder()
            ->take(25)
            ->get();

        $comments = [
            'خدمة ممتازة وسريع في الوصول',
            'احترافي جداً، أنصح بالتعامل معه',
            'السعر مناسب والعمل متقن',
            'شكراً على الإتقان',
            'تعامل راقي وعمل نظيف',
            'ممتاز، سأكرر التعامل',
            'جيد بس تأخر قليلاً',
            'العمل ممتاز لكن السعر مرتفع',
            'خدمة جيدة',
            'متوسط',
            'احتاج تحسين في التعامل',
        ];

        foreach ($completedRequests as $req) {
            $rating = [5, 5, 5, 5, 4, 4, 4, 3, 3, 2][array_rand([5, 5, 5, 5, 4, 4, 4, 3, 3, 2])];

            Review::create([
                'request_id'         => $req->id,
                'client_id'          => $req->client_id,
                'craftsman_id'       => $req->craftsman_id,
                'rating'             => $rating,
                'quality_rating'     => max(1, min(5, $rating + rand(-1, 1))),
                'punctuality_rating' => max(1, min(5, $rating + rand(-1, 1))),
                'behavior_rating'    => max(1, min(5, $rating + rand(-1, 1))),
                'comment'            => $comments[array_rand($comments)],
                'created_at'         => $req->updated_at->copy()->addDays(rand(1, 5)),
                'updated_at'         => $req->updated_at->copy()->addDays(rand(1, 5)),
            ]);
        }

        // تحديث إحصائيات الحرفيين
        $craftsmen = User::where('role', 'craftsman')->get();
        foreach ($craftsmen as $c) {
            $stats = Review::where('craftsman_id', $c->id)
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
                ->first();

            if ($stats && $stats->total > 0) {
                CraftsmanProfile::where('user_id', $c->id)->update([
                    'rating_avg'    => round($stats->avg_rating, 2),
                    'total_reviews' => $stats->total,
                ]);
            }
        }

        $this->command->line('   ✓ ' . $completedRequests->count() . ' تقييم');
    }

    // ==========================================
    // 9) الشكاوى
    // ==========================================
    private function seedComplaints(): void
    {
        $existing = Complaint::count();
        if ($existing > 0) return;

        $requests = Request::whereNotNull('craftsman_id')
            ->whereIn('status', ['completed', 'in_progress', 'accepted'])
            ->inRandomOrder()
            ->take(6)
            ->get();

        $reasons = ['quality', 'delay', 'price', 'behavior', 'other'];
        $statuses = ['pending', 'pending', 'reviewing', 'resolved', 'rejected', 'pending'];

        foreach ($requests as $i => $req) {
            $status = $statuses[$i % count($statuses)];
            $createdAt = $req->updated_at->copy()->addDays(rand(1, 10));

            Complaint::create([
                'request_id'     => $req->id,
                'client_id'      => $req->client_id,
                'craftsman_id'   => $req->craftsman_id,
                'reason'         => $reasons[array_rand($reasons)],
                'details'        => 'تفاصيل الشكوى: ' . fake()->realText(150),
                'status'         => $status,
                'admin_response' => in_array($status, ['resolved', 'rejected'])
                    ? 'رد الإدارة: تمت مراجعة الشكوى بعناية واتخاذ الإجراء المناسب'
                    : null,
                'resolved_at'    => $status === 'resolved' ? $createdAt->copy()->addDays(2) : null,
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ]);
        }

        $this->command->line('   ✓ 6 شكاوى');
    }

    // ==========================================
    // 10) الإشعارات
    // ==========================================
    private function seedNotifications(): void
    {
        $existing = Notification::count();
        if ($existing > 0) return;

        $clients = User::where('role', 'client')->get();
        $templates = [
            ['title' => '✅ تم قبول طلبك',        'message' => 'قبل الحرفي طلبك وبدأ التنفيذ',      'type' => 'success'],
            ['title' => '💰 تم الدفع',             'message' => 'تم استلام دفعتك بنجاح',             'type' => 'success'],
            ['title' => '⭐ تقييمك مهم',           'message' => 'قيّم خدمة الحرفي لتحسين التجربة',   'type' => 'info'],
            ['title' => '⏰ موعدك غداً',           'message' => 'لديك موعد مع الحرفي غداً',          'type' => 'warning'],
            ['title' => '🎉 تم إنجاز الطلب',       'message' => 'أكمل الحرفي الطلب بنجاح',           'type' => 'success'],
            ['title' => '🚨 طارئ',                 'message' => 'تم قبول طلب الطوارئ',                'type' => 'danger'],
        ];

        foreach ($clients as $client) {
            $count = rand(2, 5);
            for ($i = 0; $i < $count; $i++) {
                $tpl = $templates[array_rand($templates)];
                Notification::create([
                    'user_id'    => $client->id,
                    'title'      => $tpl['title'],
                    'message'    => $tpl['message'],
                    'type'       => $tpl['type'],
                    'is_read'    => rand(0, 1) === 1,
                    'created_at' => now()->subHours(rand(1, 72)),
                    'updated_at' => now()->subHours(rand(1, 72)),
                ]);
            }
        }

        $this->command->line('   ✓ إشعارات للعملاء');
    }

    // ==========================================
    // 11) الأقساط (مضافة مع الطلبات)
    // ==========================================
    private function seedInstallments(): void
    {
        // تمت إضافتها مع الطلبات
        $count = Installment::count();
        $this->command->line('   ✓ ' . $count . ' قسط');
    }

    // ==========================================
    // 12) الفيديوهات التعليمية
    // ==========================================
    private function seedTutorials(): void
    {
        $tutorials = [
            ['category_id' => 1, 'title' => 'كيفية إصلاح تسريب المياه', 'description' => 'شرح مبسط لإصلاح التسريبات بنفسك', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
            ['category_id' => 2, 'title' => 'تركيب مفتاح كهرباء بأمان', 'description' => 'طريقة آمنة لتركيب المفاتيح', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
            ['category_id' => 3, 'title' => 'صنع باب خشبي خطوة بخطوة', 'description' => 'دليل شامل لصنع باب خشبي', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
            ['category_id' => 4, 'title' => 'أساسيات الدهانات المنزلية', 'description' => 'تعلم الدهانات باحترافية', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
            ['category_id' => 5, 'title' => 'صيانة المكيفات الدورية', 'description' => 'كيف تحافظ على مكيفك', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
        ];
        foreach ($tutorials as $t) {
            Tutorial::updateOrCreate(['title' => $t['title']], $t + ['views' => rand(50, 500)]);
        }
        $this->command->line('   ✓ 5 فيديوهات تعليمية');
    }

    // ==========================================
    // 13) المكافآت
    // ==========================================
    private function seedRewards(): void
    {
        $existing = Reward::count();
        if ($existing > 0) return;

        $clients = User::where('role', 'client')->get();

        foreach ($clients as $client) {
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                Reward::create([
                    'user_id'       => $client->id,
                    'points_earned' => rand(5, 15),
                    'action_type'   => ['request_completed', 'review_given', 'referral', 'bonus'][array_rand([0, 1, 2, 3])],
                    'description'   => 'نقاط مكتسبة',
                    'created_at'    => now()->subDays(rand(1, 60)),
                    'updated_at'    => now()->subDays(rand(1, 60)),
                ]);
            }
        }
        $this->command->line('   ✓ مكافآت للعملاء');
    }
}
