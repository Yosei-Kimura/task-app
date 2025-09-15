@extends('layouts.app')

@section('title', 'イベント編集 - ' . $event->name)

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">ダッシュボード</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">イベント一覧</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.show', $event) }}">{{ Str::limit($event->name, 20) }}</a></li>
                <li class="breadcrumb-item active">編集</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-edit me-2"></i>
                イベント編集
            </h1>
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">
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
                    イベント情報の編集
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('events.update', $event) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">イベント名 <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $event->name) }}" 
                               placeholder="イベントの名前を入力してください" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">詳細</label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="イベントの詳細を入力してください（任意）">{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">開始日 <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">終了日</label>
                                <input type="date" name="end_date" id="end_date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d') : '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">終了日は任意です。設定しない場合は継続的なイベントとして扱われます。</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   value="1" {{ old('is_active', $event->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                アクティブ
                            </label>
                        </div>
                        <div class="form-text">アクティブなイベントのみダッシュボードに表示されます。</div>
                    </div>

                    @if($event->teams->count() > 0 || $event->tasks->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>注意:</strong> このイベントには{{ $event->teams->count() }}個のチームと{{ $event->tasks->count() }}個のタスクが関連付けられています。
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">
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

        <!-- 関連情報 -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    関連情報
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>チーム</h6>
                        @if($event->teams->count() > 0)
                            <ul class="list-unstyled">
                                @foreach($event->teams->take(5) as $team)
                                    <li>
                                        <span class="badge rounded-pill me-2" style="background-color: {{ $team->color }}">
                                            {{ $team->name }}
                                        </span>
                                        <small class="text-muted">({{ $team->members->count() }}名)</small>
                                    </li>
                                @endforeach
                                @if($event->teams->count() > 5)
                                    <li><small class="text-muted">他{{ $event->teams->count() - 5 }}個のチーム</small></li>
                                @endif
                            </ul>
                        @else
                            <p class="text-muted">チームがありません</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6>最近のタスク</h6>
                        @if($event->tasks->count() > 0)
                            <ul class="list-unstyled">
                                @foreach($event->tasks->sortByDesc('created_at')->take(5) as $task)
                                    <li>
                                        <small>{{ Str::limit($task->title, 30) }}</small>
                                        @if($task->progressStatus)
                                            <span class="badge ms-1" style="background-color: {{ $task->progressStatus->color }}; font-size: 0.6em;">
                                                {{ $task->progressStatus->name }}
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                                @if($event->tasks->count() > 5)
                                    <li><small class="text-muted">他{{ $event->tasks->count() - 5 }}個のタスク</small></li>
                                @endif
                            </ul>
                        @else
                            <p class="text-muted">タスクがありません</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 削除フォーム（危険な操作として別エリアに配置） -->
        @if($event->teams->count() == 0 && $event->tasks->count() == 0)
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        危険な操作
                    </h5>
                </div>
                <div class="card-body">
                    <p>このイベントには関連するチームやタスクがないため、削除が可能です。</p>
                    <p class="text-danger"><strong>注意: この操作は取り消せません。</strong></p>
                    
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>
                        イベントを削除
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- 削除確認モーダル -->
@if($event->teams->count() == 0 && $event->tasks->count() == 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">イベントの削除</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>本当にこのイベントを削除しますか？</p>
                <p><strong>{{ $event->name }}</strong></p>
                <p class="text-danger small">この操作は取り消せません。</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <form action="{{ route('events.destroy', $event) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">削除する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
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
