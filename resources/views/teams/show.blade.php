@extends('layouts.app')

@section('title', 'チーム詳細 - ' . $team->name)

@section('content')
<style>
.team-header-card {
    background: linear-gradient(135deg, {{ $team->color }}60, {{ $team->color }}90);
    border: none;
    color: white;
    margin-bottom: 2rem;
}

.team-color-primary {
    color: {{ $team->color }} !important;
}

.team-bg-primary {
    background-color: {{ $team->color }}20 !important;
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

.stats-card {
    text-align: center;
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 1rem;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.stats-card.members {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.stats-card.tasks {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
}

.stats-card.leaders {
    background: linear-gradient(135deg, #fa709a, #fee140);
    color: white;
}

.stats-card.completed {
    background: linear-gradient(135deg, #43e97b, #38f9d7);
    color: white;
}

.member-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
    margin-bottom: 1rem;
    overflow: hidden;
}

.member-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.member-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    background-color: {{ $team->color }};
}

.task-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
    margin-bottom: 1rem;
    border-left: 4px solid #dee2e6;
}

.task-card:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.task-card.overdue {
    border-left-color: #dc3545;
    background: rgba(220, 53, 69, 0.05);
}

.task-card.today {
    border-left-color: #ffc107;
    background: rgba(255, 193, 7, 0.05);
}

.quick-action-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: none;
    color: white;
}

.breadcrumb-custom {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    backdrop-filter: blur(10px);
}
</style>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ダッシュボード</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.show', $team->event) }}">{{ $team->event->name }}</a></li>
                <li class="breadcrumb-item active">{{ $team->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- チームヘッダー -->
<div class="card team-header-card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="team-logo me-4" style="width: 80px; height: 80px; background-color: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-2">{{ $team->name }}</h1>
                        @if($team->description)
                            <p class="mb-3 opacity-75">{{ $team->description }}</p>
                        @endif
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <!-- イベント情報 -->
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <div>
                                    <div>{{ $team->event->name }}</div>
                                    <small class="opacity-75">{{ $team->event->start_date->format('Y/m/d') }} - {{ $team->event->end_date->format('Y/m/d') }}</small>
                                </div>
                            </div>
                            
                            <!-- メンバー数 -->
                            <span class="badge px-3 py-2" style="background-color: rgba(255,255,255,0.2); border-radius: 25px; backdrop-filter: blur(10px);">
                                <i class="fas fa-user me-1"></i>
                                {{ $team->members->count() }}名のメンバー
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('teams.edit', $team) }}" class="btn btn-light btn-lg">
                        <i class="fas fa-edit me-2"></i>
                        編集
                    </a>
                    <button type="button" class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>
                        削除
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 統計ダッシュボード -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card members">
            <div class="h3 mb-1">{{ $team->members->count() }}</div>
            <div class="small">メンバー</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card tasks">
            <div class="h3 mb-1">{{ $team->tasks->count() }}</div>
            <div class="small">タスク</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card leaders">
            <div class="h3 mb-1">{{ $team->members->where('role', 'leader')->count() }}</div>
            <div class="small">リーダー</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card completed">
            @php
                $completedTasks = $team->tasks->filter(function($task) {
                    return $task->progressStatus && $task->progressStatus->name === '完了';
                })->count();
                $totalTasks = $team->tasks->count();
                $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            @endphp
            <div class="h3 mb-1">{{ $completedTasks }}</div>
            <div class="small">完了 ({{ $completionRate }}%)</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- サイドバー -->
    <div class="col-lg-4">
        <!-- チーム基本情報 -->
        <div class="card info-card mb-4">
            <div class="card-header bg-gradient team-bg-primary">
                <h5 class="card-title mb-0 team-color-primary">
                    <i class="fas fa-info-circle me-2"></i>
                    基本情報
                </h5>
            </div>
            <div class="card-body">
                @if($team->description)
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-align-left me-2 team-color-primary"></i>
                            <strong class="text-muted">説明</strong>
                        </div>
                        <div class="p-3 rounded team-bg-primary">
                            {{ $team->description }}
                        </div>
                    </div>
                @endif

                <!-- タイムスタンプ -->
                <div class="border-top pt-3">
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted d-block">作成日</small>
                            <strong>{{ $team->created_at->format('Y/m/d') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">最終更新</small>
                            <strong>{{ $team->updated_at->format('Y/m/d') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- チーム進捗 -->
        @if($team->tasks->count() > 0)
            <div class="card info-card mb-4">
                <div class="card-body text-center">
                    <h6 class="card-title mb-3">チーム進捗</h6>
                    <div class="progress-ring mb-3 position-relative d-inline-block">
                        <svg width="80" height="80">
                            <circle cx="40" cy="40" r="35" stroke="#e9ecef" stroke-width="6" fill="transparent"/>
                            <circle cx="40" cy="40" r="35" stroke="{{ $team->color }}" stroke-width="6" fill="transparent"
                                    stroke-dasharray="{{ 2 * 3.14159 * 35 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 35 * (1 - $completionRate / 100) }}"
                                    style="transition: stroke-dashoffset 0.5s ease-in-out; transform: rotate(-90deg); transform-origin: center;"/>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <div class="fw-bold h5 mb-0" style="color: {{ $team->color }};">{{ $completionRate }}%</div>
                        </div>
                    </div>
                    <div class="small text-muted">{{ $completedTasks }}/{{ $totalTasks }} タスク完了</div>
                </div>
            </div>
        @endif

        <!-- クイックアクション -->
        <div class="card quick-action-card">
            <div class="card-header border-0">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-bolt me-2"></i>
                    クイックアクション
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('members.create') }}?team_id={{ $team->id }}" class="btn btn-light">
                        <i class="fas fa-user-plus me-2"></i>
                        メンバー追加
                    </a>
                    <a href="{{ route('tasks.create') }}?team_id={{ $team->id }}" class="btn btn-outline-light">
                        <i class="fas fa-plus me-2"></i>
                        タスク作成
                    </a>
                    @if($team->members->whereNotNull('slack_user_id')->count() > 0)
                        <button class="btn btn-outline-light" onclick="notifyTeam()">
                            <i class="fab fa-slack me-2"></i>
                            チーム通知
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- メンバー一覧 -->
    <div class="col-lg-8">
        <div class="card info-card mb-4">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-users me-2"></i>
                        チームメンバー ({{ $team->members->count() }}人)
                    </h5>
                    <a href="{{ route('members.create') }}?team_id={{ $team->id }}" class="btn btn-light btn-sm">
                        <i class="fas fa-user-plus me-2"></i>
                        メンバー追加
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                @if($team->members->count() > 0)
                    @foreach($team->members as $member)
                        <div class="member-card p-3">
                            <div class="d-flex align-items-center">
                                <div class="member-avatar me-3">
                                    {{ substr($member->name, 0, 2) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 me-2">
                                            <a href="{{ route('members.show', $member) }}" class="text-decoration-none fw-semibold">
                                                {{ $member->name }}
                                            </a>
                                        </h6>
                                        @if($member->role === 'leader')
                                            <span class="badge bg-warning text-dark px-2 py-1" style="border-radius: 12px; font-size: 0.7rem;">
                                                <i class="fas fa-crown me-1"></i>リーダー
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-3 text-muted small">
                                        @if($member->email)
                                            <span>
                                                <i class="fas fa-envelope me-1"></i>
                                                {{ Str::limit($member->email, 25) }}
                                            </span>
                                        @endif
                                        @if($member->slack_user_id)
                                            <span class="text-success">
                                                <i class="fab fa-slack me-1"></i>
                                                Slack連携済み
                                            </span>
                                        @endif
                                        <span>
                                            <i class="fas fa-tasks me-1"></i>
                                            {{ $member->assignedTasks->count() }}件のタスク
                                        </span>
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('members.show', $member) }}" class="btn btn-outline-primary btn-sm" title="詳細">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-outline-secondary btn-sm" title="編集">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted">メンバーがまだいません</h6>
                        <p class="text-muted small">このチームにメンバーを追加してプロジェクトを開始しましょう</p>
                        <a href="{{ route('members.create') }}?team_id={{ $team->id }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            最初のメンバーを追加
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- タスク一覧 -->
        <div class="card info-card">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-tasks me-2"></i>
                        チームのタスク ({{ $team->tasks->count() }}件)
                    </h5>
                    <a href="{{ route('tasks.create') }}?team_id={{ $team->id }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-2"></i>
                        タスク作成
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                @if($team->tasks->count() > 0)
                    @foreach($team->tasks->sortBy('due_date') as $task)
                        @php
                            $isOverdue = $task->due_date && $task->due_date->isPast() && (!$task->progressStatus || $task->progressStatus->name !== '完了');
                            $isToday = $task->due_date && $task->due_date->isToday();
                        @endphp
                        <div class="task-card p-3 @if($isOverdue) overdue @elseif($isToday) today @endif">
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
                                                {{ $task->title }}
                                            </a>
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        @if($task->assignedMember)
                                            <span class="small text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <a href="{{ route('members.show', $task->assignedMember) }}" class="text-decoration-none">
                                                    {{ $task->assignedMember->name }}
                                                </a>
                                            </span>
                                        @else
                                            <span class="small text-muted">
                                                <i class="fas fa-user-slash me-1"></i>
                                                未割り当て
                                            </span>
                                        @endif
                                        @if($task->due_date)
                                            <span class="small @if($isOverdue) text-danger fw-bold @elseif($isToday) text-warning fw-bold @else text-muted @endif">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $task->due_date->format('m/d H:i') }}
                                                @if($isOverdue)
                                                    (期限切れ)
                                                @elseif($isToday)
                                                    (今日期限)
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if($task->progressStatus)
                                        <span class="badge px-2 py-1" style="background-color: {{ $task->progressStatus->color }}; border-radius: 12px; font-size: 0.75rem;">
                                            {{ $task->progressStatus->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1" style="border-radius: 12px; font-size: 0.75rem;">
                                            未設定
                                        </span>
                                    @endif
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm" title="詳細">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm" title="編集">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-3x text-muted mb-3 opacity-50"></i>
                        <h6 class="text-muted">タスクがまだありません</h6>
                        <p class="text-muted small">このチームにタスクを作成してプロジェクトを開始しましょう</p>
                        <a href="{{ route('tasks.create') }}?team_id={{ $team->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            最初のタスクを作成
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 削除確認モーダル -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">チーム削除確認</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>{{ $team->name }}</strong> を削除してもよろしいですか？</p>
                @if($team->members->count() > 0 || $team->tasks->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>注意:</strong> このチームには以下が関連付けられています：
                        <ul class="mb-0">
                            @if($team->members->count() > 0)
                                <li>{{ $team->members->count() }}人のメンバー</li>
                            @endif
                            @if($team->tasks->count() > 0)
                                <li>{{ $team->tasks->count() }}件のタスク</li>
                            @endif
                        </ul>
                        <br>
                        チームを削除すると、関連するメンバーとタスクも削除されます。この操作は取り消せません。
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <form method="POST" action="{{ route('teams.destroy', $team) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function notifyTeam() {
    if (confirm('チーム全体にSlack通知を送信しますか？')) {
        fetch('{{ route("teams.notify", $team) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: 'チームの進捗確認をお願いします。'
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
</script>
@endsection
