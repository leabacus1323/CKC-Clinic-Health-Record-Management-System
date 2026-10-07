<?php

namespace App\Http\Controllers;

use App\Models\HealthAnalyticsSnapshot;
use App\Services\HealthAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminHealthAnalyticsController extends Controller
{
    public function index(Request $request, HealthAnalyticsService $analytics): View
    {
        $period = (int) $request->integer('period', 30);
        if (! in_array($period, HealthAnalyticsService::ALLOWED_PERIODS, true)) {
            $period = 30;
        }

        return view('admin.analytics.index', [
            'period' => $period,
            'report' => $analytics->generate($period),
            'snapshots' => HealthAnalyticsSnapshot::query()
                ->with('generatedByUser:id,name')
                ->latest('generated_at')
                ->limit(12)
                ->get(),
        ]);
    }

    public function generate(Request $request, HealthAnalyticsService $analytics): RedirectResponse
    {
        $validated = $request->validate([
            'period' => ['required', 'integer', 'in:30,90,180,365'],
        ]);

        $analytics->createSnapshot(
            (int) $validated['period'],
            'manual',
            $request->user()?->id,
        );

        return redirect()
            ->route('admin.analytics.index', ['period' => $validated['period']])
            ->with('success', 'A new privacy-safe analytics snapshot was generated.');
    }

    public function export(Request $request, HealthAnalyticsService $analytics): Response
    {
        $period = (int) $request->integer('period', 30);
        if (! in_array($period, HealthAnalyticsService::ALLOWED_PERIODS, true)) {
            $period = 30;
        }

        $report = $analytics->generate($period);
        $metrics = $report['metrics'];
        $rows = [
            ['CKC Clinic Health Record Management System - Automated Health Analytics'],
            ['Period', $metrics['period_start'].' to '.$metrics['period_end']],
            ['Generated at', now()->format('Y-m-d H:i:s')],
            [],
            ['Metric', 'Value'],
        ];

        foreach ($metrics as $key => $value) {
            if (is_scalar($value)) {
                $rows[] = [str_replace('_', ' ', ucfirst($key)), $value];
            }
        }

        $rows[] = [];
        $rows[] = ['Aggregated visit category', 'Count'];
        foreach ($report['trends']['visit_categories'] as $item) {
            $rows[] = [$item['label'], $item['count']];
        }

        $rows[] = [];
        $rows[] = ['Evidence-based recommendation', 'Priority', 'Action'];
        foreach ($report['recommendations'] as $item) {
            $rows[] = [$item['title'], strtoupper($item['priority']), $item['action']];
        }

        $stream = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($stream, $row);
        }
        rewind($stream);
        $csv = stream_get_contents($stream) ?: '';
        fclose($stream);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="health-analytics-'.$period.'-days-'.now()->format('Ymd-His').'.csv"',
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
