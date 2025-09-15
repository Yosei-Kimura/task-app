@extends('layouts.app')

@section('title', 'タスク一覧')

@push('styles')
<style>
.task-card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.task-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.task-card.overdue {
    border-left: 5px solid #dc3545;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.2);
}

.task-card.high-priority {
    border-left: 5px solid #fd7e14;
    box-shadow: 0 8px 25px rgba(253, 126, 20, 0.2);
}

.task-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
}

.task-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    color: white;
}

.task-meta {
    font-size: 0.85rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

.priority-badge {
    border-radius: 20px;
    padding: 0.4rem 0.8rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge {
    border-radius: 15px;
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.assignee-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.assignee-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
    color: white;
}

.task-stats {
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e7f3 100%);
    border-radius: 15px;
    padding: 1rem;
    margin: 1rem 0;
}

.filter-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    border: none;
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.filter-card .card-body {
    background: rgba(255, 255, 255, 0.95);
    margin: 1rem;
    border-radius: 15px;
}

.filter-card .form-select,
.filter-card .form-label {
    color: #2d3748;
}

.task-table {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.task-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.task-table thead th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.task-table tbody tr {
    transition: all 0.3s ease;
}

.task-table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: scale(1.01);
}

.task-table tbody td {
    padding: 1rem;
    border: none;
    vertical-align: middle;
}

.action-btn {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.action-btn:hover {
    transform: scale(1.1);
}

.deadline-warning {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-tasks me-3"></i>
                        タスク一覧
                    </h1>
                    <p class="page-subtitle">プロジェクトタスクを効率的に管理</p>
                </div>
                <a href="{{ route('tasks.create') }}" class="btn-custom btn-custom-primary">
                    <i class="fas fa-plus me-2"></i>
                    新しいタスク
                </a>
            </div>
        </div>
    </div>
</div>

<!-- フィルター -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-header text-center">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    タスクフィルター
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('tasks.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="event_id" class="form-label fw-bold">
                                <i class="fas fa-calendar me-2"></i>
                                イベント
                            </label>
                            <select name="event_id" id="event_id" class="form-select">
                                <option value="">すべてのイベント</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" 
                                            @if(request('event_id') == $event->id) selected @endif>
                                        {{ $event->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="team_id" class="form-label fw-bold">
                                <i class="fas fa-users me-2"></i>
                                チーム
                            </label>
                            <select name="team_id" id="team_id" class="form-select">
                                <option value="">すべてのチーム</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" 
                                            @if(request('team_id') == $team->id) selected @endif>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_id" class="form-label fw-bold">
                                <i class="fas fa-flag me-2"></i>
                                ステータス
                            </label>
                            <select name="status_id" id="status_id" class="form-select">
                                <option value="">すべてのステータス</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" 
                                            @if(request('status_id') == $status->id) selected @endif>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="assigned_member_id" class="form-label fw-bold">
                                <i class="fas fa-user-tag me-2"></i>
                                担当者
                            </label>
                            <select name="assigned_member_id" id="assigned_member_id" class="form-select">
                                <option value="">すべての担当者</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" 
                                            @if(request('assigned_member_id') == $member->id) selected @endif>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-center gap-3">
                            <button type="submit" class="btn-custom btn-custom-primary">
                                <i class="fas fa-search me-2"></i>
                                検索
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn-custom btn-custom-secondary">
                                <i class="fas fa-redo me-2"></i>
                                リセット
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- タスク一覧 -->
<div class="row">
    <div class="col-12">
        <div class="card task-table">
            @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>タスク名</th>
                                <th>イベント</th>
                                <th>チーム</th>
                                <th>担当者</th>
                                <th>ステータス</th>
                                <th>優先度</th>
                                <th>期限</th>
                                <th>作成日</th>
                                <th>アクション</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr class="@if($task->isOverdue()) bg-light border-start border-danger border-4 @endif">
                                    <td>
                                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-bold text-primary">
                                            {{ $task->title }}
                                        </a>
                                        @if($task->description)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-white">
                                            {{ $task->event->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $task->team->color }}; color: white;">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $task->team->name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->assignedMember)
                                            <div class="assignee-info">
                                                <div class="assignee-avatar" style="background: linear-gradient(135deg, {{ $task->team->color ?? '#667eea' }} 0%, {{ $task->team->color ? $task->team->color . '80' : '#764ba2' }} 100%);">
                                                    {{ mb_substr($task->assignedMember->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold">{{ $task->assignedMember->name }}</span>
                                                    @if($task->assignedMember->hasSlackAccount())
                                                        <br><i class="fab fa-slack text-success" title="Slack連携済み"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash me-1"></i>
                                                未割当
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->progressStatus)
                                            <span class="status-badge" style="background-color: {{ $task->progressStatus->color }}">
                                                {{ $task->progressStatus->name }}
                                            </span>
                                        @else
                                            <span class="status-badge bg-secondary">未設定</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="priority-badge 
                                            @if($task->priority >= 4) bg-danger text-white
                                            @elseif($task->priority >= 3) bg-warning text-dark
                                            @else bg-secondary text-white @endif">
                                            @if($task->priority >= 4)
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                            @elseif($task->priority >= 3)
                                                <i class="fas fa-exclamation me-1"></i>
                                            @else
                                                <i class="fas fa-minus me-1"></i>
                                            @endif
                                            {{ $task->priority_text }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->due_date)
                                            <div class="@if($task->isOverdue()) text-danger fw-bold deadline-warning @endif">
                                                @if($task->isOverdue())
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                @elseif($task->due_date->diffInDays() <= 3)
                                                    <i class="fas fa-clock me-1 text-warning"></i>
                                                @else
                                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                                @endif
                                                <small>{{ $task->due_date->format('Y/m/d H:i') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-calendar-times me-1"></i>
                                                期限なし
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-plus me-1"></i>
                                            {{ $task->created_at->format('Y/m/d') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('tasks.show', $task) }}" 
                                               class="action-btn btn btn-outline-primary" 
                                               title="詳細表示">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tasks.edit', $task) }}" 
                                               class="action-btn btn btn-outline-success" 
                                               title="編集">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- ページネーション -->
                @if($tasks->hasPages())
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-center">
                        {{ $tasks->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            @else
                <div class="card-body text-center py-5">
                    <i class="fas fa-tasks fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">タスクがありません</h4>
                    @if(request()->hasAny(['event_id', 'team_id', 'status_id', 'assigned_member_id']))
                        <p class="text-muted mb-4">フィルター条件を変更してみてください。</p>
                        <a href="{{ route('tasks.index') }}" class="btn-custom btn-custom-secondary me-3">
                            <i class="fas fa-redo me-2"></i>
                            フィルターをリセット
                        </a>
                    @else
                        <p class="text-muted mb-4">新しいタスクを作成してプロジェクトを開始しましょう。</p>
                    @endif
                    <a href="{{ route('tasks.create') }}" class="btn-custom btn-custom-primary">
                        <i class="fas fa-plus me-2"></i>
                        最初のタスクを作成
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- 統計情報 -->
@if($tasks->count() > 0)
<div class="row mt-5">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="stats-number text-primary">{{ $tasks->total() }}</h3>
                <p class="stats-label">総タスク数</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="stats-number text-success">
                    {{ $tasks->filter(function($task) { 
                        return $task->progressStatus && $task->progressStatus->name === '完了'; 
                    })->count() }}
                </h3>
                <p class="stats-label">完了済み</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="stats-number text-warning">
                    {{ $tasks->filter(function($task) { 
                        return $task->progressStatus && $task->progressStatus->name === '進行中'; 
                    })->count() }}
                </h3>
                <p class="stats-label">進行中</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="stats-number text-danger">
                    {{ $tasks->filter(function($task) { 
                        return $task->isOverdue(); 
                    })->count() }}
                </h3>
                <p class="stats-label">期限切れ</p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
