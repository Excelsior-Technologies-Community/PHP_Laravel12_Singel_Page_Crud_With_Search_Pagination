<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | User ID Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {

            $query->where(
                'user_id',
                $request->user_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Action Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('action')) {

            $query->where(
                'action',
                $request->action
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortBy = $request->get(
            'sort_by',
            'created_at'
        );

        $sortOrder = $request->get(
            'sort_order',
            'desc'
        );

        $allowedSorts = [
            'id',
            'created_at',
            'action',
            'ip_address',
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy(
            $sortBy,
            $sortOrder
        );

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $logs = $query
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalLogs = ActivityLog::count();

        $todayLogs = ActivityLog::whereDate(
            'created_at',
            today()
        )->count();

        $thisWeekLogs = ActivityLog::where(
            'created_at',
            '>=',
            now()->startOfWeek()
        )->count();

        $thisMonthLogs = ActivityLog::where(
            'created_at',
            '>=',
            now()->startOfMonth()
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Actions
        |--------------------------------------------------------------------------
        */

        $actions = ActivityLog::query()
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        $users = ActivityLog::with('user')
            ->whereNotNull('user_id')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        return view(
            'activity-logs.index',
            compact(
                'logs',
                'totalLogs',
                'todayLogs',
                'thisWeekLogs',
                'thisMonthLogs',
                'actions',
                'users'
            )
        );
    }

    /**
     * Show single activity log.
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'log' => $log,
        ]);
    }

    /**
     * Delete activity log.
     */
    public function destroy($id)
    {
        $log = ActivityLog::findOrFail($id);

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Log deleted successfully!',
        ]);
    }

    /**
     * Clear old activity logs.
     */
    public function clearOld(Request $request)
    {
        $days = (int) $request->get('days', 30);

        if ($days < 1) {
            $days = 30;
        }

        $deleted = ActivityLog::where(
            'created_at',
            '<',
            now()->subDays($days)
        )->delete();

        return redirect()
            ->route('activity-logs.index')
            ->with(
                'success',
                "{$deleted} old logs cleared!"
            );
    }

    /**
     * Export activity logs as CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = ActivityLog::with('user');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {

            $query->where(
                'user_id',
                $request->user_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Action
        |--------------------------------------------------------------------------
        */

        if ($request->filled('action')) {

            $query->where(
                'action',
                $request->action
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sortBy = $request->get(
            'sort_by',
            'created_at'
        );

        $sortOrder = $request->get(
            'sort_order',
            'desc'
        );

        $allowedSorts = [
            'id',
            'created_at',
            'action',
            'ip_address',
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy(
            $sortBy,
            $sortOrder
        );

        $logs = $query->get();

        $filename =
            'activity-logs-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        return response()->streamDownload(
            function () use ($logs) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                fputcsv($handle, [
                    'ID',
                    'User ID',
                    'User',
                    'Action',
                    'Model',
                    'Model ID',
                    'Description',
                    'IP Address',
                    'Created At',
                ]);

                foreach ($logs as $log) {

                    fputcsv($handle, [
                        $log->id,
                        $log->user_id ?? '',
                        optional($log->user)->name ?? 'System',
                        $log->action,
                        $log->model_type ?? '',
                        $log->model_id ?? '',
                        $log->description ?? '',
                        $log->ip_address ?? '',
                        optional($log->created_at)
                            ->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($handle);

            },
            $filename
        );
    }
}