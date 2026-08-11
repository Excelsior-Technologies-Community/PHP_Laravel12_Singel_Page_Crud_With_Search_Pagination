<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(20);

        return view('activity-logs.index', compact('logs'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);

        return response()->json(['success' => true, 'log' => $log]);
    }

    public function destroy($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();

        return response()->json(['success' => true, 'message' => 'Log deleted successfully!']);
    }

    public function clearOld(Request $request)
    {
        $days = $request->get('days', 30);
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return response()->json(['success' => true, 'message' => "{$deleted} old logs cleared!"]);
    }
}
