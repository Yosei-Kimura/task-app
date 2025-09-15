@extends('layouts.app')

@section('title', 'ステータス作成')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-plus me-2"></i>
                    新しい進捗ステータス作成
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('progress-statuses.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="event_id" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>
                            対象イベント <span class="text-danger">*</span>
                        </label>
                        <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror" required>
                            <option value="">イベントを選択してください</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id', request('event_id')) == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                    ({{ $event->start_date->format('Y/m/d') }} - {{ $event->end_date->format('Y/m/d') }})
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">ステータスは選択したイベントでのみ使用できます。</div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-tag me-1"></i>
                            ステータス名 <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required maxlength="50"
                               placeholder="例: 進行中、レビュー待ち、完了">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">最大50文字まで入力できます。</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>
                            説明
                        </label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="2" maxlength="255" placeholder="このステータスの詳細説明（任意）">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">最大255文字まで入力できます。</div>
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">
                            <i class="fas fa-palette me-1"></i>
                            ステータスカラー <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="color" name="color" id="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       value="{{ old('color', '#6c757d') }}" title="ステータスカラーを選択" required>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm color-preset" data-color="#6c757d" style="background-color: #6c757d; width: 30px; height: 30px; border-radius: 50%;" title="未着手"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#ffc107" style="background-color: #ffc107; width: 30px; height: 30px; border-radius: 50%;" title="進行中"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#17a2b8" style="background-color: #17a2b8; width: 30px; height: 30px; border-radius: 50%;" title="レビュー中"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#28a745" style="background-color: #28a745; width: 30px; height: 30px; border-radius: 50%;" title="完了"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#dc3545" style="background-color: #dc3545; width: 30px; height: 30px; border-radius: 50%;" title="保留・停止"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#6f42c1" style="background-color: #6f42c1; width: 30px; height: 30px; border-radius: 50%;" title="優先度高"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#fd7e14" style="background-color: #fd7e14; width: 30px; height: 30px; border-radius: 50%;" title="要確認"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#e83e8c" style="background-color: #e83e8c; width: 30px; height: 30px; border-radius: 50%;" title="緊急"></button>
                                </div>
                                <small class="form-text text-muted">推奨カラーをクリックするか、カラーピッカーで自由に選択できます</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="order" class="form-label">
                            <i class="fas fa-sort-numeric-up me-1"></i>
                            表示順序
                        </label>
                        <input type="number" name="order" id="order" class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', $nextOrder) }}" min="1" max="999"
                               placeholder="1">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            数値が小さいほど先頭に表示されます。同じイベント内の他のステータス：
                            <span id="existing-orders" class="text-muted"></span>
                        </div>
                    </div>

                    <!-- プレビュー -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-eye me-1"></i>
                            プレビュー
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">バッジ表示</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <span class="badge" style="background-color: #6c757d; font-size: 1rem;" id="preview-badge">
                                            新しいステータス
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">タスク表示例</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div style="width: 4px; height: 40px; background-color: #6c757d; margin-right: 10px;" id="preview-border"></div>
                                            <div>
                                                <h6 class="mb-1">サンプルタスク</h6>
                                                <span class="badge" style="background-color: #6c757d;" id="preview-task-badge">新しいステータス</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 既存ステータステンプレート -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-templates me-1"></i>
                            クイック作成テンプレート
                        </label>
                        <div class="row">
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-secondary w-100 template-btn" 
                                        data-name="未着手" data-color="#6c757d" data-description="まだ作業を開始していないタスク">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 20px; height: 20px; background-color: #6c757d; border-radius: 3px; margin-right: 8px;"></div>
                                        未着手
                                    </div>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-secondary w-100 template-btn" 
                                        data-name="進行中" data-color="#ffc107" data-description="現在作業中のタスク">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 20px; height: 20px; background-color: #ffc107; border-radius: 3px; margin-right: 8px;"></div>
                                        進行中
                                    </div>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-secondary w-100 template-btn" 
                                        data-name="レビュー中" data-color="#17a2b8" data-description="レビューや確認待ちのタスク">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 20px; height: 20px; background-color: #17a2b8; border-radius: 3px; margin-right: 8px;"></div>
                                        レビュー中
                                    </div>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-secondary w-100 template-btn" 
                                        data-name="完了" data-color="#28a745" data-description="作業が完了したタスク">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 20px; height: 20px; background-color: #28a745; border-radius: 3px; margin-right: 8px;"></div>
                                        完了
                                    </div>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted mt-2">テンプレートをクリックして設定を自動入力できます</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('progress-statuses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            戻る
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            ステータス作成
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
    const templateButtons = document.querySelectorAll('.template-btn');
    
    // プレビュー要素
    const previewBadge = document.getElementById('preview-badge');
    const previewTaskBadge = document.getElementById('preview-task-badge');
    const previewBorder = document.getElementById('preview-border');
    const existingOrdersSpan = document.getElementById('existing-orders');
    
    // 既存ステータスの順序情報
    const eventStatuses = {
        @foreach($events as $event)
            '{{ $event->id }}': {!! $event->progressStatuses->pluck('order')->toJson() !!},
        @endforeach
    };
    
    function updatePreview() {
        const name = nameInput.value || '新しいステータス';
        const color = colorInput.value;
        
        previewBadge.textContent = name;
        previewBadge.style.backgroundColor = color;
        previewTaskBadge.textContent = name;
        previewTaskBadge.style.backgroundColor = color;
        previewBorder.style.backgroundColor = color;
    }
    
    function updateExistingOrders() {
        const eventId = eventSelect.value;
        if (eventId && eventStatuses[eventId] && eventStatuses[eventId].length > 0) {
            existingOrdersSpan.textContent = eventStatuses[eventId].sort((a, b) => a - b).join(', ');
        } else {
            existingOrdersSpan.textContent = 'なし';
        }
    }
    
    // イベントリスナー
    nameInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    eventSelect.addEventListener('change', updateExistingOrders);
    
    // カラープリセット
    colorPresets.forEach(button => {
        button.addEventListener('click', function() {
            const color = this.getAttribute('data-color');
            colorInput.value = color;
            updatePreview();
        });
    });
    
    // テンプレートボタン
    templateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const color = this.getAttribute('data-color');
            const description = this.getAttribute('data-description');
            
            nameInput.value = name;
            colorInput.value = color;
            descriptionInput.value = description;
            updatePreview();
        });
    });
    
    // 初期表示
    updatePreview();
    updateExistingOrders();
});
</script>
@endsection
