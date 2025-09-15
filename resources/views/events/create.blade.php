@extends('layouts.app')

@section('title', '新しいイベント')

@push('styles')
<style>
.form-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
    backdrop-filter: blur(10px);
}

.form-inner {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.form-control-custom {
    border: 2px solid #e3e7f3;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
}

.form-control-custom:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: translateY(-2px);
}

.form-label-custom {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.75rem;
}

.info-card {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: none;
    border-radius: 15px;
    padding: 1.5rem;
}

.date-input-group {
    position: relative;
}

.date-input-group::before {
    content: '\f073';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    pointer-events: none;
    z-index: 10;
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="custom-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ダッシュボード</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">イベント一覧</a></li>
                <li class="breadcrumb-item active">新しいイベント</li>
            </ol>
        </nav>

        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-plus-circle me-3"></i>
                        新しいイベント
                    </h1>
                    <p class="page-subtitle">新しいプロジェクトイベントを作成します</p>
                </div>
                <a href="{{ route('events.index') }}" class="btn-custom btn-custom-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    戻る
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="form-container">
            <div class="form-inner">
                <div class="text-center mb-4">
                    <h4 class="form-title">
                        <i class="fas fa-edit me-2"></i>
                        イベント情報入力
                    </h4>
                    <p class="form-subtitle">必要な情報を入力してイベントを作成してください</p>
                </div>

                <form action="{{ route('events.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label form-label-custom">
                            <i class="fas fa-project-diagram me-2"></i>
                            イベント名 <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" 
                               class="form-control form-control-custom @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="イベントの名前を入力してください" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label form-label-custom">
                            <i class="fas fa-align-left me-2"></i>
                            詳細説明
                        </label>
                        <textarea name="description" id="description" 
                                  class="form-control form-control-custom @error('description') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="イベントの詳細や目的を入力してください（任意）">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="start_date" class="form-label form-label-custom">
                                    <i class="fas fa-play-circle me-2"></i>
                                    開始日 <span class="text-danger">*</span>
                                </label>
                                <div class="date-input-group">
                                    <input type="date" name="start_date" id="start_date" 
                                           class="form-control form-control-custom @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date') }}" required>
                                </div>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="end_date" class="form-label form-label-custom">
                                    <i class="fas fa-stop-circle me-2"></i>
                                    終了日（任意）
                                </label>
                                <div class="date-input-group">
                                    <input type="date" name="end_date" id="end_date" 
                                           class="form-control form-control-custom @error('end_date') is-invalid @enderror" 
                                           value="{{ old('end_date') }}">
                                </div>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    終了日を設定しない場合は継続的なイベントとして扱われます
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check-custom">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-toggle-on me-2"></i>
                                アクティブ状態で作成
                            </label>
                        </div>
                        <div class="form-text text-muted">
                            アクティブなイベントのみダッシュボードに表示されます
                        </div>
                    </div>

                    <div class="info-card mb-4">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-lightbulb fa-2x text-primary me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold text-primary mb-2">イベント作成後の自動設定</h6>
                                <ul class="mb-0 text-muted">
                                    <li>デフォルトの進捗状況（未着手、作業中、レビュー中、完了）が自動作成されます</li>
                                    <li>作成後にチームとメンバーを追加してプロジェクトを開始できます</li>
                                    <li>進捗状況は後からカスタマイズ可能です</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('events.index') }}" class="btn-custom btn-custom-secondary">
                            <i class="fas fa-times me-2"></i>
                            キャンセル
                        </a>
                        <button type="submit" class="btn-custom btn-custom-primary">
                            <i class="fas fa-save me-2"></i>
                            イベントを作成
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    // 開始日が変更されたら終了日の最小値を設定
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = '';
        }
    });

    // 終了日が開始日より前に設定されないようにチェック
    endDateInput.addEventListener('change', function() {
        if (this.value && startDateInput.value && this.value < startDateInput.value) {
            alert('終了日は開始日以降の日付を選択してください。');
            this.value = '';
        }
    });
});
</script>
@endpush
