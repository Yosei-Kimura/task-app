<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Event;
use App\Models\Team;
use App\Models\Member;
use App\Models\ProgressStatus;
use App\Models\TaskHistory;
use App\Services\SlackNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * タスク一覧を表示
     */
    public function index(Request $request): View
    {
        $query = Task::with(['event', 'team', 'assignedMember', 'progressStatus']);

        // フィルター処理
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->filled('status_id')) {
            $query->where('progress_status_id', $request->status_id);
        }

        if ($request->filled('assigned_member_id')) {
            $query->where('assigned_member_id', $request->assigned_member_id);
        }

        // ソート処理
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $tasks = $query->paginate(15);

        // フィルター用のデータ
        $events = Event::active()->get();
        $teams = Team::active()->get();
        $members = Member::active()->with(['teams' => function($query) {
            $query->where('member_team.is_active', true);
        }])->get();
        $statuses = ProgressStatus::active()->get();

        return view('tasks.index', compact('tasks', 'events', 'teams', 'members', 'statuses'));
    }

    /**
     * タスク詳細を表示
     */
    public function show(Task $task): View
    {
        $task->load([
            'event', 
            'team', 
            'assignedMember', 
            'progressStatus', 
            'histories.changedByMember'
        ]);

        return view('tasks.show', compact('task'));
    }

    /**
     * タスク作成フォームを表示
     */
    public function create(Request $request): View
    {
        $events = Event::active()->get();
        $teams = Team::active()->get();
        $members = Member::active()->with(['teams' => function($query) {
            $query->where('member_team.is_active', true);
        }])->get();
        $statuses = ProgressStatus::active()->get();

        // URLパラメータからデフォルト値を設定
        $defaultEventId = $request->get('event_id');
        $defaultTeamId = $request->get('team_id');

        return view('tasks.create', compact(
            'events', 
            'teams', 
            'members', 
            'statuses', 
            'defaultEventId', 
            'defaultTeamId'
        ));
    }

    /**
     * タスクを作成
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_id' => 'required|exists:teams,id',
            'assigned_member_id' => 'nullable|exists:members,id',
            'progress_status_id' => 'nullable|exists:progress_statuses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|datetime',
            'priority' => 'required|integer|min:1|max:5',
        ]);

        $task = Task::create($validated);

        // 履歴を記録
        $this->recordHistory($task, 'created', null, $validated, $request->get('changed_by_member_id'));

        return redirect()->route('tasks.show', $task)
                        ->with('success', 'タスクが作成されました。');
    }

    /**
     * タスク編集フォームを表示
     */
    public function edit(Task $task): View
    {
        $events = Event::active()->get();
        $teams = Team::active()->get();
        $members = Member::active()->with(['teams' => function($query) {
            $query->where('member_team.is_active', true);
        }])->get();
        $statuses = ProgressStatus::where('event_id', $task->event_id)->active()->get();

        return view('tasks.edit', compact('task', 'events', 'teams', 'members', 'statuses'));
    }

    /**
     * タスクを更新
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'team_id' => 'required|exists:teams,id',
            'assigned_member_id' => 'nullable|exists:members,id',
            'progress_status_id' => 'nullable|exists:progress_statuses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|datetime',
            'priority' => 'required|integer|min:1|max:5',
        ]);

        $oldValues = $task->toArray();
        $task->update($validated);

        // 履歴を記録
        $this->recordHistory($task, 'updated', $oldValues, $validated, $request->get('changed_by_member_id'));

        return redirect()->route('tasks.show', $task)
                        ->with('success', 'タスクが更新されました。');
    }

    /**
     * タスクを削除
     */
    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $oldValues = $task->toArray();
        
        // 履歴を記録
        $this->recordHistory($task, 'deleted', $oldValues, null, $request->get('changed_by_member_id'));

        $task->delete();

        return redirect()->route('tasks.index')
                        ->with('success', 'タスクが削除されました。');
    }

    /**
     * タスクのステータスを更新
     */
    public function updateStatus(Request $request, Task $task, SlackNotificationService $slackService): RedirectResponse
    {
        $validated = $request->validate([
            'progress_status_id' => 'required|exists:progress_statuses,id',
            'changed_by_member_id' => 'nullable|exists:members,id',
            'comment' => 'nullable|string',
        ]);

        $oldStatus = $task->progressStatus;
        $oldStatusId = $task->progress_status_id;
        $task->update(['progress_status_id' => $validated['progress_status_id']]);
        
        // 新しいステータスを取得
        $task->load('progressStatus');
        $newStatus = $task->progressStatus;

        // 履歴を記録
        $this->recordHistory(
            $task, 
            'status_changed', 
            ['progress_status_id' => $oldStatusId], 
            ['progress_status_id' => $validated['progress_status_id']], 
            $validated['changed_by_member_id'],
            $validated['comment']
        );

        // Slack通知を送信
        if ($oldStatus && $newStatus) {
            $changedByMember = null;
            if ($validated['changed_by_member_id']) {
                $changedByMember = Member::find($validated['changed_by_member_id']);
            }
            
            $slackService->sendStatusChangeNotification(
                $task, 
                $oldStatus->name, 
                $newStatus->name, 
                $changedByMember
            );
        }

        return redirect()->back()
                        ->with('success', 'タスクのステータスが更新されました。');
    }

    /**
     * タスクの担当者を変更
     */
    public function assignMember(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_member_id' => 'nullable|exists:members,id',
            'changed_by_member_id' => 'nullable|exists:members,id',
            'comment' => 'nullable|string',
        ]);

        $oldMemberId = $task->assigned_member_id;
        $task->update(['assigned_member_id' => $validated['assigned_member_id']]);

        // 履歴を記録
        $this->recordHistory(
            $task, 
            'assigned', 
            ['assigned_member_id' => $oldMemberId], 
            ['assigned_member_id' => $validated['assigned_member_id']], 
            $validated['changed_by_member_id'],
            $validated['comment']
        );

        return redirect()->back()
                        ->with('success', 'タスクの担当者が変更されました。');
    }

    /**
     * タスク履歴を記録
     */
    private function recordHistory(
        Task $task, 
        string $action, 
        ?array $oldValues, 
        ?array $newValues, 
        ?int $changedByMemberId, 
        ?string $comment = null
    ): void {
        TaskHistory::create([
            'task_id' => $task->id,
            'changed_by_member_id' => $changedByMemberId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'comment' => $comment,
        ]);
    }
}
