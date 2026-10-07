<?php

namespace App\Http\Controllers;

use App\Models\InfrastructureEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InfrastructureLogController extends Controller
{
    /**
     * Show the authenticated clinic staff member only their own operational log.
     * Medical record contents and request payloads are intentionally excluded.
     */
    public function staffIndex(Request $request): View
    {
        $user = $request->user();

        $eventsQuery = InfrastructureEvent::query()
            ->where('actor_user_id', $user->id)
            ->latest('occurred_at');

        if ($request->filled('category')) {
            $eventsQuery->where('event_category', $request->string('category')->toString());
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            if ($status === 'success') {
                $eventsQuery->whereBetween('response_status', [200, 399]);
            } elseif ($status === 'failed') {
                $eventsQuery->where('response_status', '>=', 400);
            }
        }

        $events = $eventsQuery->paginate(40)->withQueryString();

        $base = InfrastructureEvent::query()->where('actor_user_id', $user->id);
        $summary = [
            'today' => (clone $base)->whereDate('occurred_at', today())->count(),
            'writes' => (clone $base)->whereIn('action', ['CREATE', 'UPDATE', 'DELETE'])->count(),
            'failed' => (clone $base)->where('response_status', '>=', 400)->count(),
            'total' => (clone $base)->count(),
        ];

        $categories = InfrastructureEvent::query()
            ->where('actor_user_id', $user->id)
            ->whereNotNull('event_category')
            ->distinct()
            ->orderBy('event_category')
            ->pluck('event_category');

        $system = [
            'Environment' => app()->environment(),
            'Database' => DB::connection()->getDriverName(),
            'Cache' => config('cache.default'),
            'Session' => config('session.driver'),
            'Queue' => config('queue.default'),
        ];

        return view('staff.infrastructure-logs', compact('events', 'summary', 'categories', 'system'));
    }
}
