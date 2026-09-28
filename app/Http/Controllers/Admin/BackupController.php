<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    private string $backupDir = 'backups';

    public function index()
    {
        if (!Storage::disk('local')->exists($this->backupDir)) {
            Storage::disk('local')->makeDirectory($this->backupDir);
        }

        $files = collect(Storage::disk('local')->files($this->backupDir))
            ->filter(fn($f) => str_ends_with($f, '.sql'))
            ->map(fn($f) => [
                'name'  => basename($f),
                'size'  => round(Storage::disk('local')->size($f) / 1024, 2) . ' KB',
                'date'  => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($f)),
                'path'  => $f,
            ])
            ->sortByDesc('date')
            ->values();

        return view('admin.backup', compact('files'));
    }

    public function create()
    {
        Storage::disk('local')->makeDirectory($this->backupDir);

        $filename = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path     = storage_path("app/private/{$this->backupDir}/{$filename}");

        try {
            $sql = $this->generateSqlDump();

            if (empty($sql)) {
                return back()->with('error', 'فشل: لم يتم توليد بيانات');
            }

            file_put_contents($path, $sql);

            if (file_exists($path) && filesize($path) > 0) {
                ActivityService::log(auth()->id(), 'create_backup', "إنشاء نسخة احتياطية: {$filename}");
                return back()->with('success', '✅ تم إنشاء النسخة الاحتياطية بنجاح: ' . $filename);
            }

            return back()->with('error', 'فشل في كتابة الملف');
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ: ' . $e->getMessage());
        }
    }

    /**
     * استعادة نسخة احتياطية — بثلاث طبقات حماية
     */
    public function restore(Request $request)
    {
        // ═══════════ الطبقة 1: التحقق من Confirm ═══════════
        $request->validate([
            'file'    => 'required|string',
            'confirm' => 'required|string',
        ]);

        if ($request->input('confirm') !== 'RESTORE') {
            return back()->with('error', '❌ يجب كتابة كلمة RESTORE بالضبط للتأكيد');
        }

        $file = basename($request->input('file'));
        $path = storage_path("app/private/{$this->backupDir}/{$file}");

        if (!file_exists($path)) {
            return back()->with('error', 'الملف غير موجود');
        }

        try {
            // ═══════════ الطبقة 2: نسخة تلقائية قبل الاستعادة ═══════════
            $safetyName = 'safety_before_restore_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $safetyPath = storage_path("app/private/{$this->backupDir}/{$safetyName}");

            try {
                $safetySql = $this->generateSqlDump();
                if (!empty($safetySql)) {
                    file_put_contents($safetyPath, $safetySql);
                    ActivityService::log(auth()->id(), 'safety_backup', "نسخة أمان قبل الاستعادة: {$safetyName}");
                }
            } catch (\Exception $e) {
                return back()->with('error', '⚠️ فشل إنشاء نسخة الأمان. توقف الإجراء لحماية بياناتك.');
            }

            // ═══════════ الطبقة 3: تنفيذ الاستعادة ═══════════
            $pdo = DB::connection()->getPdo();
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

            $sql = file_get_contents($path);
            $statements = $this->splitSqlStatements($sql);

            $count = 0;
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    $pdo->exec($statement);
                    $count++;
                }
            }

            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

            ActivityService::log(auth()->id(), 'restore_backup', "استعادة نسخة: {$file} ({$count} جملة)");

            return back()->with('success', "✅ تم الاستعادة بنجاح ({$count} جملة). نسخة أمان محفوظة: {$safetyName}");
        } catch (\Exception $e) {
            return back()->with('error', '❌ فشل الاستعادة: ' . $e->getMessage() . ' — يمكنك استعادة نسخة الأمان');
        }
    }

    /**
     * معاينة النسخة قبل الاستعادة
     */
    public function preview(string $file)
    {
        $file = basename($file);
        $path = storage_path("app/private/{$this->backupDir}/{$file}");

        if (!file_exists($path)) {
            return response()->json(['error' => 'الملف غير موجود'], 404);
        }

        $content = file_get_contents($path);

        // عدد الجداول
        preg_match_all('/CREATE TABLE `([^`]+)`/i', $content, $tables);
        $tablesCount = count($tables[1] ?? []);

        // عدد السجلات (INSERT groups)
        preg_match_all('/INSERT INTO/i', $content, $inserts);
        $insertsCount = count($inserts[0] ?? []);

        return response()->json([
            'name'          => $file,
            'size'          => round(filesize($path) / 1024, 2) . ' KB',
            'date'          => date('Y-m-d H:i:s', filemtime($path)),
            'tables_count'  => $tablesCount,
            'tables'        => $tables[1] ?? [],
            'inserts_count' => $insertsCount,
        ]);
    }

    public function download(string $file): StreamedResponse
    {
        $file = basename($file);
        return Storage::disk('local')->download("{$this->backupDir}/{$file}");
    }

    public function destroy(string $file)
    {
        $file = basename($file);
        $path = "{$this->backupDir}/{$file}";

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            ActivityService::log(auth()->id(), 'delete_backup', "حذف نسخة: {$file}");
            return back()->with('success', '✅ تم حذف النسخة');
        }

        return back()->with('error', 'الملف غير موجود');
    }

    /**
     * توليد SQL dump باستخدام PHP خالص
     */
    private function generateSqlDump(): string
    {
        $pdo      = DB::connection()->getPdo();
        $database = config('database.connections.mysql.database');

        $sql  = "-- Hirfati Backup\n";
        $sql .= "-- Date: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$database}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n";

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $create = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $create['Create Table'] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);

            if (count($rows) > 0) {
                $sql .= "INSERT INTO `{$table}` VALUES\n";

                $values = [];
                foreach ($rows as $row) {
                    $escaped = array_map(function ($value) use ($pdo) {
                        if ($value === null) return 'NULL';
                        return $pdo->quote($value);
                    }, $row);

                    $values[] = '(' . implode(', ', $escaped) . ')';
                }

                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    /**
     * تقسيم SQL إلى جمل منفصلة
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current    = '';
        $inString   = false;
        $stringChar = '';
        $len        = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];

            if (!$inString && ($char === "'" || $char === '"')) {
                $inString   = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar) {
                $escaped = ($i > 0 && $sql[$i - 1] === '\\');
                if (!$escaped) {
                    $inString = false;
                }
            }

            $current .= $char;

            if (!$inString && $char === ';') {
                $statements[] = $current;
                $current = '';
            }
        }

        if (trim($current) !== '') {
            $statements[] = $current;
        }

        return $statements;
    }
}
