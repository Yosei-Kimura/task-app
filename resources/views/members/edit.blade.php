@extends('layouts.app')

@section('title', 'メンバー編集 - ' . $member->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    メンバー編集: {{ $member->name }}
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('members.update', $member) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="team_ids" class="form-label">
                            <i class="fas fa-users me-1"></i>
                            所属チーム <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            @foreach($teams as $team)
                                @php
                                    $isSelected = $member->teams->contains($team->id);
                                    $memberRole = $isSelected ? $member->teams->find($team->id)->pivot->role : 'member';
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="card {{ $isSelected ? 'border-primary' : '' }}">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="team_ids[]" 
                                                       value="{{ $team->id }}" id="team_{{ $team->id }}"
                                                       {{ $isSelected ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="team_{{ $team->id }}">
                                                    {{ $team->name }}
                                                </label>
                                            </div>
                                            <small class="text-muted">{{ $team->event->name }}</small>
                                            
                                            <div class="mt-2" id="role_section_{{ $team->id }}" style="display: {{ $isSelected ? 'block' : 'none' }}">
                                                <label class="form-label small">役割:</label>
                                                <select name="roles[{{ $team->id }}]" class="form-select form-select-sm">
                                                    <option value="member" {{ $memberRole === 'member' ? 'selected' : '' }}>メンバー</option>
                                                    <option value="leader" {{ $memberRole === 'leader' ? 'selected' : '' }}>リーダー</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('team_ids')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @if($member->assignedTasks->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>注意:</strong> このメンバーには{{ $member->assignedTasks->count() }}件のタスクが割り当てられています。
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-user me-1"></i>
                            名前 <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $member->name) }}" required maxlength="100"
                               placeholder="例: 田中 太郎">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>
                            メールアドレス
                        </label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $member->email) }}" maxlength="255"
                               placeholder="例: tanaka@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">通知やコミュニケーション用のメールアドレスです。</div>
                    </div>

                    <div class="mb-3">
                        <label for="slack_user_id" class="form-label">
                            <i class="fab fa-slack me-1"></i>
                            Slack ユーザーID
                        </label>
                        <input type="text" name="slack_user_id" id="slack_user_id" class="form-control @error('slack_user_id') is-invalid @enderror" 
                               value="{{ old('slack_user_id', $member->slack_user_id) }}" maxlength="50"
                               placeholder="例: U1234567890">
                        @error('slack_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <strong>Slack連携機能について:</strong><br>
                            SlackのユーザーIDを設定すると、タスク関連の通知を直接Slackで受け取れます。<br>
                            <small class="text-muted">
                                ユーザーIDの確認方法: Slackでプロフィール → その他 → メンバーIDをコピー
                            </small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">
                            <i class="fas fa-sticky-note me-1"></i>
                            備考・メモ
                        </label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" maxlength="1000" placeholder="スキル、専門分野、特記事項など">{{ old('notes', $member->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">最大1000文字まで入力できます。</div>
                    </div>

                    <!-- プレビュー -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-eye me-1"></i>
                            プレビュー
                        </label>
                        <div class="card" id="member-preview">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @php
                                        $primaryTeam = $member->teams->first();
                                        $primaryColor = $primaryTeam ? $primaryTeam->color : '#667eea';
                                    @endphp
                                    <div class="avatar me-3" style="width: 40px; height: 40px; background-color: {{ $primaryColor }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span class="text-white fw-bold" id="preview-avatar">{{ substr($member->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" id="preview-name">{{ $member->name }}</h6>
                                        <span id="preview-role-badge">
                                            @if($member->isLeader())
                                                <i class="fas fa-crown text-warning" title="リーダー"></i>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">所属チーム:</small>
                                    <div id="preview-teams">
                                        @foreach($member->teams as $team)
                                            <span class="badge rounded-pill me-1 mb-1" style="background-color: {{ $team->color }}">
                                                {{ $team->name }}
                                                @if($team->pivot->role === 'leader')
                                                    <i class="fas fa-crown ms-1"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="mb-2" id="preview-email-section" style="{{ $member->email ? 'display: block;' : 'display: none;' }}">
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i>
                                        <span id="preview-email">{{ $member->email }}</span>
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <span class="badge {{ $member->slack_user_id ? 'bg-success' : 'bg-secondary' }}" id="preview-slack-badge">
                                        <i class="fab fa-slack me-1"></i>
                                        {{ $member->slack_user_id ? 'Slack連携済み' : 'Slack未連携' }}
                                    </span>
                                </div>

                                <div id="preview-notes-section" style="{{ $member->notes ? 'display: block;' : 'display: none;' }}">
                                    <small class="text-muted">備考:</small>
                                    <p class="small" id="preview-notes">{{ $member->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- メンバー統計 -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-chart-bar me-1"></i>
                            現在の担当状況
                        </label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center bg-light">
                                    <div class="card-body py-3">
                                        <h4 class="text-primary">{{ $member->assignedTasks->count() }}</h4>
                                        <p class="card-text mb-0">担当タスク</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center bg-light">
                                    <div class="card-body py-3">
                                        <h4 class="text-success">{{ $member->assignedTasks->filter(function($task) { return $task->progressStatus && $task->progressStatus->name === '完了'; })->count() }}</h4>
                                        <p class="card-text mb-0">完了済み</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center bg-light">
                                    <div class="card-body py-3">
                                        <h4 class="text-warning">{{ $member->assignedTasks->filter(function($task) { return $task->due_date && $task->due_date->isPast() && (!$task->progressStatus || $task->progressStatus->name !== '完了'); })->count() }}</h4>
                                        <p class="card-text mb-0">期限切れ</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                戻る
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                更新
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const slackInput = document.getElementById('slack_user_id');
    const notesInput = document.getElementById('notes');
    
    // プレビュー要素
    const previewAvatar = document.getElementById('preview-avatar');
    const previewName = document.getElementById('preview-name');
    const previewEmailSection = document.getElementById('preview-email-section');
    const previewEmail = document.getElementById('preview-email');
    
    // チェックボックスとセクションの表示/非表示切り替え
    const teamCheckboxes = document.querySelectorAll('input[name="team_ids[]"]');
    teamCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const teamId = this.value;
            const roleSection = document.getElementById('role_section_' + teamId);
            
            if (this.checked) {
                roleSection.style.display = 'block';
            } else {
                roleSection.style.display = 'none';
            }
            
            updatePreview();
        });
    });
    
    function updatePreview() {
        const name = nameInput.value || '新しいメンバー';
        const email = emailInput.value;
        
        // 名前とアバター
        previewName.textContent = name;
        previewAvatar.textContent = name.substring(0, 2);
        
        // メール
        if (email) {
            previewEmail.textContent = email;
            previewEmailSection.style.display = 'block';
        } else {
            previewEmailSection.style.display = 'none';
        }
        
        // 選択されたチームを更新
        updateTeamPreview();
    }
    
    function updateTeamPreview() {
        const checkedBoxes = document.querySelectorAll('input[name="team_ids[]"]:checked');
        const previewTeamsDiv = document.getElementById('preview-teams');
        
        previewTeamsDiv.innerHTML = '';
        
        checkedBoxes.forEach(function(checkbox) {
            const teamId = checkbox.value;
            const teamName = checkbox.parentElement.querySelector('label').textContent.trim();
            const roleSelect = document.querySelector(`select[name="roles[${teamId}]"]`);
            const role = roleSelect ? roleSelect.value : 'member';
            
            const badge = document.createElement('span');
            badge.className = 'badge rounded-pill me-1 mb-1';
            badge.style.backgroundColor = '#667eea'; // デフォルト色
            badge.innerHTML = teamName + (role === 'leader' ? ' <i class="fas fa-crown ms-1"></i>' : '');
            
            previewTeamsDiv.appendChild(badge);
        });
    }
    
    // イベントリスナー設定
    nameInput.addEventListener('input', updatePreview);
    emailInput.addEventListener('input', updatePreview);
    if (slackInput) slackInput.addEventListener('input', updatePreview);
    if (notesInput) notesInput.addEventListener('input', updatePreview);
    
    // 役割セレクトボックスの変更監視
    document.addEventListener('change', function(e) {
        if (e.target.name && e.target.name.startsWith('roles[')) {
            updatePreview();
        }
    });
    
    // 初期状態のプレビュー更新
    updatePreview();
});
</script>
        } else {
            previewTeam.textContent = 'チームを選択してください';
            previewTeam.style.backgroundColor = '#6c757d';
            document.querySelector('#member-preview .avatar').style.backgroundColor = '#007bff';
        }
        
        // メールアドレス
        if (email) {
            previewEmail.textContent = email;
            previewEmailSection.style.display = 'block';
        } else {
            previewEmailSection.style.display = 'none';
        }
        
        // Slack連携
        if (slackId) {
            previewSlackBadge.className = 'badge bg-success';
            previewSlackBadge.innerHTML = '<i class="fab fa-slack me-1"></i>Slack連携済み';
        } else {
            previewSlackBadge.className = 'badge bg-secondary';
            previewSlackBadge.innerHTML = '<i class="fab fa-slack me-1"></i>Slack未連携';
        }
        
        // 備考
        if (notes) {
            previewNotes.textContent = notes;
            previewNotesSection.style.display = 'block';
        } else {
            previewNotesSection.style.display = 'none';
        }
    }
    
    // イベントリスナー
    nameInput.addEventListener('input', updatePreview);
    emailInput.addEventListener('input', updatePreview);
    teamSelect.addEventListener('change', updatePreview);
    roleInputs.forEach(input => input.addEventListener('change', updatePreview));
    slackInput.addEventListener('input', updatePreview);
    notesInput.addEventListener('input', updatePreview);
});
</script>
@endsection
