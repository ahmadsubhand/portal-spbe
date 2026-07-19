<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AuditLog::class);

        $action = trim((string) $request->string('action'));
        $actor = trim((string) $request->string('actor'));

        $logsQuery = AuditLog::query()
            ->with('user')
            ->when($action !== '', fn ($query) => $query->where('action', $action))
            ->when($actor !== '', function ($query) use ($actor): void {
                $query->whereHas('user', function ($userQuery) use ($actor): void {
                    $userQuery->where('name', 'like', '%'.$actor.'%')
                        ->orWhere('email', 'like', '%'.$actor.'%');
                });
            });

        return view('admin.audit-logs.index', [
            'logs' => $logsQuery
                ->latest('created_at')
                ->paginate(20)
                ->withQueryString(),
            'actions' => AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
            'filters' => [
                'action' => $action,
                'actor' => $actor,
            ],
            'stats' => [
                'total' => AuditLog::count(),
                'today' => AuditLog::where('created_at', '>=', now()->startOfDay())->count(),
                'actors' => AuditLog::distinct('user_id')->count('user_id'),
                'actions' => AuditLog::distinct('action')->count('action'),
            ],
        ]);
    }
}
