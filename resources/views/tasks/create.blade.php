@extends('layouts.app')

@section('title', '新しいタスク')

@push('styles')
<style>
.task-wizard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    color: white;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.task-wizard .card-body {
    background: rgba(255, 255, 255, 0.98);
    margin: 1.5rem;
    border-radius: 20px;
    color: #2d3748;
}

.step-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 2rem;
}

.step {
    display: flex;
    align-items: center;
    position: relative;
    padding: 0 2rem;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    margin-right: 10px;
}

.step-title {
    font-weight: 600;
    color: #4a5568;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 2rem;
    height: 2px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}

.task-section {
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e7f3 100%);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 5px solid;
    transition: all 0.3s ease;
}

.task-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.section-basic { border-left-color: #4299e1; }
.section-assignment { border-left-color: #38b2ac; }
.section-schedule { border-left-color: #ed8936; }
.section-priority { border-left-color: #e53e3e; }

.section-title {
    color: #4a5568;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.section-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 0.9rem;
}

.form-control, .form-select, .form-control:focus, .form-select:focus {
    border: 2px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    padding: 12px 18px;
    transition: all 0.3s ease;
    background: #fff;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    transform: translateY(-2px);
}

.priority-selector {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    margin-top: 10px;
}

.priority-option {
    position: relative;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 15px 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.priority-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.priority-option.selected {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.priority-1 { border-left: 4px solid #68d391; }
.priority-2 { border-left: 4px solid #4fd1c7; }
.priority-3 { border-left: 4px solid #63b3ed; }
.priority-4 { border-left: 4px solid #f6ad55; }
.priority-5 { border-left: 4px solid #fc8181; }

.priority-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.priority-option .priority-icon {
    font-size: 1.5rem;
    margin-bottom: 8px;
    display: block;
}

.priority-option .priority-text {
    font-weight: 600;
    font-size: 0.9rem;
}

.character-counter {
    font-size: 0.8rem;
    color: #718096;
    text-align: right;
    margin-top: 5px;
}

.character-counter.warning { color: #f6ad55; }
.character-counter.danger { color: #fc8181; }

.preview-task-card {
    background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
    border-radius: 20px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 2rem;
}

.task-preview-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 20px 20px 0 0;
}

.due-date-warning {
    background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
    border-left: 4px solid #f56565;
    border-radius: 8px;
    padding: 10px;
    margin-top: 10px;
}

.btn-create {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: none;
    border-radius: 25px;
    padding: 15px 35px;
    font-weight: 600;
    font-size: 1.1rem;
    color: white;
    transition: all 0.4s ease;
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-create::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.btn-create:hover::before {
    left: 100%;
}

.btn-create:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(79, 172, 254, 0.4);
    color: white;
}

.form-floating > .form-control, .form-floating > .form-select {
    height: calc(3.5rem + 2px);
    line-height: 1.25;
}

.breadcrumb-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 12px 20px;
    margin-bottom: 2rem;
}

.breadcrumb-modern .breadcrumb {
    margin: 0;
    background: transparent;
}

.breadcrumb-modern .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.breadcrumb-modern .breadcrumb-item.active {
    color: white;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="breadcrumb-modern">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ダッシュボード</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">タスク一覧</a></li>
                    <li class="breadcrumb-item active">新しいタスク</li>
                </ol>
            </nav>
        </div>

        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-plus-circle me-3"></i>
                        新しいタスク作成
                    </h1>
                    <p class="page-subtitle">詳細な情報を入力してタスクを作成します</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card task-wizard">
            <div class="card-header text-center">
                <h4 class="mb-3">
                    <i class="fas fa-tasks me-2"></i>
                    タスク作成ウィザード
                </h4>
                
                <!-- ステップインジケーター -->
                <div class="step-indicator">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-title">基本情報</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-title">割り当て</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-title">スケジュール</div>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-title">優先度</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST" id="taskForm">
                    @csrf
                    
                    <!-- 1. 基本情報セクション -->
                    <div class="task-section section-basic">
                        <h5 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            基本情報
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                                        <option value="">選択してください</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" 
                                                    @if(old('event_id', $defaultEventId ?? '') == $event->id) selected @endif>
                                                {{ $event->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="event_id">イベント <span class="text-danger">*</span></label>
                                    @error('event_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="team_id" id="team_id" class="form-select @error('team_id') is-invalid @enderror" required>
                                        <option value="">選択してください</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" 
                                                    data-event-id="{{ $team->event_id }}"
                                                    data-color="{{ $team->color }}"
                                                    @if(old('team_id', $defaultTeamId ?? '') == $team->id) selected @endif>
                                                {{ $team->name }} ({{ $team->event->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="team_id">チーム <span class="text-danger">*</span></label>
                                    @error('team_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" name="title" id="title" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" maxlength="200"
                                       placeholder="タスクの名前を入力してください" required>
                                <label for="title">タスク名 <span class="text-danger">*</span></label>
                                <div class="character-counter" id="titleCounter">0 / 200</div>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">詳細説明</label>
                            <textarea name="description" id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" maxlength="1000"
                                      placeholder="タスクの詳細、手順、注意事項など">{{ old('description') }}</textarea>
                            <div class="character-counter" id="descriptionCounter">0 / 1000</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 2. 割り当てセクション -->
                    <div class="task-section section-assignment">
                        <h5 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            担当者・ステータス設定
                        </h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="assigned_member_id" id="assigned_member_id" class="form-select @error('assigned_member_id') is-invalid @enderror">
                                        <option value="">担当者未割当</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" 
                                                    data-team-id="{{ $member->team_id }}"
                                                    data-slack="{{ $member->slack_user_id ? 'true' : 'false' }}"
                                                    @if(old('assigned_member_id', request('assignee_id')) == $member->id) selected @endif>
                                                {{ $member->name }} ({{ $member->team->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="assigned_member_id">担当者</label>
                                    @error('assigned_member_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select name="progress_status_id" id="progress_status_id" class="form-select @error('progress_status_id') is-invalid @enderror">
                                        <option value="">ステータス未設定</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" 
                                                    data-event-id="{{ $status->event_id }}"
                                                    data-color="{{ $status->color }}"
                                                    @if(old('progress_status_id') == $status->id) selected @endif>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="progress_status_id">進捗状況</label>
                                    @error('progress_status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. スケジュールセクション -->
                    <div class="task-section section-schedule">
                        <h5 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            期限設定
                        </h5>
                        
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="datetime-local" name="due_date" id="due_date" 
                                       class="form-control @error('due_date') is-invalid @enderror" 
                                       value="{{ old('due_date') }}" min="{{ now()->format('Y-m-d\TH:i') }}">
                                <label for="due_date">
                                    <i class="fas fa-clock me-1"></i>
                                    期限日時
                                </label>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                期限を設定すると、Slack連携済みメンバーにリマインダー通知が送信されます
                            </div>
                            <div id="dueDateWarning" class="due-date-warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>注意：</strong> 期限が24時間以内に設定されています
                            </div>
                        </div>
                    </div>

                    <!-- 4. 優先度セクション -->
                    <div class="task-section section-priority">
                        <h5 class="section-title">
                            <div class="section-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            優先度設定
                        </h5>
                        
                        <input type="hidden" name="priority" id="priority_hidden" value="{{ old('priority', 3) }}">
                        
                        <div class="priority-selector">
                            <div class="priority-option priority-1" data-value="1">
                                <input type="radio" name="priority_radio" value="1" {{ old('priority', 3) == 1 ? 'checked' : '' }}>
                                <span class="priority-icon">💚</span>
                                <span class="priority-text">最低</span>
                            </div>
                            <div class="priority-option priority-2" data-value="2">
                                <input type="radio" name="priority_radio" value="2" {{ old('priority', 3) == 2 ? 'checked' : '' }}>
                                <span class="priority-icon">🔵</span>
                                <span class="priority-text">低</span>
                            </div>
                            <div class="priority-option priority-3 selected" data-value="3">
                                <input type="radio" name="priority_radio" value="3" {{ old('priority', 3) == 3 ? 'checked' : '' }}>
                                <span class="priority-icon">⚪</span>
                                <span class="priority-text">中</span>
                            </div>
                            <div class="priority-option priority-4" data-value="4">
                                <input type="radio" name="priority_radio" value="4" {{ old('priority', 3) == 4 ? 'checked' : '' }}>
                                <span class="priority-icon">🟠</span>
                                <span class="priority-text">高</span>
                            </div>
                            <div class="priority-option priority-5" data-value="5">
                                <input type="radio" name="priority_radio" value="5" {{ old('priority', 3) == 5 ? 'checked' : '' }}>
                                <span class="priority-icon">🔴</span>
                                <span class="priority-text">最高</span>
                            </div>
                        </div>
                        
                        @error('priority')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-4 border-top">
                        <a href="{{ route('tasks.index') }}" class="btn-custom btn-custom-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            タスク一覧に戻る
                        </a>
                        <button type="submit" class="btn btn-create">
                            <i class="fas fa-plus me-2"></i>
                            タスクを作成
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- プレビューエリア -->
    <div class="col-lg-4">
        <div class="preview-task-card">
            <div class="task-preview-header text-center">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>
                    タスクプレビュー
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="task-priority-indicator mb-2" id="preview-priority-indicator">
                        <span class="badge bg-secondary">優先度: 中</span>
                    </div>
                    <h5 class="fw-bold" id="preview-title">新しいタスク</h5>
                    <small class="text-muted" id="preview-description">詳細が入力されると表示されます</small>
                </div>

                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="text-muted small">イベント</div>
                            <div class="fw-bold" id="preview-event">未選択</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">チーム</div>
                        <div class="fw-bold" id="preview-team">未選択</div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">担当者:</small>
                        <span id="preview-assignee" class="text-muted">未割当</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">ステータス:</small>
                        <span id="preview-status" class="badge bg-secondary">未設定</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">期限:</small>
                        <span id="preview-due-date" class="text-muted">未設定</span>
                    </div>
                </div>

                <div class="text-center border-top pt-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar-plus me-1"></i>
                        作成日: {{ now()->format('Y年m月d日') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // フォーム要素
    const eventSelect = document.getElementById('event_id');
    const teamSelect = document.getElementById('team_id');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const memberSelect = document.getElementById('assigned_member_id');
    const statusSelect = document.getElementById('progress_status_id');
    const dueDateInput = document.getElementById('due_date');
    const priorityHidden = document.getElementById('priority_hidden');
    
    // プレビュー要素
    const previewTitle = document.getElementById('preview-title');
    const previewDescription = document.getElementById('preview-description');
    const previewEvent = document.getElementById('preview-event');
    const previewTeam = document.getElementById('preview-team');
    const previewAssignee = document.getElementById('preview-assignee');
    const previewStatus = document.getElementById('preview-status');
    const previewDueDate = document.getElementById('preview-due-date');
    const previewPriorityIndicator = document.getElementById('preview-priority-indicator');
    
    // 文字数カウンター要素
    const titleCounter = document.getElementById('titleCounter');
    const descriptionCounter = document.getElementById('descriptionCounter');
    
    // 期限警告要素
    const dueDateWarning = document.getElementById('dueDateWarning');
    
    // データ格納
    const events = {
        @foreach($events as $event)
            '{{ $event->id }}': { name: '{{ $event->name }}' },
        @endforeach
    };
    
    const teams = {
        @foreach($teams as $team)
            '{{ $team->id }}': { 
                name: '{{ $team->name }}', 
                color: '{{ $team->color }}',
                event_id: '{{ $team->event_id }}'
            },
        @endforeach
    };
    
    const members = {
        @foreach($members as $member)
            '{{ $member->id }}': { 
                name: '{{ $member->name }}',
                team_id: '{{ $member->team_id }}',
                slack: {{ $member->slack_user_id ? 'true' : 'false' }}
            },
        @endforeach
    };
    
    // 文字数カウンター更新
    function updateCharacterCounter(input, counter, maxLength) {
        const currentLength = input.value.length;
        counter.textContent = `${currentLength} / ${maxLength}`;
        
        counter.classList.remove('warning', 'danger');
        if (currentLength > maxLength * 0.8) {
            counter.classList.add('warning');
        }
        if (currentLength > maxLength * 0.95) {
            counter.classList.add('danger');
        }
    }
    
    // 期限警告チェック
    function checkDueDateWarning() {
        const dueDateValue = dueDateInput.value;
        if (dueDateValue) {
            const dueDate = new Date(dueDateValue);
            const now = new Date();
            const diffHours = (dueDate - now) / (1000 * 60 * 60);
            
            if (diffHours <= 24 && diffHours > 0) {
                dueDateWarning.style.display = 'block';
            } else {
                dueDateWarning.style.display = 'none';
            }
        } else {
            dueDateWarning.style.display = 'none';
        }
    }
    
    // プレビュー更新
    function updatePreview() {
        // タイトル
        const title = titleInput.value || '新しいタスク';
        previewTitle.textContent = title;
        
        // 説明
        const description = descriptionInput.value;
        if (description) {
            previewDescription.textContent = description.substring(0, 100) + (description.length > 100 ? '...' : '');
            previewDescription.style.display = 'block';
        } else {
            previewDescription.textContent = '詳細が入力されると表示されます';
            previewDescription.style.display = 'block';
        }
        
        // イベント
        const eventId = eventSelect.value;
        if (eventId && events[eventId]) {
            previewEvent.textContent = events[eventId].name;
        } else {
            previewEvent.textContent = '未選択';
        }
        
        // チーム
        const teamId = teamSelect.value;
        if (teamId && teams[teamId]) {
            const team = teams[teamId];
            previewTeam.textContent = team.name;
            previewTeam.style.color = team.color;
        } else {
            previewTeam.textContent = '未選択';
            previewTeam.style.color = '#6c757d';
        }
        
        // 担当者
        const memberId = memberSelect.value;
        if (memberId && members[memberId]) {
            const member = members[memberId];
            previewAssignee.innerHTML = member.name + (member.slack ? ' <i class="fab fa-slack text-success"></i>' : '');
            previewAssignee.classList.remove('text-muted');
        } else {
            previewAssignee.textContent = '未割当';
            previewAssignee.classList.add('text-muted');
        }
        
        // ステータス
        const statusOption = statusSelect.options[statusSelect.selectedIndex];
        if (statusSelect.value && statusOption) {
            const statusColor = statusOption.dataset.color || '#6c757d';
            previewStatus.textContent = statusOption.textContent;
            previewStatus.style.backgroundColor = statusColor;
            previewStatus.classList.remove('bg-secondary');
        } else {
            previewStatus.textContent = '未設定';
            previewStatus.style.backgroundColor = '#6c757d';
            previewStatus.classList.add('bg-secondary');
        }
        
        // 期限
        const dueDate = dueDateInput.value;
        if (dueDate) {
            const date = new Date(dueDate);
            const now = new Date();
            const diffDays = Math.ceil((date - now) / (1000 * 60 * 60 * 24));
            
            let dateText = date.toLocaleString('ja-JP');
            if (diffDays < 0) {
                dateText += ' <span class="text-danger">(期限切れ)</span>';
            } else if (diffDays <= 1) {
                dateText += ' <span class="text-warning">(24時間以内)</span>';
            }
            
            previewDueDate.innerHTML = dateText;
            previewDueDate.classList.remove('text-muted');
        } else {
            previewDueDate.textContent = '未設定';
            previewDueDate.classList.add('text-muted');
        }
        
        // 優先度
        const priority = priorityHidden.value;
        const priorityTexts = {
            '1': '最低',
            '2': '低',
            '3': '中',
            '4': '高',
            '5': '最高'
        };
        const priorityColors = {
            '1': 'bg-success',
            '2': 'bg-info', 
            '3': 'bg-secondary',
            '4': 'bg-warning',
            '5': 'bg-danger'
        };
        
        previewPriorityIndicator.innerHTML = `<span class="badge ${priorityColors[priority]}">優先度: ${priorityTexts[priority]}</span>`;
    }
    
    // 優先度選択処理
    const priorityOptions = document.querySelectorAll('.priority-option');
    priorityOptions.forEach(option => {
        option.addEventListener('click', function() {
            priorityOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            const value = this.dataset.value;
            priorityHidden.value = value;
            this.querySelector('input[type="radio"]').checked = true;
            updatePreview();
        });
    });
    
    // イベント変更時のフィルタリング
    eventSelect.addEventListener('change', function() {
        const eventId = this.value;
        
        // チームフィルタリング
        Array.from(teamSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const teamEventId = option.dataset.eventId;
                option.style.display = eventId === '' || teamEventId === eventId ? 'block' : 'none';
            }
        });
        
        // ステータスフィルタリング
        Array.from(statusSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
            } else {
                const statusEventId = option.dataset.eventId;
                option.style.display = eventId === '' || statusEventId === eventId ? 'block' : 'none';
            }
        });
        
        teamSelect.value = '';
        memberSelect.value = '';
        statusSelect.value = '';
        updatePreview();
    });
    
    // チーム変更時のフィルタリング
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
        
        memberSelect.value = '';
        updatePreview();
    });
    
    // イベントリスナー設定
    titleInput.addEventListener('input', function() {
        updateCharacterCounter(this, titleCounter, 200);
        updatePreview();
    });
    
    descriptionInput.addEventListener('input', function() {
        updateCharacterCounter(this, descriptionCounter, 1000);
        updatePreview();
    });
    
    eventSelect.addEventListener('change', updatePreview);
    teamSelect.addEventListener('change', updatePreview);
    memberSelect.addEventListener('change', updatePreview);
    statusSelect.addEventListener('change', updatePreview);
    dueDateInput.addEventListener('change', function() {
        checkDueDateWarning();
        updatePreview();
    });
    
    // 初期化
    updateCharacterCounter(titleInput, titleCounter, 200);
    updateCharacterCounter(descriptionInput, descriptionCounter, 1000);
    updatePreview();
    checkDueDateWarning();
});
</script>
@endpush
