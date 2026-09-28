<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $reports = [
            'total_requests'     => Request::where('client_id', $user->id)->count(),
            'completed_requests' => Request::where('client_id', $user->id)->where('status', 'completed')->count(),
        ];
        return view('client.reports', compact('reports'));
    }

    public function export(HttpRequest $request)
    {
        $user = auth()->user();
        $type = $request->input('type', 'csv');

        $data = Request::where('client_id', $user->id)
            ->orWhere('craftsman_id', $user->id)
            ->get();

        if ($type === 'csv') {
            $filename = 'report_' . now()->format('Y-m-d') . '.csv';
            return response()->streamDownload(function () use ($data) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['ID', 'العنوان', 'الوصف', 'الحالة', 'التاريخ', 'الميزانية']);
                foreach ($data as $row) {
                    fputcsv($out, [
                        $row->id, $row->title, $row->description,
                        $row->status, $row->created_at->toDateString(), $row->budget,
                    ]);
                }
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return response()->json($data);
    }
}
