@extends('layouts.app')

@section('title', 'イベント一覧')

@push('styles')
<style>
.event-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.25);
}

.progress-custom {
    height: 8px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.progress-custom .progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.table-custom {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.table-custom thead th {
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e7f3 100%);
    border: none;
    color: #5a67d8;
    font-weight: 600;
    padding: 1rem;
}

.table-custom tbody tr {
    border: none;
    transition: all 0.2s ease;
}

.table-custom tbody tr:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3e7f3 100%);
    transform: scale(1.01);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 500;
    font-size: 0.875rem;
}

.btn-action {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
                        <i class="fas fa-calendar-alt me-3"></i>
                        イベント一覧
                    </h1>
                    <p class="page-subtitle">プロジェクトイベントを管理・監視します</p>
                </div>
                <a href="{{ route('events.create') }}" class="btn-custom btn-custom-primary">
                    <i class="fas fa-plus me-2"></i>
                    新しいイベント
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="custom-card">
            <div class="custom-card-body p-0">
                @if($events->count() > 0)
                    <div class="table-responsive">
                        <table class="table-custom table mb-0">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-project-diagram me-2"></i>イベント名</th>
                                    <th><i class="fas fa-calendar-week me-2"></i>期間</th>
                                    <th><i class="fas fa-users me-2"></i>チーム数</th>
                                    <th><i class="fas fa-tasks me-2"></i>タスク数</th>
                                    <th><i class="fas fa-chart-line me-2"></i>進捗</th>
                                    <th><i class="fas fa-signal me-2"></i>状態</th>
                                    <th><i class="fas fa-clock me-2"></i>作成日</th>
                                    <th><i class="fas fa-cogs me-2"></i>アクション</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                    <tr class="@if(!$event->is_active) opacity-75 @endif">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="event-icon me-3">
                                                    <i class="fas fa-calendar-alt text-primary"></i>
                                                </div>
                                                <div>
                                                    <a href="{{ route('events.show', $event) }}" class="text-decoration-none fw-bold event-link">
                                                        {{ $event->name }}
                                                    </a>
                                                    @if($event->description)
                                                        <div class="event-description">
                                                            {{ Str::limit($event->description, 50) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="date-info">
                                                <div class="date-start">
                                                    <i class="fas fa-play-circle me-1 text-success"></i>
                                                    {{ $event->start_date->format('Y/m/d') }}
                                                </div>
                                                @if($event->end_date)
                                                    <div class="date-end">
                                                        <i class="fas fa-stop-circle me-1 text-danger"></i>
                                                        {{ $event->end_date->format('Y/m/d') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="count-badge count-badge-info">
                                                {{ $event->teams->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="count-badge count-badge-secondary">
                                                {{ $event->tasks->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $totalTasks = $event->tasks->count();
                                                $completedTasks = $event->tasks->whereIn('progress_status_id', 
                                                    $event->progressStatuses->where('is_completed', true)->pluck('id'))->count();
                                                $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                            @endphp
                                            <div class="progress progress-custom mb-1">
                                                <div class="progress-bar 
                                                    @if($progressPercentage >= 80) bg-success
                                                    @elseif($progressPercentage >= 50) bg-info
                                                    @elseif($progressPercentage >= 20) bg-warning
                                                    @else bg-danger @endif" 
                                                    style="width: {{ $progressPercentage }}%">
                                                </div>
                                            </div>
                                            <div class="progress-text">
                                                <strong>{{ $progressPercentage }}%</strong>
                                                <small class="text-muted">({{ $completedTasks }}/{{ $totalTasks }})</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($event->is_active)
                                                @php
                                                    $today = now()->toDateString();
                                                    $isCurrentlyRunning = $event->start_date->toDateString() <= $today && 
                                                                        (!$event->end_date || $event->end_date->toDateString() >= $today);
                                                @endphp
                                                @if($isCurrentlyRunning)
                                                    <span class="status-badge bg-success text-white">
                                                        <i class="fas fa-play me-1"></i>進行中
                                                    </span>
                                                @elseif($event->start_date->isFuture())
                                                    <span class="status-badge bg-primary text-white">
                                                        <i class="fas fa-hourglass-start me-1"></i>開始前
                                                    </span>
                                                @else
                                                    <span class="status-badge bg-info text-white">
                                                        <i class="fas fa-check-circle me-1"></i>終了
                                                    </span>
                                                @endif
                                            @else
                                                <span class="status-badge bg-secondary text-white">
                                                    <i class="fas fa-pause me-1"></i>非アクティブ
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="created-date">
                                                {{ $event->created_at->format('Y/m/d') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('events.show', $event) }}" class="btn-action btn-outline-primary" title="詳細表示">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('events.edit', $event) }}" class="btn-action btn-outline-secondary" title="編集">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('tasks.create', ['event_id' => $event->id]) }}" class="btn-action btn-outline-success" title="タスク追加">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- ページネーション -->
                    <div class="custom-card-footer">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-alt fa-4x mb-4 text-primary"></i>
                        <h4 class="empty-title">イベントがありません</h4>
                        <p class="empty-message">新しいイベントを作成してプロジェクトを開始しましょう。</p>
                        <a href="{{ route('events.create') }}" class="btn-custom btn-custom-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            最初のイベントを作成
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 統計情報 -->
@if($events->count() > 0)
<div class="row mt-4 g-4">
    <div class="col-md-3">
        <div class="stats-card stats-card-primary">
            <div class="stats-card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-calendar-alt fa-2x"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $events->total() }}</h3>
                    <p class="stats-label">総イベント数</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card stats-card-success">
            <div class="stats-card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-play-circle fa-2x"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $events->where('is_active', true)->count() }}</h3>
                    <p class="stats-label">アクティブ</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card stats-card-info">
            <div class="stats-card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $events->sum(function($event) { return $event->teams->count(); }) }}</h3>
                    <p class="stats-label">総チーム数</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card stats-card-warning">
            <div class="stats-card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-tasks fa-2x"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $events->sum(function($event) { return $event->tasks->count(); }) }}</h3>
                    <p class="stats-label">総タスク数</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
