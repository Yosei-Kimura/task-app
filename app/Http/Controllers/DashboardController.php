<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * ダッシュボードを表示
     */
    public function index(): View
    {
        // 現在アクティブなイベント
        $currentEvents = Event::active()->current()->with(['teams', 'tasks'])->get();

        // 緊急度の高いタスク（期限切れ、今日締切、明日締切）
        $overdueTasks = Task::active()->overdue()->with(['team', 'assignedMember', 'progressStatus'])->get();
        $todayTasks = Task::active()->dueToday()->with(['team', 'assignedMember', 'progressStatus'])->get();
        $tomorrowTasks = Task::active()->dueTomorrow()->with(['team', 'assignedMember', 'progressStatus'])->get();

        // 統計情報
        $stats = [
            'total_events' => Event::active()->count(),
            'total_tasks' => Task::active()->count(),
            'completed_tasks' => Task::active()->whereHas('progressStatus', function($q) {
                $q->where('is_completed', true);
            })->count(),
            'overdue_tasks' => $overdueTasks->count(),
        ];

        return view('dashboard', compact(
            'currentEvents',
            'overdueTasks',
            'todayTasks',
            'tomorrowTasks',
            'stats'
        ));
    }
}
