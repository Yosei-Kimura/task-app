@extends('layouts.app')

@section('title', 'チーム作成')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    新しいチーム作成
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('teams.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="event_id" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>
                            イベント <span class="text-danger">*</span>
                        </label>
                        <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                            <option value="">イベントを選択してください</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id', request('event_id')) == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }} ({{ $event->start_date->format('Y/m/d') }} - {{ $event->end_date->format('Y/m/d') }})
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-users me-1"></i>
                            チーム名 <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required maxlength="100"
                               placeholder="例: フロントエンドチーム">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>
                            説明
                        </label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" maxlength="500" placeholder="チームの役割や責任について説明してください">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">最大500文字まで入力できます。</div>
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">
                            <i class="fas fa-palette me-1"></i>
                            チームカラー
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="color" name="color" id="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       value="{{ old('color', '#007bff') }}" title="チームカラーを選択">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm color-preset" data-color="#007bff" style="background-color: #007bff; width: 30px; height: 30px; border-radius: 50%;"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#28a745" style="background-color: #28a745; width: 30px; height: 30px; border-radius: 50%;"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#dc3545" style="background-color: #dc3545; width: 30px; height: 30px; border-radius: 50%;"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#ffc107" style="background-color: #ffc107; width: 30px; height: 30px; border-radius: 50%;"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#17a2b8" style="background-color: #17a2b8; width: 30px; height: 30px; border-radius: 50%;"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#6f42c1" style="background-color: #6f42c1; width: 30px; height: 30px; border-radius: 50%;"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#e83e8c" style="background-color: #e83e8c; width: 30px; height: 30px; border-radius: 50%;"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#fd7e14" style="background-color: #fd7e14; width: 30px; height: 30px; border-radius: 50%;"></button>
                                </div>
                                <small class="form-text text-muted">プリセットカラーをクリックするか、カラーピッカーで自由に選択できます</small>
                            </div>
                        </div>
                    </div>

                    <!-- プレビュー -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-eye me-1"></i>
                            プレビュー
                        </label>
                        <div class="card" id="team-preview">
                            <div class="card-header d-flex justify-content-between align-items-center" 
                                 style="background-color: #007bff20; border-bottom: 2px solid #007bff">
                                <h5 class="card-title mb-0">
                                    <span class="badge rounded-pill me-2" style="background-color: #007bff" id="preview-badge">
                                        新しいチーム
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6 class="text-muted" id="preview-event">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    イベントを選択してください
                                </h6>
                                <p class="card-text" id="preview-description">チームの説明がここに表示されます</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('teams.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            戻る
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            チーム作成
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const colorInput = document.getElementById('color');
    const eventSelect = document.getElementById('event_id');
    const colorPresets = document.querySelectorAll('.color-preset');
    
    // プレビュー要素
    const previewBadge = document.getElementById('preview-badge');
    const previewEvent = document.getElementById('preview-event');
    const previewDescription = document.getElementById('preview-description');
    const previewCard = document.getElementById('team-preview');
    
    function updatePreview() {
        const name = nameInput.value || '新しいチーム';
        const description = descriptionInput.value || 'チームの説明がここに表示されます';
        const color = colorInput.value;
        const eventText = eventSelect.options[eventSelect.selectedIndex].text;
        
        previewBadge.textContent = name;
        previewBadge.style.backgroundColor = color;
        
        if (eventSelect.value) {
            previewEvent.innerHTML = '<i class="fas fa-calendar-alt me-1"></i>' + eventText;
        } else {
            previewEvent.innerHTML = '<i class="fas fa-calendar-alt me-1"></i>イベントを選択してください';
        }
        
        previewDescription.textContent = description;
        
        const cardHeader = previewCard.querySelector('.card-header');
        cardHeader.style.backgroundColor = color + '20';
        cardHeader.style.borderBottomColor = color;
    }
    
    // イベントリスナー
    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    eventSelect.addEventListener('change', updatePreview);
    
    // カラープリセット
    colorPresets.forEach(button => {
        button.addEventListener('click', function() {
            const color = this.getAttribute('data-color');
            colorInput.value = color;
            updatePreview();
        });
    });
    
    // 初期プレビュー更新
    updatePreview();
});
</script>
@endsection
