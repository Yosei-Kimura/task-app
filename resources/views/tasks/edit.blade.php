@extends('layouts.app')

@section('title', 'タスク編集 - ' . $task->title)

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ダッシュボード</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">タスク一覧</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.show', $task) }}">{{ Str::limit($task->title, 20) }}</a></li>
                <li class="breadcrumb-item active">編集</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-edit me-2"></i>
                タスク編集
            </h1>
            <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                戻る
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>
                    タスク情報の編集
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">イベント <span class="text-danger">*</span></label>
                                <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                                    <option value="">イベントを選択してください</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" 
                                                @if(old('event_id', $task->event_id) == $event->id) selected @endif>
                                            {{ $event->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('event_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="team_id" class="form-label">チーム <span class="text-danger">*</span></label>
                                <select name="team_id" id="team_id" class="form-select @error('team_id') is-invalid @enderror" required>
                                    <option value="">チームを選択してください</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" 
                                                data-event-id="{{ $team->event_id }}"
                                                @if(old('team_id', $task->team_id) == $team->id) selected @endif>
                                            {{ $team->name }} ({{ $team->event->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">タスク名 <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $task->title) }}" 
                               placeholder="タスクの名前を入力してください" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">詳細</label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="タスクの詳細を入力してください（任意）">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_member_id" class="form-label">担当者</label>
                                <select name="assigned_member_id" id="assigned_member_id" class="form-select @error('assigned_member_id') is-invalid @enderror">
                                    <option value="">担当者を選択してください（任意）</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" 
                                                @if(old('assigned_member_id', $task->assigned_member_id) == $member->id) selected @endif>
                                            {{ $member->name }}
                                            @if($member->teams->isNotEmpty())
                                                ({{ $member->teams->pluck('name')->join(', ') }})
                                            @else
                                                (チーム未所属)
                                            @endif
                                            @if($member->hasSlackAccount())
                                                <i class="fab fa-slack"></i>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_member_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="progress_status_id" class="form-label">進捗状況</label>
                                <select name="progress_status_id" id="progress_status_id" class="form-select @error('progress_status_id') is-invalid @enderror">
                                    <option value="">進捗状況を選択してください（任意）</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" 
                                                data-event-id="{{ $status->event_id }}"
                                                data-color="{{ $status->color }}"
                                                @if(old('progress_status_id', $task->progress_status_id) == $status->id) selected @endif>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('progress_status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">期限</label>
                                <input type="datetime-local" name="due_date" id="due_date" 
                                       class="form-control @error('due_date') is-invalid @enderror" 
                                       value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    期限を変更するとリマインダー送信フラグがリセットされます
                                    @if($task->is_reminder_sent)
                                        <br><span class="text-success">✓ リマインダー送信済み ({{ $task->reminder_sent_at->format('Y/m/d H:i') }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">優先度 <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="1" @if(old('priority', $task->priority) == 1) selected @endif>最低</option>
                                    <option value="2" @if(old('priority', $task->priority) == 2) selected @endif>低</option>
                                    <option value="3" @if(old('priority', $task->priority) == 3) selected @endif>中</option>
                                    <option value="4" @if(old('priority', $task->priority) == 4) selected @endif>高</option>
                                    <option value="5" @if(old('priority', $task->priority) == 5) selected @endif>最高</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        変更内容は自動的に履歴として記録されます。
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            キャンセル
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            更新
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 現在の状態表示 -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>
                    現在の状態
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">現在の担当者</h6>
                        <p>
                            @if($task->assignedMember)
                                {{ $task->assignedMember->name }}
                                @if($task->assignedMember->hasSlackAccount())
                                    <i class="fab fa-slack text-success ms-1"></i>
                                @endif
                            @else
                                <span class="text-muted">未割当</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">現在のステータス</h6>
                        <p>
                            @if($task->progressStatus)
                                <span class="badge" style="background-color: {{ $task->progressStatus->color }}">
                                    {{ $task->progressStatus->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary">未設定</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventSelect = document.getElementById('event_id');
    const teamSelect = document.getElementById('team_id');
    const memberSelect = document.getElementById('assigned_member_id');
    const statusSelect = document.getElementById('progress_status_id');

    // 初期表示時のフィルタリング
    filterOptions();

    // イベント選択時にチームをフィルタリング
    eventSelect.addEventListener('change', function() {
        filterOptions();
        // 現在の選択をリセット（イベントが変更された場合）
        if (this.value !== '{{ $task->event_id }}') {
            teamSelect.value = '';
            memberSelect.value = '';
            statusSelect.value = '';
        }
    });

    // チーム選択時にメンバーをフィルタリング
    teamSelect.addEventListener('change', function() {
        const teamId = this.value;
        
        Array.from(memberSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const memberTeamId = option.dataset.teamId;
                option.style.display = teamId === '' || memberTeamId === teamId ? 'block' : 'none';
            }
        });
        
        // 現在の選択をリセット（チームが変更された場合）
        if (this.value !== '{{ $task->team_id }}') {
            memberSelect.value = '';
        }
    });

    function filterOptions() {
        const eventId = eventSelect.value;
        
        // チームの選択肢をフィルタリング
        Array.from(teamSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const teamEventId = option.dataset.eventId;
                option.style.display = eventId === '' || teamEventId === eventId ? 'block' : 'none';
            }
        });
        
        // 進捗状況の選択肢をフィルタリング
        Array.from(statusSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const statusEventId = option.dataset.eventId;
                option.style.display = eventId === '' || statusEventId === eventId ? 'block' : 'none';
            }
        });

        // チーム選択に基づいてメンバーをフィルタリング
        const teamId = teamSelect.value;
        Array.from(memberSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const memberTeamId = option.dataset.teamId;
                option.style.display = teamId === '' || memberTeamId === teamId ? 'block' : 'none';
            }
        });
    }
});
</script>
@endpush
