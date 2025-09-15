@extends('layouts.app')

@section('title', 'メンバー一覧')

@push('styles')
<style>
.member-card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
    overflow: hidden;
    height: 100%;
}

.member-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.member-card.leader {
    border: 2px solid #ffd700;
    box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2);
}

.member-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    position: relative;
    margin-right: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.member-avatar.leader::after {
    content: '\f521';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ffd700;
    color: #000;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
}

.member-info {
    flex: 1;
}

.member-name {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #2d3748;
}

.member-role {
    font-size: 0.9rem;
    color: #718096;
}

.team-badge {
    border-radius: 20px;
    padding: 0.3rem 0.8rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.slack-badge {
    border-radius: 15px;
    padding: 0.4rem 0.8rem;
    font-size: 0.75rem;
}

.member-stats {
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e7f3 100%);
    border-radius: 15px;
    padding: 1rem;
    margin: 1rem 0;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
}

.filter-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    border: none;
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.filter-card .form-select,
.filter-card .form-label {
    color: #2d3748;
}

.filter-card .card-body {
    background: rgba(255, 255, 255, 0.95);
    margin: 1rem;
    border-radius: 15px;
}

.task-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.task-item:last-child {
    border-bottom: none;
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
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-friends me-3"></i>
                        メンバー一覧
                    </h1>
                    <p class="page-subtitle">プロジェクトメンバーを管理・監視します</p>
                </div>
                <a href="{{ route('members.create') }}" class="btn-custom btn-custom-primary">
                    <i class="fas fa-user-plus me-2"></i>
                    新しいメンバー
                </a>
            </div>
        </div>
    </div>
</div>

<!-- フィルター -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" action="{{ route('members.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="event_id" class="form-label fw-bold">
                                <i class="fas fa-calendar me-2"></i>
                                イベント
                            </label>
                            <select name="event_id" id="event_id" class="form-select">
                                <option value="">全てのイベント</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
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
                                <option value="">全てのチーム</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="role" class="form-label fw-bold">
                                <i class="fas fa-user-tie me-2"></i>
                                役割
                            </label>
                            <select name="role" id="role" class="form-select">
                                <option value="">全ての役割</option>
                                <option value="leader" {{ request('role') === 'leader' ? 'selected' : '' }}>リーダー</option>
                                <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>メンバー</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="slack_connected" class="form-label fw-bold">
                                <i class="fab fa-slack me-2"></i>
                                Slack連携
                            </label>
                            <select name="slack_connected" id="slack_connected" class="form-select">
                                <option value="">全て</option>
                                <option value="1" {{ request('slack_connected') === '1' ? 'selected' : '' }}>連携済み</option>
                                <option value="0" {{ request('slack_connected') === '0' ? 'selected' : '' }}>未連携</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn-custom btn-custom-primary flex-fill">
                                <i class="fas fa-search me-2"></i>
                                検索
                            </button>
                            <a href="{{ route('members.index') }}" class="btn-custom btn-custom-secondary flex-fill">
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

<div class="row">
    @if($members->count() > 0)
        @foreach($members as $member)
            <div class="col-lg-4 col-md-6 mb-4">
                @php
                    $primaryTeam = $member->teams->first();
                    $primaryColor = $primaryTeam ? $primaryTeam->color : '#667eea';
                @endphp
                <div class="card member-card {{ $member->isLeader() ? 'leader' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="member-avatar {{ $member->isLeader() ? 'leader' : '' }}" 
                                 style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}80 100%);">
                                {{ mb_substr($member->name, 0, 1) }}
                            </div>
                            <div class="member-info">
                                <h5 class="member-name">{{ $member->name }}</h5>
                                <p class="member-role">
                                    @if($member->isLeader())
                                        <i class="fas fa-crown text-warning me-1"></i>
                                        チームリーダー
                                    @else
                                        <i class="fas fa-user me-1"></i>
                                        メンバー
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- チーム情報 -->
                        <div class="mb-3">
                            @foreach($member->teams->take(2) as $team)
                                <a href="{{ route('teams.show', $team) }}" class="text-decoration-none">
                                    <span class="team-badge me-1 mb-1 d-inline-block" style="background-color: {{ $team->color ?? '#667eea' }}">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $team->name }}
                                        @if($team->pivot->role === 'leader')
                                            <i class="fas fa-crown ms-1" title="リーダー"></i>
                                        @endif
                                    </span>
                                </a>
                            @endforeach
                            @if($member->teams->count() > 2)
                                <span class="text-muted small">他{{ $member->teams->count() - 2 }}チーム</span>
                            @endif
                        </div>

                        <!-- イベント情報 -->
                        @if($primaryTeam)
                        <div class="mb-3">
                            <a href="{{ route('events.show', $primaryTeam->event) }}" class="text-decoration-none text-muted">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ $primaryTeam->event->name }}
                            </a>
                        </div>
                        @endif

                        <!-- Slack連携 -->
                        @if($member->slack_user_id)
                            <div class="mb-3">
                                <span class="slack-badge bg-success text-white">
                                    <i class="fab fa-slack me-1"></i>
                                    Slack連携済み
                                </span>
                            </div>
                        @else
                            <div class="mb-3">
                                <span class="slack-badge bg-warning text-dark">
                                    <i class="fab fa-slack me-1"></i>
                                    Slack未連携
                                </span>
                            </div>
                        @endif

                        @if($member->email)
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ $member->email }}
                                </small>
                            </div>
                        @endif

                        <!-- 担当タスク統計 -->
                        <div class="member-stats">
                            <div class="row g-0">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-number">{{ $member->assignedTasks->count() }}</div>
                                        <small class="text-muted">担当タスク</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <div class="stat-number text-success">{{ $member->assignedTasks->filter(function($task) { return $task->progressStatus && $task->progressStatus->name === '完了'; })->count() }}</div>
                                        <small class="text-muted">完了済み</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 最近のタスク -->
                        @if($member->assignedTasks->count() > 0)
                            <div class="mt-3">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-tasks me-1"></i>
                                    最近の担当タスク
                                </h6>
                                @foreach($member->assignedTasks->sortByDesc('created_at')->take(2) as $task)
                                    <div class="task-item">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none text-dark">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small">{{ Str::limit($task->title, 25) }}</span>
                                                @if($task->progressStatus)
                                                    <span class="badge" style="background-color: {{ $task->progressStatus->color }}; font-size: 0.6em;">
                                                        {{ $task->progressStatus->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-clipboard-list text-muted mb-2"></i>
                                <p class="text-muted small mb-0">担当タスクはありません</p>
                            </div>
                        @endif

                        <!-- アクションボタン -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <i class="fas fa-calendar-plus me-1"></i>
                                参加日: {{ $member->created_at->format('Y/m/d') }}
                            </small>
                            <div class="d-flex gap-1">
                                <a href="{{ route('members.show', $member) }}" 
                                   class="action-btn btn btn-outline-primary" 
                                   title="詳細表示">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('members.edit', $member) }}" 
                                   class="action-btn btn btn-outline-success" 
                                   title="編集">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($member->slack_user_id)
                                    <button class="action-btn btn btn-outline-info" 
                                            onclick="notifyMember({{ $member->id }})" 
                                            title="Slack通知">
                                        <i class="fab fa-slack"></i>
                                    </button>
                                @endif
                                <a href="{{ route('tasks.create') }}?assignee_id={{ $member->id }}" 
                                   class="action-btn btn btn-outline-warning" 
                                   title="タスク作成">
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
            <div class="card member-card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-friends fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">該当するメンバーがいません</h4>
                    @if(request()->hasAny(['event_id', 'team_id', 'role', 'slack_connected']))
                        <p class="text-muted mb-4">フィルター条件を変更してみてください。</p>
                        <a href="{{ route('members.index') }}" class="btn-custom btn-custom-secondary">
                            <i class="fas fa-redo me-2"></i>
                            フィルターをリセット
                        </a>
                    @else
                        <p class="text-muted mb-4">新しいメンバーを追加してプロジェクトを開始しましょう。</p>
                        <a href="{{ route('members.create') }}" class="btn-custom btn-custom-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            メンバーを追加
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<!-- ページネーション -->
@if($members->hasPages())
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $members->withQueryString()->links() }}
        </div>
    </div>
</div>
@endif

<!-- 統計情報 -->
@if($allMembers->count() > 0)
<div class="row mt-5">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-user-friends"></i>
                </div>
                <h3 class="stats-number text-primary">{{ $allMembers->count() }}</h3>
                <p class="stats-label">総メンバー数</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-crown"></i>
                </div>
                <h3 class="stats-number text-warning">{{ $allMembers->where('role', 'leader')->count() }}</h3>
                <p class="stats-label">リーダー数</p>
            </div>
        </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fab fa-slack"></i>
                </div>
                <h3 class="stats-number text-success">{{ $allMembers->whereNotNull('slack_user_id')->count() }}</h3>
                <p class="stats-label">Slack連携済み</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-icon mb-3">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="stats-number text-info">{{ $allMembers->sum(function($member) { return $member->assignedTasks->count(); }) }}</h3>
                <p class="stats-label">総担当タスク数</p>
            </div>
        </div>
    </div>
</div>
@endif

<script>
function notifyMember(memberId) {
    if (confirm('このメンバーにSlack通知を送信しますか？')) {
        fetch(`/members/${memberId}/notify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: 'タスクの進捗確認をお願いします。'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('通知を送信しました。');
            } else {
                alert('通知の送信に失敗しました。');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('通知の送信中にエラーが発生しました。');
        });
    }
}

// チーム選択時の動的フィルタリング
document.getElementById('event_id').addEventListener('change', function() {
    const eventId = this.value;
    const teamSelect = document.getElementById('team_id');
    
    // チームオプションを初期化
    teamSelect.innerHTML = '<option value="">全てのチーム</option>';
    
    if (eventId) {
        // 選択されたイベントのチームのみ表示
        @foreach($teams as $team)
            if ('{{ $team->event_id }}' === eventId) {
                const option = document.createElement('option');
                option.value = '{{ $team->id }}';
                option.textContent = '{{ $team->name }}';
                if ('{{ request("team_id") }}' === '{{ $team->id }}') {
                    option.selected = true;
                }
                teamSelect.appendChild(option);
            }
        @endforeach
    } else {
        // 全てのチーム表示
        @foreach($teams as $team)
            const option{{ $team->id }} = document.createElement('option');
            option{{ $team->id }}.value = '{{ $team->id }}';
            option{{ $team->id }}.textContent = '{{ $team->name }}';
            if ('{{ request("team_id") }}' === '{{ $team->id }}') {
                option{{ $team->id }}.selected = true;
            }
            teamSelect.appendChild(option{{ $team->id }});
        @endforeach
    }
});
</script>
@endsection
