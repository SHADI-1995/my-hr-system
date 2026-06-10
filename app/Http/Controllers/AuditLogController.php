<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!auth()->user()->hasPermission('audit_logs.view'), 403);

        $query = AuditLog::with('user');

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->module) {
            $query->where('module', $request->module);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->latest()->paginate(20);

        $users = User::orderBy('name')->get();

        return view('audit_logs.index', compact('auditLogs', 'users'));
    }

    public function show(AuditLog $auditLog)
    {
        abort_if(!auth()->user()->hasPermission('audit_logs.view'), 403);

        $auditLog->load('user');

        return view('audit_logs.show', compact('auditLog'));
    }
}
