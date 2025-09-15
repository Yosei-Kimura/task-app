@extends('layouts.app')

@section('title', 'メンバー詳細 - ' . $member->name)

@section('content')
<style>
.member-profile-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    margin-bottom: 2rem;
}

.member-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    border: 4px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(10px);
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

.stats-card.primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.stats-card.success {
    background: linear-gradient(135deg, #56ab2f, #a8e6cf);
    color: white;
}

.stats-card.warning {
    background: linear-gradient(135deg, #ff9a56, #ffeaa7);
    color: white;
}

.stats-card.info {
    background: linear-gradient(135deg, #74b9ff, #0984e3);
    color: white;
}

.task-row {
    transition: all 0.2s ease;
}

.task-row:hover {
    transform: translateX(3px);
}

.task-completed {
    background: rgba(40, 167, 69, 0.05) !important;
}

.task-overdue {
    background: rgba(220, 53, 69, 0.05) !important;
    border-left: 4px solid #dc3545 !important;
}

.task-active {
    background: rgba(102, 126, 234, 0.02) !important;
}

.progress-ring {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 60px;
}

.progress-ring svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.progress-ring circle {
    fill: transparent;
    stroke-width: 4;
    stroke-linecap: round;
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

.info-item {
    margin-bottom: 1.5rem;
}

.info-item:last-child {
    margin-bottom: 0;
}
</style>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-custom mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ダッシュボード</a></li>
                <li class="breadcrumb-item"><a href="{{ route('members.index') }}">メンバー一覧</a></li>
                <li class="breadcrumb-item active">{{ $member->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- プロフィールヘッダー -->
<div class="card member-profile-card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="member-avatar-large me-4" style="background-color: {{ $member->teams->first()->color ?? '#667eea' }};">
                        {{ substr($member->name, 0, 2) }}
                    </div>
                    <div>
                        <h1 class="h2 mb-2">
                            {{ $member->name }}
                            @if($member->isLeader())
                                <i class="fas fa-crown text-warning ms-2" title="リーダー"></i>
                            @endif
                        </h1>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <!-- チームバッジ -->
                            @foreach($member->teams as $team)
                                <a href="{{ route('teams.show', $team) }}" class="text-decoration-none">
                                    <span class="badge fs-6 px-3 py-2" style="background-color: rgba(255,255,255,0.2); border-radius: 25px; backdrop-filter: blur(10px);">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $team->name }}
                                        @if($team->pivot->role === 'leader')
                                            <i class="fas fa-crown ms-1" title="このチームのリーダー"></i>
                                        @endif
                                    </span>
                                </a>
                            @endforeach
                            
                            <!-- Slack連携状態 -->
                            <span class="badge fs-6 px-3 py-2" style="background-color: rgba(255,255,255,0.2); border-radius: 25px; backdrop-filter: blur(10px);">
                                @if($member->slack_user_id)
                                    <i class="fab fa-slack me-1"></i>
                                    Slack連携済み
                                @else
                                    <i class="fas fa-link-slash me-1"></i>
                                    Slack未連携
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('members.edit', $member) }}" class="btn btn-light btn-lg">
                        <i class="fas fa-edit me-2"></i>
                        編集
                    </a>
                    @if($member->slack_user_id)
                        <button class="btn btn-outline-light btn-lg" onclick="notifyMember()">
                            <i class="fab fa-slack me-2"></i>
                            通知
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>
                        削除
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- サイドバー -->
    <div class="col-lg-4">
        <!-- 基本情報カード -->
        <div class="card info-card mb-4">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-user me-2"></i>
                    基本情報
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-users me-2 text-primary"></i>
                        <strong class="text-muted">所属チーム</strong>
                    </div>
                    @foreach($member->teams as $team)
                        <a href="{{ route('teams.show', $team) }}" class="text-decoration-none">
                            <div class="p-2 rounded mb-2" style="background: rgba(102, 126, 234, 0.1);">
                                <div class="fw-semibold">
                                    {{ $team->name }}
                                    @if($team->pivot->role === 'leader')
                                        <i class="fas fa-crown text-warning ms-1" title="リーダー"></i>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $team->event->name }} - 
                                    {{ $team->event->start_date->format('Y/m/d') }} - 
                                    {{ $team->event->end_date->format('Y/m/d') }}
                                </small>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($member->email)
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope me-2 text-success"></i>
                            <strong class="text-muted">メールアドレス</strong>
                        </div>
                        <a href="mailto:{{ $member->email }}" class="text-decoration-none">
                            <div class="p-2 rounded" style="background: rgba(40, 167, 69, 0.1);">
                                <div class="fw-semibold">{{ $member->email }}</div>
                                <small class="text-muted">メール送信</small>
                            </div>
                        </a>
                    </div>
                @endif

                @if($member->notes)
                    <div class="info-item mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-sticky-note me-2 text-warning"></i>
                            <strong class="text-muted">備考・メモ</strong>
                        </div>
                        <div class="p-3 rounded" style="background: #f8f9fa; border-left: 4px solid #667eea;">
                            {{ $member->notes }}
                        </div>
                    </div>
                @endif

                <!-- タイムスタンプ -->
                <div class="border-top pt-3 mt-3">
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted d-block">参加日</small>
                            <strong>{{ $member->created_at->format('Y/m/d') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">最終更新</small>
                            <strong>{{ $member->updated_at->format('Y/m/d') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 統計情報 -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="stats-card primary">
                    <div class="h2 mb-1">{{ $member->assignedTasks->count() }}</div>
                    <div class="small">担当タスク</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stats-card success">
                    <div class="h2 mb-1">{{ $member->assignedTasks->filter(function($task) { return $task->progressStatus && $task->progressStatus->name === '完了'; })->count() }}</div>
                    <div class="small">完了済み</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stats-card warning">
                    <div class="h2 mb-1">{{ $member->assignedTasks->filter(function($task) { return $task->due_date && $task->due_date->isPast() && (!$task->progressStatus || $task->progressStatus->name !== '完了'); })->count() }}</div>
                    <div class="small">期限切れ</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stats-card info">
                    <div class="h2 mb-1">{{ $member->assignedTasks->filter(function($task) { return $task->due_date >= now() && (!$task->progressStatus || $task->progressStatus->name !== '完了'); })->count() }}</div>
                    <div class="small">進行中</div>
                </div>
            </div>
        </div>

        <!-- 完了率表示 -->
        @if($member->assignedTasks->count() > 0)
            @php
                $completedTasks = $member->assignedTasks->filter(function($task) { return $task->progressStatus && $task->progressStatus->name === '完了'; })->count();
                $totalTasks = $member->assignedTasks->count();
                $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            @endphp
            <div class="card info-card mb-4">
                <div class="card-body text-center">
                    <h6 class="card-title mb-3">タスク完了率</h6>
                    <div class="progress-ring mb-3">
                        <svg width="60" height="60">
                            <circle cx="30" cy="30" r="25" stroke="#e9ecef" stroke-width="4" fill="transparent"/>
                            <circle cx="30" cy="30" r="25" stroke="#28a745" stroke-width="4" fill="transparent"
                                    stroke-dasharray="{{ 2 * 3.14159 * 25 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 25 * (1 - $completionRate / 100) }}"
                                    style="transition: stroke-dashoffset 0.5s ease-in-out;"/>
                        </svg>
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <div class="fw-bold">{{ $completionRate }}%</div>
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
                    <a href="{{ route('tasks.create') }}?assignee_id={{ $member->id }}" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i>
                        タスク割り当て
                    </a>
                    @if($member->email)
                        <a href="mailto:{{ $member->email }}" class="btn btn-outline-light">
                            <i class="fas fa-envelope me-2"></i>
                            メール送信
                        </a>
                    @endif
                    @if($member->slack_user_id)
                        <button class="btn btn-outline-light" onclick="notifyMember()">
                            <i class="fab fa-slack me-2"></i>
                            Slack通知
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 担当タスク一覧 -->
    <div class="col-lg-8">
        <div class="card info-card">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-tasks me-2"></i>
                        担当タスク ({{ $member->assignedTasks->count() }}件)
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="task-filter" id="all-tasks" autocomplete="off" checked>
                        <label class="btn btn-outline-light" for="all-tasks">全て</label>

                        <input type="radio" class="btn-check" name="task-filter" id="active-tasks" autocomplete="off">
                        <label class="btn btn-outline-light" for="active-tasks">進行中</label>

                        <input type="radio" class="btn-check" name="task-filter" id="completed-tasks" autocomplete="off">
                        <label class="btn btn-outline-light" for="completed-tasks">完了済み</label>

                        <input type="radio" class="btn-check" name="task-filter" id="overdue-tasks" autocomplete="off">
                        <label class="btn btn-outline-light" for="overdue-tasks">期限切れ</label>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($member->assignedTasks->count() > 0)
                    <div class="task-list p-3">
                        @foreach($member->assignedTasks->sortBy('due_date') as $task)
                            @php
                                $isCompleted = $task->progressStatus && $task->progressStatus->name === '完了';
                                $isOverdue = $task->due_date && $task->due_date->isPast() && !$isCompleted;
                                $taskClass = '';
                                if ($isCompleted) $taskClass = 'task-completed';
                                elseif ($isOverdue) $taskClass = 'task-overdue';
                                else $taskClass = 'task-active';
                            @endphp
                            <div class="task-row card mb-2 {{ $taskClass }}" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center">
                                                <div class="task-priority-indicator me-3" style="width: 4px; height: 40px; border-radius: 2px; background-color: 
                                                    @if($task->priority >= 4) #dc3545
                                                    @elseif($task->priority >= 3) #ffc107
                                                    @else #6c757d @endif;">
                                                </div>
                                                <div>
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-semibold">
                                                        {{ $task->title }}
                                                    </a>
                                                    <div class="small text-muted mt-1">
                                                        @if($isOverdue)
                                                            <span class="badge bg-danger">期限切れ</span>
                                                        @elseif($task->due_date && $task->due_date->isToday())
                                                            <span class="badge bg-warning text-dark">今日が期限</span>
                                                        @elseif($task->due_date && $task->due_date->isTomorrow())
                                                            <span class="badge bg-info">明日が期限</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            @if($task->due_date)
                                                <div class="text-center">
                                                    <div class="fw-semibold">{{ $task->due_date->format('m/d') }}</div>
                                                    <small class="text-muted">{{ $task->due_date->format('H:i') }}</small>
                                                    @if($isOverdue)
                                                        <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-center text-muted">
                                                    <small>期限なし</small>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-center">
                                            @if($task->progressStatus)
                                                <span class="badge px-3 py-2" style="background-color: {{ $task->progressStatus->color }}; border-radius: 15px;">
                                                    {{ $task->progressStatus->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2" style="border-radius: 15px;">未設定</span>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-end">
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
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-tasks fa-3x text-muted mb-3 opacity-50"></i>
                            <h5 class="text-muted">担当タスクがありません</h5>
                            <p class="text-muted">このメンバーにタスクを割り当てましょう。</p>
                            <a href="{{ route('tasks.create') }}?assignee_id={{ $member->id }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                タスク割り当て
                            </a>
                        </div>
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
                <h5 class="modal-title">メンバー削除確認</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>{{ $member->name }}</strong> を削除してもよろしいですか？</p>
                @if($member->assignedTasks->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>注意:</strong> このメンバーには{{ $member->assignedTasks->count() }}件のタスクが割り当てられています。<br>
                        メンバーを削除すると、割り当てられたタスクは未割り当て状態になります。この操作は取り消せません。
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <form method="POST" action="{{ route('members.destroy', $member) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function notifyMember() {
    if (confirm('{{ $member->name }}さんにSlack通知を送信しますか？')) {
        fetch('{{ route("members.notify", $member) }}', {
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

// タスクフィルタリング
document.addEventListener('DOMContentLoaded', function() {
    const filterRadios = document.querySelectorAll('input[name="task-filter"]');
    const taskRows = document.querySelectorAll('.task-row');
    
    filterRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const filterType = this.id;
            
            taskRows.forEach(row => {
                let shouldShow = true;
                
                switch(filterType) {
                    case 'active-tasks':
                        shouldShow = row.classList.contains('task-active');
                        break;
                    case 'completed-tasks':
                        shouldShow = row.classList.contains('task-completed');
                        break;
                    case 'overdue-tasks':
                        shouldShow = row.classList.contains('task-overdue');
                        break;
                    case 'all-tasks':
                    default:
                        shouldShow = true;
                        break;
                }
                
                row.style.display = shouldShow ? '' : 'none';
            });
            
            // フィルタリング後の件数を更新
            updateTaskCount();
        });
    });
    
    function updateTaskCount() {
        const activeFilter = document.querySelector('input[name="task-filter"]:checked').id;
        const totalTasks = document.querySelectorAll('.task-row').length;
        const visibleTasks = document.querySelectorAll('.task-row[style=""], .task-row:not([style])').length;
        
        const titleElement = document.querySelector('.card-title');
        const originalText = titleElement.textContent.replace(/\(\d+件\)/, '');
        titleElement.textContent = `${originalText}(${visibleTasks}件)`;
    }
});
</script>

<style>
.task-completed {
    background-color: #f8f9fa;
}
.task-overdue {
    background-color: #fff5f5;
}
.task-active {
    background-color: #fff;
}
</style>
@endsection
