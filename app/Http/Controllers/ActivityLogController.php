<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->hasPermission('view-all-activity-logs')) {
            $query = ActivityLog::with('user');
        } else {
            $query = ActivityLog::where('user_id', Auth::id());
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
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

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        $users = User::all();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('activity-logs.index', compact('logs', 'users', 'actions'));
    }
}
