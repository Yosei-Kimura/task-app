@extends('layouts.app')

@section('title', $event->name . ' - イベント詳細')

@section('content')
<style>
.event-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    margin-bottom: 2rem;
}

.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.stats-card.teams {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.stats-card.tasks {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
}

.stats-card.completed {
    background: linear-gradient(135deg, #43e97b, #38f9d7);
    color: white;
}

.stats-card.overdue {
    background: linear-gradient(135deg, #fa709a, #fee140);
    color: white;
}

.info-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.team-item {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.team-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.task-item {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
    border-left: 4px solid #dee2e6;
}

.task-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.task-item.overdue {
    border-left-color: #dc3545;
    background: rgba(220, 53, 69, 0.05);
}

.progress-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: bold;
    margin: 0 auto;
}

.breadcrumb-custom {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    backdrop-filter: blur(10px);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}
</style>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ダッシュボード</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">イベント一覧</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($event->name, 30) }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- イベントヘッダー -->
<div class="card event-header-card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{ $event->name }}
                </h1>
                @if($event->description)
                    <p class="mb-3 opacity-75">{{ $event->description }}</p>
                @endif
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <!-- 期間表示 -->
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-2"></i>
                        <div>
                            <div>{{ $event->start_date->format('Y/m/d') }} - 
                                @if($event->end_date)
                                    {{ $event->end_date->format('Y/m/d') }}
                                @else
                                    未設定
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- ステータスバッジ -->
                    <span class="status-badge @if($event->is_active) bg-success bg-opacity-75 @else bg-secondary bg-opacity-75 @endif text-white">
                        @if($event->is_active)
                            <i class="fas fa-play-circle me-1"></i>
                            アクティブ
                        @else
                            <i class="fas fa-pause-circle me-1"></i>
                            非アクティブ
                        @endif
                    </span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('events.edit', $event) }}" class="btn btn-light btn-lg">
                        <i class="fas fa-edit me-2"></i>
                        編集
                    </a>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-light btn-lg dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-plus me-2"></i>
                            追加
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('teams.create') }}?event_id={{ $event->id }}">
                                <i class="fas fa-users me-2"></i>チーム追加
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('tasks.create') }}?event_id={{ $event->id }}">
                                <i class="fas fa-tasks me-2"></i>タスク追加
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 統計ダッシュボード -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card teams">
            <div class="card-body text-center py-4">
                <div class="progress-circle mb-2" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="mb-1">{{ $event->teams->count() }}</h3>
                <p class="mb-0">チーム</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card tasks">
            <div class="card-body text-center py-4">
                <div class="progress-circle mb-2" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="mb-1">{{ $event->tasks->count() }}</h3>
                <p class="mb-0">タスク</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card completed">
            <div class="card-body text-center py-4">
                @php
                    $completedTasks = $event->tasks->whereIn('progress_status_id', 
                        $event->progressStatuses->where('is_completed', true)->pluck('id'))->count();
                    $totalTasks = $event->tasks->count();
                    $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp
                <div class="progress-circle mb-2" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="mb-1">{{ $completedTasks }}</h3>
                <p class="mb-0">完了済み ({{ $completionRate }}%)</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card overdue">
            <div class="card-body text-center py-4">
                @php
                    $overdueTasks = $event->tasks->filter(function($task) {
                        return $task->isOverdue();
                    })->count();
                @endphp
                <div class="progress-circle mb-2" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="mb-1">{{ $overdueTasks }}</h3>
                <p class="mb-0">期限切れ</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- チーム一覧 -->
    <div class="col-lg-6">
        <div class="card info-card mb-4">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-users me-2"></i>
                        チーム一覧 ({{ $event->teams->count() }}個)
                    </h5>
                    <a href="{{ route('teams.create') }}?event_id={{ $event->id }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        追加
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                @if($event->teams->count() > 0)
                    @foreach($event->teams->sortBy('created_at') as $team)
                        <div class="team-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="team-color-indicator me-3" style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ $team->color }};"></div>
                                        <h6 class="mb-0">
                                            <a href="{{ route('teams.show', $team) }}" class="text-decoration-none fw-semibold">
                                                {{ $team->name }}
                                            </a>
                                        </h6>
                                    </div>
                                    @if($team->description)
                                        <p class="mb-2 small text-muted">{{ Str::limit($team->description, 60) }}</p>
                                    @endif
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="small text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $team->members->count() }}名
                                        </span>
                                        <span class="small text-muted">
                                            <i class="fas fa-tasks me-1"></i>
                                            {{ $team->tasks->count() }}タスク
                                        </span>
                                        @if($team->tasks->count() > 0)
                                            @php
                                                $teamCompletedTasks = $team->tasks->whereIn('progress_status_id', 
                                                    $event->progressStatuses->where('is_completed', true)->pluck('id'))->count();
                                                $teamCompletionRate = round(($teamCompletedTasks / $team->tasks->count()) * 100);
                                            @endphp
                                            <span class="small text-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                {{ $teamCompletionRate }}%完了
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-primary btn-sm" title="詳細">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.create') }}?team_id={{ $team->id }}" class="btn btn-outline-success btn-sm" title="タスク追加">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted">チームがまだありません</h6>
                        <p class="text-muted small">最初のチームを作成してプロジェクトを開始しましょう</p>
                        <a href="{{ route('teams.create') }}?event_id={{ $event->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            最初のチームを作成
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 最近のタスク -->
    <div class="col-lg-6">
        <div class="card info-card mb-4">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-tasks me-2"></i>
                        最近のタスク
                    </h5>
                    <a href="{{ route('tasks.index') }}?event_id={{ $event->id }}" class="btn btn-light btn-sm">
                        すべて見る
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                @if($event->tasks->count() > 0)
                    @foreach($event->tasks->sortByDesc('created_at')->take(5) as $task)
                        <div class="task-item @if($task->isOverdue()) overdue @endif">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="task-priority-indicator me-2" style="width: 4px; height: 30px; border-radius: 2px; background-color: 
                                            @if($task->priority >= 4) #dc3545
                                            @elseif($task->priority >= 3) #ffc107
                                            @else #6c757d @endif;">
                                        </div>
                                        <h6 class="mb-0">
                                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-semibold">
                                                {{ Str::limit($task->title, 30) }}
                                            </a>
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge px-2 py-1" style="background-color: {{ $task->team->color }}; border-radius: 12px; font-size: 0.75rem;">
                                            {{ $task->team->name }}
                                        </span>
                                        @if($task->assignedMember)
                                            <span class="small text-muted">{{ $task->assignedMember->name }}</span>
                                        @endif
                                    </div>
                                    @if($task->due_date)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock me-1 @if($task->isOverdue()) text-danger @else text-muted @endif"></i>
                                            <small class="@if($task->isOverdue()) text-danger fw-bold @else text-muted @endif">
                                                {{ $task->due_date->format('m/d H:i') }}
                                                @if($task->isOverdue())
                                                    (期限切れ)
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    @if($task->progressStatus)
                                        <span class="badge px-2 py-1" style="background-color: {{ $task->progressStatus->color }}; border-radius: 12px; font-size: 0.75rem;">
                                            {{ $task->progressStatus->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1" style="border-radius: 12px; font-size: 0.75rem;">
                                            未設定
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($event->tasks->count() > 5)
                        <div class="text-center pt-2 border-top">
                            <a href="{{ route('tasks.index') }}?event_id={{ $event->id }}" class="btn btn-outline-primary btn-sm">
                                他{{ $event->tasks->count() - 5 }}件を見る
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-3x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted">タスクがまだありません</h6>
                        <p class="text-muted small">最初のタスクを作成してプロジェクトを開始しましょう</p>
                        <a href="{{ route('tasks.create') }}?event_id={{ $event->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            最初のタスクを作成
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 進捗状況設定と期間情報 -->
<div class="row">
    <div class="col-lg-8">
        <div class="card info-card">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-list-check me-2"></i>
                    進捗状況設定
                </h5>
            </div>
            <div class="card-body">
                @if($event->progressStatuses->count() > 0)
                    <div class="row">
                        @foreach($event->progressStatuses->sortBy('order') as $status)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="status-item p-3 rounded" style="background: rgba({{ hexdec(substr($status->color, 1, 2)) }}, {{ hexdec(substr($status->color, 3, 2)) }}, {{ hexdec(substr($status->color, 5, 2)) }}, 0.1); border: 2px solid {{ $status->color }};">
                                    <div class="d-flex align-items-center">
                                        <div class="status-color me-2" style="width: 16px; height: 16px; border-radius: 50%; background-color: {{ $status->color }};"></div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-semibold">{{ $status->name }}</h6>
                                            <small class="text-muted">順序: {{ $status->order }}</small>
                                        </div>
                                        @if($status->is_completed)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-list-check fa-3x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted">進捗状況が設定されていません</h6>
                        <p class="text-muted small">プロジェクトの進捗を管理するためのステータスを設定しましょう</p>
                        <a href="{{ route('progress-statuses.index') }}?event_id={{ $event->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            ステータスを設定
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card info-card">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #fa709a, #fee140);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-info-circle me-2"></i>
                    イベント詳細
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-play-circle me-2 text-success"></i>
                        <strong class="text-muted">開始日</strong>
                    </div>
                    <div class="fw-semibold">{{ $event->start_date->format('Y年m月d日') }}</div>
                    <small class="text-muted">{{ $event->start_date->diffForHumans() }}</small>
                </div>

                <div class="info-item mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-stop-circle me-2 text-danger"></i>
                        <strong class="text-muted">終了日</strong>
                    </div>
                    @if($event->end_date)
                        <div class="fw-semibold">{{ $event->end_date->format('Y年m月d日') }}</div>
                        <small class="text-muted">{{ $event->end_date->diffForHumans() }}</small>
                    @else
                        <div class="text-muted">設定なし</div>
                    @endif
                </div>

                <div class="info-item mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-calendar-plus me-2 text-info"></i>
                        <strong class="text-muted">作成日</strong>
                    </div>
                    <div class="fw-semibold">{{ $event->created_at->format('Y年m月d日') }}</div>
                    <small class="text-muted">{{ $event->created_at->diffForHumans() }}</small>
                </div>

                @if($event->tasks->count() > 0)
                    <div class="border-top pt-3">
                        <div class="text-center">
                            <small class="text-muted d-block mb-2">プロジェクト進捗</small>
                            @php
                                $totalTasks = $event->tasks->count();
                                $completedTasks = $event->tasks->whereIn('progress_status_id', 
                                    $event->progressStatuses->where('is_completed', true)->pluck('id'))->count();
                                $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                            @endphp
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $completionRate }}%"></div>
                            </div>
                            <div class="fw-bold text-success">{{ $completionRate }}% 完了</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
