@extends('layouts.app')

@section('title', $task->title . ' - タスク詳細')

@section('content')
<style>
.task-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    margin-bottom: 2rem;
}

.task-priority-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.quick-action-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: none;
    color: white;
}

.status-timeline {
    position: relative;
    padding-left: 2rem;
}

.status-timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #667eea, #764ba2);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -1.6rem;
    top: 1.2rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
    border: 3px solid white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
}

.related-link {
    padding: 0.75rem 1rem;
    border-radius: 10px;
    transition: all 0.2s ease;
    text-decoration: none !important;
}

.related-link:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.status-form {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
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
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">タスク一覧</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($task->title, 30) }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- タスクヘッダーカード -->
<div class="card task-header-card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-2">
                    <i class="fas fa-tasks me-2"></i>
                    {{ $task->title }}
                </h1>
                <div class="d-flex align-items-center flex-wrap gap-3 mt-3">
                    <!-- 優先度バッジ -->
                    <span class="task-priority-badge
                        @if($task->priority >= 4) bg-danger text-white
                        @elseif($task->priority >= 3) bg-warning text-dark
                        @else bg-secondary text-white @endif">
                        <i class="fas fa-flag me-1"></i>
                        {{ $task->priority_text }}
                    </span>
                    
                    <!-- ステータスバッジ -->
                    @if($task->progressStatus)
                        <span class="badge fs-6 px-3 py-2" style="background-color: {{ $task->progressStatus->color }}; border-radius: 25px;">
                            <i class="fas fa-circle me-1"></i>
                            {{ $task->progressStatus->name }}
                        </span>
                    @else
                        <span class="badge bg-light text-dark fs-6 px-3 py-2" style="border-radius: 25px;">
                            <i class="fas fa-question-circle me-1"></i>
                            未設定
                        </span>
                    @endif
                    
                    <!-- 期限表示 -->
                    @if($task->due_date)
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-clock me-2"></i>
                            <div>
                                <div @if($task->isOverdue()) class="text-warning fw-bold" @endif>
                                    {{ $task->due_date->format('Y/m/d H:i') }}
                                </div>
                                @if($task->isOverdue())
                                    <small class="text-warning">期限超過</small>
                                @elseif($task->due_date->isToday())
                                    <small class="text-info">今日期限</small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-light btn-lg">
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

<div class="row">
    <!-- メイン情報 -->
    <div class="col-lg-8">
        <!-- 基本情報カード -->
        <div class="card info-card mb-4">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-info-circle me-2"></i>
                    基本情報
                </h5>
            </div>
            <div class="card-body">
                <!-- 第1行: イベント・チーム -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-item">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>
                                イベント
                            </h6>
                            <div class="info-content">
                                <a href="{{ route('events.show', $task->event) }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center p-2 rounded" style="background: rgba(102, 126, 234, 0.1);">
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $task->event->name }}</div>
                                            <small class="text-muted">
                                                {{ $task->event->start_date->format('Y/m/d') }} - 
                                                {{ $task->event->end_date->format('Y/m/d') }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-users me-1"></i>
                                チーム
                            </h6>
                            <div class="info-content">
                                <a href="{{ route('teams.show', $task->team) }}" class="text-decoration-none">
                                    <div class="d-flex align-items-center p-2 rounded" style="background: {{ $task->team->color }}20;">
                                        <div class="team-avatar me-2" style="width: 32px; height: 32px; background-color: {{ $task->team->color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-users text-white" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $task->team->name }}</div>
                                            <small class="text-muted">チーム詳細を見る</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 第2行: 担当者・進捗 -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-item">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-user me-1"></i>
                                担当者
                            </h6>
                            <div class="info-content">
                                @if($task->assignedMember)
                                    <a href="{{ route('members.show', $task->assignedMember) }}" class="text-decoration-none">
                                        <div class="d-flex align-items-center p-2 rounded" style="background: rgba(40, 167, 69, 0.1);">
                                            <div class="member-avatar me-2" style="width: 32px; height: 32px; background-color: {{ $task->team->color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <span class="text-white fw-bold" style="font-size: 0.8rem;">{{ substr($task->assignedMember->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $task->assignedMember->name }}</div>
                                                <div class="d-flex align-items-center">
                                                    @if($task->assignedMember->hasSlackAccount())
                                                        <i class="fab fa-slack text-success me-1" title="Slack連携済み"></i>
                                                    @endif
                                                    @if($task->assignedMember->role === 'leader')
                                                        <i class="fas fa-crown text-warning" title="リーダー"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-user-slash fa-2x mb-2 opacity-50"></i>
                                        <div>担当者未設定</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-chart-line me-1"></i>
                                進捗状況
                            </h6>
                            <div class="info-content">
                                @if($task->progressStatus)
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $task->progressStatus->progress_percentage ?? 0 }}%; background-color: {{ $task->progressStatus->color }};"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge px-3 py-2" style="background-color: {{ $task->progressStatus->color }}; border-radius: 15px;">
                                            {{ $task->progressStatus->name }}
                                        </span>
                                        <small class="text-muted">{{ $task->progressStatus->progress_percentage ?? 0 }}%</small>
                                    </div>
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-question-circle fa-2x mb-2 opacity-50"></i>
                                        <div>ステータス未設定</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($task->description)
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="info-item">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-align-left me-1"></i>
                                詳細説明
                            </h6>
                            <div class="info-content">
                                <div class="description-content p-3 rounded" style="background: #f8f9fa; border-left: 4px solid #667eea;">
                                    {!! nl2br(e($task->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- タイムスタンプ -->
                <div class="row mt-4 pt-3 border-top">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-plus-circle me-1"></i>
                            作成: {{ $task->created_at->format('Y年m月d日 H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-edit me-1"></i>
                            更新: {{ $task->updated_at->format('Y年m月d日 H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 変更履歴 -->
        <div class="card info-card">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-history me-2"></i>
                    変更履歴
                </h5>
            </div>
            <div class="card-body p-0">
                @if($task->histories->count() > 0)
                    <div class="status-timeline p-4">
                        @foreach($task->histories->sortByDesc('created_at') as $history)
                            <div class="timeline-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="history-icon me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-edit text-white" style="font-size: 0.8rem;"></i>
                                            </div>
                                            <h6 class="mb-0 fw-semibold">{{ $history->action_text }}</h6>
                                        </div>
                                        
                                        @if($history->changedByMember)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user me-2 text-muted"></i>
                                                <span class="text-muted">{{ $history->changedByMember->name }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($history->changes_summary)
                                            <div class="changes-summary p-2 rounded mb-2" style="background: rgba(102, 126, 234, 0.1); border-left: 3px solid #667eea;">
                                                <strong class="text-primary">変更内容:</strong> {{ $history->changes_summary }}
                                            </div>
                                        @endif
                                        
                                        @if($history->comment)
                                            <div class="comment p-2 rounded" style="background: rgba(23, 162, 184, 0.1); border-left: 3px solid #17a2b8;">
                                                <i class="fas fa-comment me-1 text-info"></i>
                                                <span class="text-info">{{ $history->comment }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="timestamp text-end">
                                        <small class="text-muted d-block">{{ $history->created_at->format('m/d') }}</small>
                                        <small class="text-muted">{{ $history->created_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-history fa-3x text-muted mb-3 opacity-50"></i>
                            <h6 class="text-muted">変更履歴はまだありません</h6>
                            <p class="text-muted small">このタスクの編集や状態変更が記録されます</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- サイドバー -->
    <div class="col-lg-4">
        <!-- クイックアクション -->
        <div class="card quick-action-card mb-4">
            <div class="card-header border-0">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-bolt me-2"></i>
                    クイックアクション
                </h5>
            </div>
            <div class="card-body">
                <!-- ステータス変更 -->
                <div class="status-form mb-4">
                    <h6 class="text-white mb-3">
                        <i class="fas fa-chart-line me-2"></i>
                        ステータス変更
                    </h6>
                    <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="d-flex flex-column gap-2">
                            <select name="progress_status_id" class="form-select" required style="background: rgba(255,255,255,0.9); border: none; border-radius: 10px;">
                                <option value="">新しいステータスを選択</option>
                                @foreach($task->event->progressStatuses->where('is_active', true)->sortBy('order') as $status)
                                    <option value="{{ $status->id }}" 
                                            @if($task->progress_status_id == $status->id) disabled @endif
                                            data-color="{{ $status->color }}">
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-light btn-sm">
                                <i class="fas fa-check me-2"></i>
                                ステータス更新
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 担当者変更 -->
                <div class="status-form">
                    <h6 class="text-white mb-3">
                        <i class="fas fa-user-check me-2"></i>
                        担当者変更
                    </h6>
                    <form action="{{ route('tasks.assign-member', $task) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="d-flex flex-column gap-2">
                            <select name="assigned_member_id" class="form-select" style="background: rgba(255,255,255,0.9); border: none; border-radius: 10px;">
                                <option value="">新しい担当者を選択</option>
                                @foreach($task->team->members->where('is_active', true) as $member)
                                    <option value="{{ $member->id }}" 
                                            @if($task->assigned_member_id == $member->id) disabled @endif>
                                        {{ $member->name }}
                                        @if($member->hasSlackAccount()) 📱 @endif
                                        @if($member->role === 'leader') 👑 @endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-light btn-sm">
                                <i class="fas fa-user-check me-2"></i>
                                担当者変更
                            </button>
                        </div>
                    </form>
                </div>

                @if(app()->environment('local') && $task->assignedMember && $task->assignedMember->hasSlackAccount())
                    <div class="mt-4 pt-3 border-top border-light">
                        <a href="{{ route('test.slack-reminder', $task) }}" class="btn btn-outline-light btn-sm w-100">
                            <i class="fab fa-slack me-2"></i>
                            テスト通知送信
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- 関連情報 -->
        <div class="card info-card">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-link me-2"></i>
                    関連情報
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="related-links">
                    <a href="{{ route('events.show', $task->event) }}" class="related-link d-block border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="link-icon me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar-alt text-white"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $task->event->name }}</div>
                                <small class="text-muted">イベント詳細・タスク一覧</small>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                    
                    <a href="{{ route('teams.show', $task->team) }}" class="related-link d-block border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="link-icon me-3" style="width: 40px; height: 40px; background-color: {{ $task->team->color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $task->team->name }}</div>
                                <small class="text-muted">チーム詳細・メンバー一覧</small>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                    
                    @if($task->assignedMember)
                        <a href="{{ route('members.show', $task->assignedMember) }}" class="related-link d-block">
                            <div class="d-flex align-items-center">
                                <div class="link-icon me-3" style="width: 40px; height: 40px; background-color: {{ $task->team->color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold" style="font-size: 0.9rem;">{{ substr($task->assignedMember->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $task->assignedMember->name }}</div>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-2">メンバー詳細</small>
                                        @if($task->assignedMember->hasSlackAccount())
                                            <i class="fab fa-slack text-success" title="Slack連携済み"></i>
                                        @endif
                                        @if($task->assignedMember->role === 'leader')
                                            <i class="fas fa-crown text-warning ms-1" title="リーダー"></i>
                                        @endif
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right ms-auto text-muted"></i>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 削除確認モーダル -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">タスクの削除</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>本当にこのタスクを削除しますか？</p>
                <p><strong>{{ $task->title }}</strong></p>
                <p class="text-danger small">この操作は取り消せません。</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
