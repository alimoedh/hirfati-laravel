<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{User, Category, CraftsmanProfile, Setting, CraftsmanWallet};

class MigrateOldData extends Command
{
    protected $signature = 'hirfati:migrate-old-data';
    protected $description = 'نقل البيانات من قاعدة hyrfi القديمة';

    public function handle(): int
    {
        $this->warn('⚠️  هذا الأمر سينقل البيانات من قاعدة hyrfi القديمة.');
        if (!$this->confirm('هل أنت متأكد؟')) return self::FAILURE;

        config([
            'database.connections.legacy' => [
                'driver'    => 'mysql',
                'host'      => '127.0.0.1',
                'database'  => 'hyrfi',
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
            ],
        ]);

        try {
            DB::connection('legacy')->getPdo();
        } catch (\Exception $e) {
            $this->error('فشل الاتصال بقاعدة hyrfi: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('✅ تم الاتصال بقاعدة hyrfi');

        $legacy = DB::connection('legacy');
        $this->migrateUsers($legacy);
        $this->migrateCategories($legacy);
        $this->migrateProfiles($legacy);
        $this->migrateSettings($legacy);

        $this->info('');
        $this->info('🎉 اكتمل نقل البيانات!');

        return self::SUCCESS;
    }

    private function migrateUsers($legacy): void
    {
        $this->info('👥 نقل المستخدمين...');
        $users = $legacy->table('users')->get();

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u->email],
                [
                    'full_name'           => $u->full_name,
                    'phone'               => $u->phone,
                    'password'            => $u->password,
                    'role'                => $u->role,
                    'avatar'              => $u->avatar,
                    'is_verified'         => (bool) $u->is_verified,
                    'is_active'           => (bool) $u->is_active,
                    'loyalty_points'      => $u->loyalty_points ?? 0,
                    'total_points_earned' => $u->total_points_earned ?? 0,
                    'total_points_spent'  => $u->total_points_spent ?? 0,
                    'created_at'          => $u->created_at,
                    'updated_at'          => $u->updated_at,
                ]
            );
        }
        $this->line(' ✅ ' . count($users));
    }

    private function migrateCategories($legacy): void
    {
        $this->info('📁 نقل التصنيفات...');
        $cats = $legacy->table('categories')->get();
        foreach ($cats as $c) {
            Category::updateOrCreate(
                ['name' => $c->name],
                ['icon' => $c->icon, 'is_active' => (bool) $c->is_active]
            );
        }
        $this->line(' ✅ ' . count($cats));
    }

    private function migrateProfiles($legacy): void
    {
        $this->info('🔧 نقل ملفات الحرفيين...');
        $profiles = $legacy->table('craftsman_profiles')->get();

        foreach ($profiles as $p) {
            $user = User::where('email', optional($legacy->table('users')->where('id', $p->user_id)->first())->email)->first();
            if (!$user) continue;

            $oldCat = $legacy->table('categories')->where('id', $p->category_id)->first();
            $newCat = $oldCat ? Category::where('name', $oldCat->name)->first() : null;

            CraftsmanProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id'      => $newCat->id ?? 1,
                    'experience_years' => $p->experience_years ?? 0,
                    'bio'              => $p->bio,
                    'identity_document'=> $p->identity_document,
                    'hourly_rate'      => $p->hourly_rate ?? 0,
                    'is_approved'      => (bool) $p->is_approved,
                    'is_available'     => (bool) ($p->is_available ?? true),
                    'is_emergency'     => (bool) ($p->is_emergency ?? false),
                    'emergency_phone'  => $p->emergency_phone,
                    'rating_avg'       => $p->rating_avg ?? 0,
                    'total_reviews'    => $p->total_reviews ?? 0,
                ]
            );
        }
        $this->line(' ✅ ' . count($profiles));
    }

    private function migrateSettings($legacy): void
    {
        $this->info('⚙️ نقل الإعدادات...');
        $settings = $legacy->table('settings')->get();
        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['setting_key' => $s->setting_key],
                ['setting_value' => $s->setting_value]
            );
        }
        $this->line(' ✅ ' . count($settings));
    }
}
