@extends('layouts.app')

@section('title', 'ステータス詳細 - ' . $progressStatus->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <span class="badge" style="background-color: {{ $progressStatus->color }}; font-size: 1rem;">
                    {{ $progressStatus->name }}
                </span>
                <small class="text-muted ms-2">進捗ステータス詳細</small>
            </h1>
            <div class="btn-group" role="group">
                <a href="{{ route('progress-statuses.edit', $progressStatus) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>
                    編集
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-2"></i>
                    削除
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- ステータス基本情報 -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header" style="background-color: {{ $progressStatus->color }}20; border-bottom: 2px solid {{ $progressStatus->color }}">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    基本情報
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong class="text-muted">ステータス名:</strong><br>
                    <span class="badge" style="background-color: {{ $progressStatus->color }}; font-size: 0.9em;">
                        {{ $progressStatus->name }}
                    </span>
                </div>

                <div class="mb-3">
                    <strong class="text-muted">対象イベント:</strong><br>
                    <a href="{{ route('events.show', $progressStatus->event) }}" class="text-decoration-none">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $progressStatus->event->name }}
                    </a>
                    <br>
                    <small class="text-muted">
                        {{ $progressStatus->event->start_date->format('Y年m月d日') }} - 
                        {{ $progressStatus->event->end_date->format('Y年m月d日') }}
                    </small>
                </div>

                @if($progressStatus->description)
                    <div class="mb-3">
                        <strong class="text-muted">説明:</strong><br>
                        <p class="mb-0">{{ $progressStatus->description }}</p>
                    </div>
                @endif

                <div class="mb-3">
                    <strong class="text-muted">表示順序:</strong><br>
                    #{{ $progressStatus->order }}
                    @php
                        $sameEventStatuses = $progressStatus->event->progressStatuses->sortBy('order');
                        $currentIndex = $sameEventStatuses->search(function($status) use ($progressStatus) {
                            return $status->id === $progressStatus->id;
                        });
                        $totalStatuses = $sameEventStatuses->count();
                    @endphp
                    <small class="text-muted">({{ $currentIndex + 1 }}/{{ $totalStatuses }}番目)</small>
                </div>

                <div class="mb-3">
                    <strong class="text-muted">カラーコード:</strong><br>
                    <div class="d-flex align-items-center">
                        <div style="width: 30px; height: 30px; background-color: {{ $progressStatus->color }}; border-radius: 5px; border: 1px solid #dee2e6; margin-right: 10px;"></div>
                        <code>{{ strtoupper($progressStatus->color) }}</code>
                    </div>
                </div>

                <div class="mb-3">
                    <strong class="text-muted">作成日:</strong><br>
                    {{ $progressStatus->created_at->format('Y年m月d日 H:i') }}
                </div>

                <div class="mb-0">
                    <strong class="text-muted">最終更新:</strong><br>
                    {{ $progressStatus->updated_at->format('Y年m月d日 H:i') }}
                </div>
            </div>
        </div>

        <!-- 統計情報 -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    使用統計
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3 class="text-primary">{{ $progressStatus->tasks->count() }}</h3>
                        <small class="text-muted">使用中のタスク</small>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="text-info">{{ $progressStatus->tasks->where('created_at', '>=', now()->subDays(7))->count() }}</h3>
                        <small class="text-muted">7日間の使用</small>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success">{{ $progressStatus->tasks->where('updated_at', '>=', now()->subDay())->count() }}</h3>
                        <small class="text-muted">24時間の使用</small>
                    </div>
                    <div class="col-6">
                        @php
                            $allEventTasks = $progressStatus->event->tasks()->count();
                            $usagePercent = $allEventTasks > 0 ? round(($progressStatus->tasks->count() / $allEventTasks) * 100, 1) : 0;
                        @endphp
                        <h3 class="text-warning">{{ $usagePercent }}%</h3>
                        <small class="text-muted">使用率</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- プレビュー -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>
                    表示プレビュー
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong class="text-muted">バッジ表示:</strong><br>
                    <span class="badge" style="background-color: {{ $progressStatus->color }}; font-size: 1rem;">
                        {{ $progressStatus->name }}
                    </span>
                </div>
                
                <div class="mb-0">
                    <strong class="text-muted">タスク表示例:</strong><br>
                    <div class="card mt-2">
                        <div class="card-body p-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 4px; height: 40px; background-color: {{ $progressStatus->color }}; margin-right: 10px;"></div>
                                <div>
                                    <h6 class="mb-1">サンプルタスク</h6>
                                    <span class="badge" style="background-color: {{ $progressStatus->color }};">{{ $progressStatus->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 順序操作 -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sort me-2"></i>
                    順序操作
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-info" onclick="moveStatus('up')" 
                            {{ $progressStatus->order <= $sameEventStatuses->min('order') ? 'disabled' : '' }}>
                        <i class="fas fa-arrow-up me-2"></i>
                        順序を上げる
                    </button>
                    <button class="btn btn-outline-info" onclick="moveStatus('down')" 
                            {{ $progressStatus->order >= $sameEventStatuses->max('order') ? 'disabled' : '' }}>
                        <i class="fas fa-arrow-down me-2"></i>
                        順序を下げる
                    </button>
                </div>
                <small class="form-text text-muted mt-2">
                    現在の順序: {{ $currentIndex + 1 }}/{{ $totalStatuses }}
                </small>
            </div>
        </div>
    </div>

    <!-- 使用中のタスク一覧 -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    このステータスを使用中のタスク ({{ $progressStatus->tasks->count() }}件)
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="task-filter" id="recent-tasks" autocomplete="off" checked>
                    <label class="btn btn-outline-primary" for="recent-tasks">最近更新</label>

                    <input type="radio" class="btn-check" name="task-filter" id="due-soon" autocomplete="off">
                    <label class="btn btn-outline-warning" for="due-soon">期限間近</label>

                    <input type="radio" class="btn-check" name="task-filter" id="overdue-tasks" autocomplete="off">
                    <label class="btn btn-outline-danger" for="overdue-tasks">期限切れ</label>

                    <input type="radio" class="btn-check" name="task-filter" id="all-tasks" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="all-tasks">全て</label>
                </div>
            </div>
            <div class="card-body">
                @if($progressStatus->tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>タスク名</th>
                                    <th>チーム</th>
                                    <th>担当者</th>
                                    <th>期限</th>
                                    <th>最終更新</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($progressStatus->tasks->sortByDesc('updated_at') as $task)
                                    @php
                                        $isOverdue = $task->due_date && $task->due_date->isPast();
                                        $isDueSoon = $task->due_date && $task->due_date->isBetween(now(), now()->addDays(3));
                                        $isRecent = $task->updated_at >= now()->subDays(7);
                                        
                                        $taskClass = 'task-all';
                                        if ($isRecent) $taskClass .= ' task-recent';
                                        if ($isDueSoon) $taskClass .= ' task-due-soon';
                                        if ($isOverdue) $taskClass .= ' task-overdue';
                                    @endphp
                                    <tr class="{{ $taskClass }}">
                                        <td>
                                            <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                                {{ $task->title }}
                                            </a>
                                            @if($isOverdue)
                                                <span class="badge bg-danger ms-2">期限切れ</span>
                                            @elseif($isDueSoon)
                                                <span class="badge bg-warning ms-2">期限間近</span>
                                            @endif
                                            @if($isRecent)
                                                <span class="badge bg-info ms-1">最近更新</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('teams.show', $task->team) }}" class="text-decoration-none">
                                                <span class="badge rounded-pill" style="background-color: {{ $task->team->color }}">
                                                    {{ $task->team->name }}
                                                </span>
                                            </a>
                                        </td>
                                        <td>
                                            @if($task->assignee)
                                                <a href="{{ route('members.show', $task->assignee) }}" class="text-decoration-none">
                                                    {{ $task->assignee->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">未割り当て</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->due_date)
                                                {{ $task->due_date->format('m/d H:i') }}
                                                @if($isOverdue)
                                                    <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
                                                @elseif($isDueSoon)
                                                    <i class="fas fa-clock text-warning ms-1"></i>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $task->updated_at->format('m/d H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-tasks fa-3x mb-3"></i>
                        <h5>このステータスを使用中のタスクはありません</h5>
                        <p>タスク作成時やタスク編集時にこのステータスを選択できます。</p>
                        <a href="{{ route('tasks.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>
                            新しいタスクを作成
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- 同じイベントの他のステータス -->
        @if($sameEventStatuses->where('id', '!=', $progressStatus->id)->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        同じイベントの他のステータス
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($sameEventStatuses->where('id', '!=', $progressStatus->id) as $status)
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="badge me-2" style="background-color: {{ $status->color }}">
                                        {{ $status->name }}
                                    </span>
                                    <small class="text-muted">#{{ $status->order }} ({{ $status->tasks->count() }}件)</small>
                                    <a href="{{ route('progress-statuses.show', $status) }}" class="btn btn-outline-primary btn-sm ms-auto">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- 削除確認モーダル -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ステータス削除確認</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>{{ $progressStatus->name }}</strong> ステータスを削除してもよろしいですか？</p>
                @if($progressStatus->tasks->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>注意:</strong> このステータスは{{ $progressStatus->tasks->count() }}件のタスクで使用されています。<br>
                        ステータスを削除すると、これらのタスクのステータスはクリアされます。この操作は取り消せません。
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <form method="POST" action="{{ route('progress-statuses.destroy', $progressStatus) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function moveStatus(direction) {
    if (!confirm('ステータスの順序を変更しますか？')) return;
    
    fetch('{{ route("progress-statuses.move", $progressStatus) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            direction: direction
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('順序の変更に失敗しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('順序の変更中にエラーが発生しました。');
    });
}

// タスクフィルタリング
document.addEventListener('DOMContentLoaded', function() {
    const filterRadios = document.querySelectorAll('input[name="task-filter"]');
    const taskRows = document.querySelectorAll('tbody tr');
    
    filterRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const filterType = this.id;
            
            taskRows.forEach(row => {
                let shouldShow = true;
                
                switch(filterType) {
                    case 'recent-tasks':
                        shouldShow = row.classList.contains('task-recent');
                        break;
                    case 'due-soon':
                        shouldShow = row.classList.contains('task-due-soon');
                        break;
                    case 'overdue-tasks':
                        shouldShow = row.classList.contains('task-overdue');
                        break;
                    case 'all-tasks':
                    default:
                        shouldShow = row.classList.contains('task-all');
                        break;
                }
                
                row.style.display = shouldShow ? '' : 'none';
            });
        });
    });
});
</script>

<style>
.task-overdue {
    background-color: #fff5f5;
}
.task-due-soon {
    background-color: #fffbf0;
}
.task-recent {
    background-color: #f0f8ff;
}
</style>
@endsection
