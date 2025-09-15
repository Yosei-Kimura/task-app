@extends('layouts.app')

@section('title', 'チーム一覧')

@push('styles')
<style>
.team-card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
    overflow: hidden;
    height: 100%;
}

.team-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.team-card-header {
    background: linear-gradient(135deg, var(--team-color, #667eea) 0%, var(--team-color-dark, #764ba2) 100%);
    color: white;
    border: none;
    padding: 1.5rem;
    position: relative;
}

.team-card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.team-card-header > * {
    position: relative;
    z-index: 1;
}

.team-badge {
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white !important;
    font-weight: 600;
}

.member-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.75rem;
    margin: 2px;
    transition: transform 0.2s ease;
}

.member-avatar:hover {
    transform: scale(1.1);
}

.task-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.task-item:last-child {
    border-bottom: none;
}

.team-stats {
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e7f3 100%);
    border-radius: 15px;
    padding: 1rem;
    margin: 1rem 0;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    color: #667eea;
}

.quick-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.quick-action-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    transform: scale(1.1);
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
                        <i class="fas fa-users me-3"></i>
                        チーム一覧
                    </h1>
                    <p class="page-subtitle">プロジェクトチームを管理・監視します</p>
                </div>
                <a href="{{ route('teams.create') }}" class="btn-custom btn-custom-primary">
                    <i class="fas fa-plus me-2"></i>
                    新しいチーム
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @if($teams->count() > 0)
        @foreach($teams as $team)
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <div class="team-card-header" style="--team-color: {{ $team->color }}; --team-color-dark: {{ $team->color }}dd">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="team-badge badge">
                                    <i class="fas fa-users me-2"></i>
                                    {{ $team->name }}
                                </span>
                                <div class="mt-2">
                                    <small class="text-white-50">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $team->event->name }}
                                    </small>
                                </div>
                            </div>
                            <div class="quick-actions">
                                <a href="{{ route('teams.show', $team) }}" class="quick-action-btn btn btn-outline-light">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('teams.edit', $team) }}" class="quick-action-btn btn btn-outline-light">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @if($team->description)
                            <p class="card-text text-muted mb-3">{{ Str::limit($team->description, 100) }}</p>
                        @endif

                        <div class="team-stats">
                            <div class="row">
                                <div class="col-6 stat-item">
                                    <div class="stat-number">{{ $team->members->count() }}</div>
                                    <small class="text-muted">メンバー</small>
                                </div>
                                <div class="col-6 stat-item">
                                    <div class="stat-number">{{ $team->tasks->count() }}</div>
                                    <small class="text-muted">タスク</small>
                                </div>
                            </div>
                        </div>

                        <!-- メンバー一覧（アバター風） -->
                        @if($team->members->count() > 0)
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-user-friends me-2 text-primary"></i>
                                    メンバー
                                </h6>
                                <div class="d-flex flex-wrap">
                                    @foreach($team->members->take(6) as $member)
                                        <div class="member-avatar" 
                                             style="background-color: {{ $team->color }}; color: white;" 
                                             title="{{ $member->name }}{{ $member->role === 'leader' ? ' (リーダー)' : '' }}">
                                            {{ mb_substr($member->name, 0, 2) }}
                                            @if($member->role === 'leader')
                                                <i class="fas fa-crown position-absolute" style="top: -5px; right: -5px; font-size: 0.6rem; color: #ffd700;"></i>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($team->members->count() > 6)
                                        <div class="member-avatar bg-light text-dark">
                                            +{{ $team->members->count() - 6 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- 最近のタスク -->
                        @if($team->tasks->count() > 0)
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-tasks me-2 text-success"></i>
                                    最近のタスク
                                </h6>
                                <div class="task-list">
                                    @foreach($team->tasks->sortByDesc('created_at')->take(3) as $task)
                                        <div class="task-item">
                                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none small fw-bold">
                                                {{ Str::limit($task->title, 35) }}
                                            </a>
                                            @if($task->progressStatus)
                                                <span class="badge ms-2" style="background-color: {{ $task->progressStatus->color }}; font-size: 0.65em;">
                                                    {{ $task->progressStatus->name }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                作成: {{ $team->created_at->format('Y/m/d') }}
                            </small>
                            <div class="d-flex gap-2">
                                <a href="{{ route('members.create') }}?team_id={{ $team->id }}" 
                                   class="btn btn-outline-success btn-sm" 
                                   title="メンバー追加">
                                    <i class="fas fa-user-plus"></i>
                                </a>
                                <a href="{{ route('tasks.create') }}?team_id={{ $team->id }}" 
                                   class="btn btn-outline-primary btn-sm" 
                                   title="タスク追加">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="custom-card">
                <div class="custom-card-body">
                    <div class="empty-state">
                        <i class="fas fa-users fa-4x mb-4 text-primary"></i>
                        <h4 class="empty-title">チームがありません</h4>
                        <p class="empty-message">新しいチームを作成してプロジェクトを開始しましょう。</p>
                        <a href="{{ route('teams.create') }}" class="btn-custom btn-custom-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            最初のチームを作成
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- ページネーション -->
@if($teams->hasPages())
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $teams->links() }}
        </div>
    </div>
</div>
@endif

<!-- 統計情報 -->
@if($teams->count() > 0)
<div class="row mt-5 g-4">
    <div class="col-md-3">
        <div class="stats-card stats-card-info">
            <div class="stats-card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $teams->total() }}</h3>
                    <p class="stats-label">総チーム数</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card stats-card-success">
            <div class="stats-card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-user-friends fa-2x"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $teams->sum(function($team) { return $team->members->count(); }) }}</h3>
                    <p class="stats-label">総メンバー数</p>
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
                    <h3 class="stats-number">{{ $teams->sum(function($team) { return $team->tasks->count(); }) }}</h3>
                    <p class="stats-label">総タスク数</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card stats-card-primary">
            <div class="stats-card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-crown fa-2x"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $teams->sum(function($team) { return $team->members->where('role', 'leader')->count(); }) }}</h3>
                    <p class="stats-label">リーダー数</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
