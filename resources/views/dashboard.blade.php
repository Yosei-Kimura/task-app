@extends('layouts.app')

@section('title', 'ダッシュボード')

@push('styles')
<style>
/* ダッシュボード専用スタイル */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 30px;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
}

.stats-card {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: none;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 20px;
}

.stats-icon.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-icon.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stats-icon.warning {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.stats-icon.danger {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}

.stats-label {
    color: #6c757d;
    font-weight: 600;
    margin-top: 8px;
    margin-bottom: 0;
}

.recent-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
}

.recent-item {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border-left: 4px solid #667eea;
}

.recent-item:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.recent-item:last-child {
    margin-bottom: 0;
}

.priority-high {
    border-left-color: #dc3545 !important;
}

.priority-medium {
    border-left-color: #ffc107 !important;
}

.priority-low {
    border-left-color: #28a745 !important;
}

.quick-actions {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
}

.quick-action-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    padding: 15px 20px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    margin-bottom: 15px;
    font-weight: 600;
}

.quick-action-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

.quick-action-btn:last-child {
    margin-bottom: 0;
}

.progress-overview {
    background: white;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.progress-bar-custom {
    height: 12px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
    margin-bottom: 15px;
}

.progress-bar-custom .progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 30px 20px;
        text-align: center;
    }
    
    .stats-card {
        margin-bottom: 20px;
        padding: 20px;
    }
    
    .stats-number {
        font-size: 2rem;
    }
    
    .recent-section {
        padding: 20px;
    }
    
    .quick-actions {
        padding: 20px;
    }
}
</style>
@endpush

@section('content')
<!-- ヘッダーセクション -->
<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="display-4 mb-3">
                <i class="fas fa-tachometer-alt me-3"></i>
                ダッシュボード
            </h1>
            <p class="lead mb-0">プロジェクトの進捗状況と概要を確認できます</p>
        </div>
        <div class="col-lg-4 text-end">
            <p class="mb-1">今日の日付</p>
            <h4>{{ now()->format('Y年m月d日') }}</h4>
            <small>{{ now()->format('l') }}</small>
        </div>
    </div>
</div>

<!-- 統計情報カード -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card stats-card-primary">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $stats['total_events'] }}</h3>
                    <p class="stats-label">アクティブイベント</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card stats-card-info">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $stats['total_tasks'] }}</h3>
                    <p class="stats-label">総タスク数</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card stats-card-success">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $stats['completed_tasks'] }}</h3>
                    <p class="stats-label">完了タスク</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card stats-card-danger">
            <div class="stats-card-body">
                <div class="stats-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $stats['overdue_tasks'] }}</h3>
                    <p class="stats-label">期限切れ</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- 緊急タスク -->
    <div class="col-lg-6">
        <div class="custom-card card-danger">
            <div class="custom-card-header">
                <h5 class="custom-card-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    期限切れタスク ({{ $overdueTasks->count() }}件)
                </h5>
            </div>
            <div class="custom-card-body">
                @if($overdueTasks->count() > 0)
                    <div class="task-list">
                        @foreach($overdueTasks->take(5) as $task)
                            <div class="task-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="task-details">
                                        <h6 class="task-title">
                                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                                {{ $task->title }}
                                            </a>
                                        </h6>
                                        <p class="task-meta">
                                            <i class="fas fa-users me-1"></i>{{ $task->team->name }}
                                            @if($task->assignedMember)
                                                <span class="separator">・</span>
                                                <i class="fas fa-user me-1"></i>{{ $task->assignedMember->name }}
                                            @else
                                                <span class="separator">・</span>
                                                <i class="fas fa-user-slash me-1"></i>未割当
                                            @endif
                                        </p>
                                        <div class="task-deadline">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $task->due_date->format('Y/m/d H:i') }}
                                        </div>
                                    </div>
                                    @if($task->progressStatus)
                                        <span class="status-badge" style="background-color: {{ $task->progressStatus->color }}">
                                            {{ $task->progressStatus->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($overdueTasks->count() > 5)
                        <div class="custom-card-footer">
                            <a href="{{ route('tasks.index') }}?overdue=1" class="btn-custom btn-custom-danger">
                                すべて見る (残り{{ $overdueTasks->count() - 5 }}件)
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p class="empty-message">期限切れのタスクはありません</p>
                        <p class="empty-description">すべてのタスクが適切に管理されています</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 今日・明日のタスク -->
    <div class="col-lg-6">
        <div class="custom-card card-warning">
            <div class="custom-card-header">
                <h5 class="custom-card-title">
                    <i class="fas fa-calendar-day me-2"></i>
                    今日・明日のタスク
                </h5>
            </div>
            <div class="custom-card-body">
                @if($todayTasks->count() > 0 || $tomorrowTasks->count() > 0)
                    <div class="task-list">
                        @if($todayTasks->count() > 0)
                            <div class="task-section-header">
                                <i class="fas fa-calendar-day me-2 text-primary"></i>
                                <strong class="text-primary">今日 ({{ $todayTasks->count() }}件)</strong>
                            </div>
                            @foreach($todayTasks->take(3) as $task)
                                <div class="task-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="task-details">
                                            <h6 class="task-title">
                                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                                    {{ $task->title }}
                                                </a>
                                            </h6>
                                            <p class="task-meta">
                                                <i class="fas fa-users me-1"></i>{{ $task->team->name }}
                                                @if($task->assignedMember)
                                                    <span class="separator">・</span>
                                                    <i class="fas fa-user me-1"></i>{{ $task->assignedMember->name }}
                                                @else
                                                    <span class="separator">・</span>
                                                    <i class="fas fa-user-slash me-1"></i>未割当
                                                @endif
                                            </p>
                                        </div>
                                        @if($task->progressStatus)
                                            <span class="status-badge" style="background-color: {{ $task->progressStatus->color }}">
                                                {{ $task->progressStatus->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($tomorrowTasks->count() > 0)
                            <div class="task-section-header">
                                <i class="fas fa-calendar-plus me-2 text-info"></i>
                                <strong class="text-info">明日 ({{ $tomorrowTasks->count() }}件)</strong>
                            </div>
                            @foreach($tomorrowTasks->take(3) as $task)
                                <div class="task-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="task-details">
                                            <h6 class="task-title">
                                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                                    {{ $task->title }}
                                                </a>
                                            </h6>
                                            <p class="task-meta">
                                                <i class="fas fa-users me-1"></i>{{ $task->team->name }}
                                                @if($task->assignedMember)
                                                    <span class="separator">・</span>
                                                    <i class="fas fa-user me-1"></i>{{ $task->assignedMember->name }}
                                                @else
                                                    <span class="separator">・</span>
                                                    <i class="fas fa-user-slash me-1"></i>未割当
                                                @endif
                                            </p>
                                        </div>
                                        @if($task->progressStatus)
                                            <span class="status-badge" style="background-color: {{ $task->progressStatus->color }}">
                                                {{ $task->progressStatus->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <p class="empty-message">今日・明日期限のタスクはありません</p>
                        <p class="empty-description">お疲れ様でした！</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 現在のイベント -->
@if($currentEvents->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="custom-card card-primary">
            <div class="custom-card-header">
                <h5 class="custom-card-title">
                    <i class="fas fa-calendar-alt me-2"></i>
                    現在進行中のイベント
                </h5>
            </div>
            <div class="custom-card-body">
                <div class="row g-3">
                    @foreach($currentEvents as $event)
                        <div class="col-md-6 col-lg-4">
                            <div class="event-card">
                                <div class="event-card-header">
                                    <h6 class="event-title">
                                        <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
                                            {{ $event->name }}
                                        </a>
                                    </h6>
                                </div>
                                <div class="event-card-body">
                                    <p class="event-description">
                                        {{ $event->description }}
                                    </p>
                                    <div class="event-stats">
                                        <div class="stat-item">
                                            <i class="fas fa-users me-1"></i>
                                            <span>{{ $event->teams->count() }}チーム</span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-tasks me-1"></i>
                                            <span>{{ $event->tasks->count() }}タスク</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- クイックアクション -->
<div class="row mt-4">
    <div class="col-12">
        <div class="custom-card card-info">
            <div class="custom-card-header">
                <h5 class="custom-card-title">
                    <i class="fas fa-bolt me-2"></i>
                    クイックアクション
                </h5>
            </div>
            <div class="custom-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('events.create') }}" class="btn-custom btn-custom-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            新しいイベント
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('teams.create') }}" class="btn-custom btn-custom-info w-100">
                            <i class="fas fa-users-plus me-2"></i>
                            新しいチーム
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('members.create') }}" class="btn-custom btn-custom-success w-100">
                            <i class="fas fa-user-plus me-2"></i>
                            新しいメンバー
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('tasks.create') }}" class="btn-custom btn-custom-warning w-100">
                            <i class="fas fa-tasks me-2"></i>
                            新しいタスク
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
