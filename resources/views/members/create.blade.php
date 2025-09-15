@extends('layouts.app')

@section('title', 'メンバー追加')

@push('styles')
<style>
.form-wizard {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    color: white;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.form-wizard .card-body {
    background: rgba(255, 255, 255, 0.98);
    margin: 1.5rem;
    border-radius: 20px;
    color: #2d3748;
}

.form-section {
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e7f3 100%);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 5px solid #667eea;
}

.form-section-title {
    color: #4a5568;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.form-section-title i {
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

.form-control, .form-select {
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

.form-check {
    background: white;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    border: 2px solid rgba(102, 126, 234, 0.1);
    transition: all 0.3s ease;
}

.form-check:hover {
    border-color: rgba(102, 126, 234, 0.3);
    transform: translateY(-2px);
}

.form-check-input:checked + .form-check-label {
    color: #667eea;
    font-weight: 600;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.preview-card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
    overflow: hidden;
    background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
}

.preview-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
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
    color: white;
    margin-bottom: 1rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.preview-info {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    margin: 1rem 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.role-badge {
    border-radius: 20px;
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
}

.slack-status {
    border-radius: 15px;
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.form-text {
    background: rgba(102, 126, 234, 0.05);
    border-radius: 8px;
    padding: 8px 12px;
    margin-top: 8px;
    border-left: 4px solid #667eea;
}

.btn-back {
    background: linear-gradient(135deg, #a8b2d1 0%, #c8d0e6 100%);
    color: #4a5568;
    border: none;
    border-radius: 25px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.btn-back:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
    color: #4a5568;
}

.btn-submit {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: none;
    border-radius: 25px;
    padding: 12px 28px;
    font-weight: 600;
    transition: all 0.4s ease;
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-submit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.btn-submit:hover::before {
    left: 100%;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(79, 172, 254, 0.4);
}

.form-validation {
    border-radius: 10px;
    padding: 10px 15px;
    margin-top: 5px;
    background: linear-gradient(135deg, #fee 0%, #fdd 100%);
    border-left: 4px solid #dc3545;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.character-counter {
    font-size: 0.8rem;
    color: #718096;
    text-align: right;
    margin-top: 5px;
}

.character-counter.warning {
    color: #f6ad55;
}

.character-counter.danger {
    color: #fc8181;
}

.required-asterisk {
    color: #e53e3e;
    font-weight: bold;
    margin-left: 3px;
}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-user-plus me-3"></i>
                        新しいメンバー追加
                    </h1>
                    <p class="page-subtitle">チームに新しいメンバーを追加します</p>
                </div>
            </div>
        </div>

        <div class="card form-wizard">
            <div class="card-header text-center">
                <h4 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    メンバー登録フォーム
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('members.store') }}" id="memberForm">
                    @csrf

                    <div class="row">
                        <!-- フォーム入力エリア -->
                        <div class="col-lg-8">
                            <!-- 基本情報セクション -->
                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="fas fa-user"></i>
                                    基本情報
                                </h5>

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        氏名<span class="required-asterisk">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required maxlength="100"
                                           placeholder="例: 田中 太郎">
                                    <div class="character-counter" id="nameCounter">0 / 100</div>
                                    @error('name')
                                        <div class="form-validation">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2"></i>
                                        メールアドレス
                                    </label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" maxlength="255"
                                           placeholder="例: tanaka@example.com">
                                    @error('email')
                                        <div class="form-validation">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        通知やコミュニケーション用のメールアドレスです
                                    </div>
                                </div>
                            </div>

                            <!-- チームと役割セクション -->
                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="fas fa-users"></i>
                                    チーム・役割設定
                                </h5>

                                <div class="mb-3">
                                    <label for="team_id" class="form-label fw-bold">
                                        所属チーム<span class="required-asterisk">*</span>
                                    </label>
                                    <select name="team_id" id="team_id" class="form-select @error('team_id') is-invalid @enderror" required>
                                        <option value="">チームを選択してください</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" 
                                                    data-color="{{ $team->color }}"
                                                    {{ old('team_id', request('team_id')) == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }} ({{ $team->event->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('team_id')
                                        <div class="form-validation">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        役割<span class="required-asterisk">*</span>
                                    </label>
                                    <div class="form-check">
                                        <input type="radio" name="role" id="role_member" value="member" 
                                               class="form-check-input @error('role') is-invalid @enderror" 
                                               {{ old('role', 'member') === 'member' ? 'checked' : '' }} required>
                                        <label for="role_member" class="form-check-label">
                                            <strong>メンバー</strong>
                                            <small class="text-muted d-block">一般的なチームメンバーとして参加</small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="role" id="role_leader" value="leader" 
                                               class="form-check-input @error('role') is-invalid @enderror" 
                                               {{ old('role') === 'leader' ? 'checked' : '' }}>
                                        <label for="role_leader" class="form-check-label">
                                            <strong>リーダー</strong> <i class="fas fa-crown text-warning"></i>
                                            <small class="text-muted d-block">チームの統括・管理責任者として参加</small>
                                        </label>
                                    </div>
                                    @error('role')
                                        <div class="form-validation">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Slack連携セクション -->
                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="fab fa-slack"></i>
                                    Slack連携設定
                                </h5>

                                <div class="mb-3">
                                    <label for="slack_user_id" class="form-label fw-bold">
                                        <i class="fab fa-slack me-2"></i>
                                        Slack ユーザーID
                                    </label>
                                    <input type="text" name="slack_user_id" id="slack_user_id" 
                                           class="form-control @error('slack_user_id') is-invalid @enderror" 
                                           value="{{ old('slack_user_id') }}" maxlength="50"
                                           placeholder="例: U1234567890">
                                    @error('slack_user_id')
                                        <div class="form-validation">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Slack連携機能について:</strong><br>
                                        SlackのユーザーIDを設定すると、タスク関連の通知を直接Slackで受け取れます<br>
                                        <small>ユーザーIDの確認方法: Slackでプロフィール → その他 → メンバーIDをコピー</small>
                                    </div>
                                </div>
                            </div>

                            <!-- その他情報セクション -->
                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="fas fa-sticky-note"></i>
                                    その他情報
                                </h5>

                                <div class="mb-3">
                                    <label for="notes" class="form-label fw-bold">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        備考・メモ
                                    </label>
                                    <textarea name="notes" id="notes" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              rows="4" maxlength="1000" 
                                              placeholder="スキル、専門分野、特記事項など">{{ old('notes') }}</textarea>
                                    <div class="character-counter" id="notesCounter">0 / 1000</div>
                                    @error('notes')
                                        <div class="form-validation">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        スキル、専門分野、連絡事項など自由に記載してください
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- プレビューエリア -->
                        <div class="col-lg-4">
                            <div class="position-sticky" style="top: 2rem;">
                                <h5 class="text-center mb-3">
                                    <i class="fas fa-eye me-2"></i>
                                    プレビュー
                                </h5>
                                <div class="card preview-card">
                                    <div class="card-body text-center">
                                        <div class="member-avatar-large mx-auto" id="preview-avatar">
                                            新
                                        </div>
                                        
                                        <h5 class="fw-bold mb-2" id="preview-name">新しいメンバー</h5>
                                        
                                        <div class="mb-3" id="preview-role-section">
                                            <span class="role-badge bg-secondary" id="preview-role-badge">メンバー</span>
                                        </div>

                                        <div class="preview-info">
                                            <div class="mb-2">
                                                <small class="text-muted">所属チーム:</small><br>
                                                <span class="badge" style="background-color: #6c757d" id="preview-team">
                                                    チーム未選択
                                                </span>
                                            </div>
                                            
                                            <div class="mb-2" id="preview-email-section" style="display: none;">
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    <span id="preview-email"></span>
                                                </small>
                                            </div>

                                            <div class="mb-2">
                                                <span class="slack-status bg-secondary text-white" id="preview-slack-badge">
                                                    <i class="fab fa-slack me-1"></i>
                                                    未連携
                                                </span>
                                            </div>

                                            <div id="preview-notes-section" style="display: none;">
                                                <small class="text-muted">備考:</small>
                                                <div class="mt-1 p-2 bg-light rounded" id="preview-notes"></div>
                                            </div>
                                        </div>

                                        <div class="text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            登録日: {{ now()->format('Y年m月d日') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-4 border-top">
                        <a href="{{ route('members.index') }}" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i>
                            メンバー一覧に戻る
                        </a>
                        <button type="submit" class="btn btn-submit text-white">
                            <i class="fas fa-user-plus me-2"></i>
                            メンバーを登録
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // フォーム要素の取得
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const teamSelect = document.getElementById('team_id');
    const roleInputs = document.querySelectorAll('input[name="role"]');
    const slackInput = document.getElementById('slack_user_id');
    const notesInput = document.getElementById('notes');
    
    // プレビュー要素の取得
    const previewAvatar = document.getElementById('preview-avatar');
    const previewName = document.getElementById('preview-name');
    const previewRoleBadge = document.getElementById('preview-role-badge');
    const previewTeam = document.getElementById('preview-team');
    const previewEmail = document.getElementById('preview-email');
    const previewEmailSection = document.getElementById('preview-email-section');
    const previewSlackBadge = document.getElementById('preview-slack-badge');
    const previewNotes = document.getElementById('preview-notes');
    const previewNotesSection = document.getElementById('preview-notes-section');
    
    // 文字数カウンター
    const nameCounter = document.getElementById('nameCounter');
    const notesCounter = document.getElementById('notesCounter');
    
    // チーム情報
    const teams = {
        @foreach($teams as $team)
            '{{ $team->id }}': {
                name: '{{ $team->name }}',
                color: '{{ $team->color }}',
                event: '{{ $team->event->name }}'
            },
        @endforeach
    };
    
    // 文字数カウンター更新関数
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
    
    // プレビュー更新関数
    function updatePreview() {
        const name = nameInput.value || '新しいメンバー';
        const email = emailInput.value;
        const teamId = teamSelect.value;
        const role = document.querySelector('input[name="role"]:checked')?.value || 'member';
        const slackId = slackInput.value;
        const notes = notesInput.value;
        
        // 名前とアバター更新
        previewName.textContent = name;
        previewAvatar.textContent = name.substring(0, 1) || '新';
        
        // 役割バッジ更新
        if (role === 'leader') {
            previewRoleBadge.className = 'role-badge bg-warning text-dark';
            previewRoleBadge.innerHTML = '<i class="fas fa-crown me-1"></i>リーダー';
        } else {
            previewRoleBadge.className = 'role-badge bg-primary text-white';
            previewRoleBadge.innerHTML = '<i class="fas fa-user me-1"></i>メンバー';
        }
        
        // チーム情報更新
        if (teamId && teams[teamId]) {
            const team = teams[teamId];
            previewTeam.textContent = team.name;
            previewTeam.style.backgroundColor = team.color;
            previewTeam.style.color = 'white';
            
            // アバターの背景色も更新
            previewAvatar.style.background = `linear-gradient(135deg, ${team.color} 0%, ${team.color}80 100%)`;
        } else {
            previewTeam.textContent = 'チーム未選択';
            previewTeam.style.backgroundColor = '#6c757d';
            previewTeam.style.color = 'white';
            
            // デフォルトのアバター色
            previewAvatar.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }
        
        // メールアドレス更新
        if (email.trim()) {
            previewEmail.textContent = email;
            previewEmailSection.style.display = 'block';
        } else {
            previewEmailSection.style.display = 'none';
        }
        
        // Slack連携状況更新
        if (slackId.trim()) {
            previewSlackBadge.className = 'slack-status bg-success text-white';
            previewSlackBadge.innerHTML = '<i class="fab fa-slack me-1"></i>連携済み';
        } else {
            previewSlackBadge.className = 'slack-status bg-secondary text-white';
            previewSlackBadge.innerHTML = '<i class="fab fa-slack me-1"></i>未連携';
        }
        
        // 備考更新
        if (notes.trim()) {
            previewNotes.textContent = notes;
            previewNotesSection.style.display = 'block';
        } else {
            previewNotesSection.style.display = 'none';
        }
    }
    
    // フォームバリデーション
    function validateForm() {
        let isValid = true;
        
        // 名前のバリデーション
        if (!nameInput.value.trim()) {
            nameInput.classList.add('is-invalid');
            isValid = false;
        } else {
            nameInput.classList.remove('is-invalid');
        }
        
        // チーム選択のバリデーション
        if (!teamSelect.value) {
            teamSelect.classList.add('is-invalid');
            isValid = false;
        } else {
            teamSelect.classList.remove('is-invalid');
        }
        
        return isValid;
    }
    
    // イベントリスナーの設定
    nameInput.addEventListener('input', function() {
        updateCharacterCounter(this, nameCounter, 100);
        updatePreview();
    });
    
    notesInput.addEventListener('input', function() {
        updateCharacterCounter(this, notesCounter, 1000);
        updatePreview();
    });
    
    emailInput.addEventListener('input', updatePreview);
    teamSelect.addEventListener('change', updatePreview);
    roleInputs.forEach(input => input.addEventListener('change', updatePreview));
    slackInput.addEventListener('input', updatePreview);
    
    // フォーム送信時のバリデーション
    document.getElementById('memberForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            // エラーのあるフィールドにフォーカス
            document.querySelector('.is-invalid')?.focus();
        }
    });
    
    // 初期状態の更新
    updateCharacterCounter(nameInput, nameCounter, 100);
    updateCharacterCounter(notesInput, notesCounter, 1000);
    updatePreview();
    
    // フォーカス時の効果
    const formControls = document.querySelectorAll('.form-control, .form-select');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        control.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection
