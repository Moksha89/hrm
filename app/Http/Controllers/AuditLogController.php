<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Only Admin, Manager, and Team Leader can view logs
        if (!$user->isAdmin() && !$user->isManager() && !$user->isTeamLeader()) {
            abort(403, 'Unauthorized access.');
        }
        
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');
        
        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        
        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Search by description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }
        
        $logs = $query->paginate(50);
        
        // Get unique modules and actions for filter dropdowns
        $modules = AuditLog::distinct()->pluck('module')->filter()->sort()->values();
        $actions = AuditLog::distinct()->pluck('action')->filter()->sort()->values();
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);
        
        return view('logs.index', compact('logs', 'modules', 'actions', 'users'));
    }

    public function show($id)
    {
        $user = auth()->user();
        
        // Only Admin, Manager, and Team Leader can view logs
        if (!$user->isAdmin() && !$user->isManager() && !$user->isTeamLeader()) {
            abort(403, 'Unauthorized access.');
        }
        
        $log = AuditLog::with('user')->findOrFail($id);
        
        return view('logs.show', compact('log'));
    }
}
