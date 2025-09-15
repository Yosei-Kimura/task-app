@extends('layouts.app')

@section('title', '進捗ステータス管理')

@push('styles')
<style>
/* 進捗ステータス管理ページ専用スタイル */
.status-management-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    margin-bottom: 2rem;
}

.status-card {
    border-radius: 20px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    background: white;
}

.status-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: var(--status-color, #667eea);
    z-index: 1;
}

.status-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 35px 60px rgba(0, 0, 0, 0.15);
}

.status-badge-large {
    font-size: 1rem;
    padding: 12px 20px;
    border-radius: 30px;
    font-weight: 700;
    letter-spacing: 0.5px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    border: 3px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.stats-mini {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-radius: 15px;
    padding: 16px 12px;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.stats-mini:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
    transform: scale(1.05) translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.stats-mini h6 {
    margin-bottom: 4px;
    font-weight: 800;
    font-size: 1.2rem;
}

.event-section {
    margin-bottom: 3rem;
}

.event-header {
    background: linear-gradient(135deg, var(--event-color, #667eea) 0%, var(--event-color-dark, #764ba2) 100%);
    color: white;
    border-radius: 20px 20px 0 0;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 30px;
    padding: 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0 0 20px 20px;
}

.drag-handle {
    cursor: grab;
    color: #6c757d;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.drag-handle:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    transform: scale(1.1);
}

.drag-handle:active {
    cursor: grabbing;
}

.task-list-mini {
    max-height: 120px;
    overflow-y: auto;
    margin-top: 15px;
    padding: 10px;
    background: rgba(248, 249, 250, 0.7);
    border-radius: 12px;
    backdrop-filter: blur(5px);
}

.task-list-mini::-webkit-scrollbar {
    width: 6px;
}

.task-list-mini::-webkit-scrollbar-track {
    background: rgba(233, 236, 239, 0.5);
    border-radius: 6px;
}

.task-list-mini::-webkit-scrollbar-thumb {
    background: rgba(102, 126, 234, 0.5);
    border-radius: 6px;
}

.task-list-mini::-webkit-scrollbar-thumb:hover {
    background: rgba(102, 126, 234, 0.7);
}

.task-item-mini {
    padding: 6px 10px;
    border-radius: 8px;
    margin-bottom: 6px;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.task-item-mini:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.color-picker-preview {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    transition: all 0.2s ease;
}

.color-picker-preview:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

.filter-section {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 249, 250, 0.9) 100%);
    border-radius: 20px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    backdrop-filter: blur(10px);
}

.progress-indicator {
    position: absolute;
    right: 15px;
    top: 15px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .status-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 20px;
    }
    
    .event-header {
        padding: 25px 20px;
    }
    
    .status-badge-large {
        font-size: 0.9rem;
        padding: 10px 16px;
    }
}

.sortable-ghost {
    opacity: 0.5;
    transform: rotate(5deg);
}

.sortable-chosen {
    transform: scale(1.05);
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- メインヘッダー -->
    <div class="card status-management-header border-0 mb-4">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="mb-2 fw-bold">
                        <i class="fas fa-tasks me-3"></i>
                        進捗ステータス管理
                    </h1>
                    <p class="mb-0 lead opacity-90">
                        プロジェクトの進捗フローを管理し、ステータスの順序や色分けをカスタマイズできます
                    </p>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-3">
                        <div class="text-center">
                            <div class="progress-indicator">
                                <span>{{ $events->count() }}</span>
                            </div>
                            <small class="d-block mt-1 opacity-75">イベント</small>
                        </div>
                        <div class="text-center">
                            <div class="progress-indicator">
                                <span>{{ $events->sum(function($event) { return $event->progressStatuses->count(); }) }}</span>
                            </div>
                            <small class="d-block mt-1 opacity-75">ステータス</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($events->isEmpty())
        <div class="empty-state mx-auto" style="max-width: 600px;">
            <i class="fas fa-clipboard-list fa-6x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">イベントがまだ作成されていません</h3>
            <p class="text-muted mb-4">
                進捗ステータスを管理するには、まずイベントを作成してください。<br>
                イベントごとに独自のワークフローとステータスを設定できます。
            </p>
            <a href="{{ route('events.create') }}" class="btn btn-primary btn-lg rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>最初のイベントを作成
            </a>
        </div>
    @else
        <!-- フィルターセクション -->
        <div class="card filter-section border-0 mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-3 fw-bold text-dark">
                            <i class="fas fa-filter me-2 text-primary"></i>イベントフィルター
                        </h5>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="eventFilter" id="all" value="all" checked>
                            <label class="btn btn-outline-primary rounded-pill me-2" for="all">
                                すべて ({{ $events->count() }})
                            </label>
                            
                            @foreach($events as $event)
                                <input type="radio" class="btn-check" name="eventFilter" id="event-{{ $event->id }}" value="{{ $event->id }}">
                                <label class="btn btn-outline-primary rounded-pill me-2" for="event-{{ $event->id }}" 
                                       style="--event-color: {{ $event->color }}">
                                    {{ $event->name }}
                                    ({{ $event->progressStatuses->count() }})
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-light rounded-pill" id="expandAll">
                                <i class="fas fa-expand-alt me-2"></i>すべて展開
                            </button>
                            <button class="btn btn-light rounded-pill" id="collapseAll">
                                <i class="fas fa-compress-alt me-2"></i>すべて縮小
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- イベントごとのステータス表示 -->
        @foreach($events as $event)
            <div class="event-section" data-event-id="{{ $event->id }}">
                <div class="card border-0">
                    <div class="event-header" 
                         style="--event-color: {{ $event->color }}; --event-color-dark: {{ $this->darkenColor($event->color ?? '#667eea', 20) }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-2 fw-bold">
                                    <i class="fas fa-calendar-alt me-3"></i>
                                    {{ $event->name }}
                                </h3>
                                <div class="d-flex gap-3">
                                    <div class="stats-mini bg-white bg-opacity-25">
                                        <h6 class="text-white mb-0">{{ $event->progressStatuses->count() }}</h6>
                                        <small class="text-white-50">ステータス</small>
                                    </div>
                                    <div class="stats-mini bg-white bg-opacity-25">
                                        <h6 class="text-white mb-0">{{ $event->tasks->count() }}</h6>
                                        <small class="text-white-50">タスク</small>
                                    </div>
                                    <div class="stats-mini bg-white bg-opacity-25">
                                        <h6 class="text-white mb-0">{{ number_format($event->tasks->where('completed', true)->count() / max($event->tasks->count(), 1) * 100, 0) }}%</h6>
                                        <small class="text-white-50">完了率</small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('progress-statuses.create') }}?event_id={{ $event->id }}" 
                                   class="btn btn-light rounded-pill px-4">
                                    <i class="fas fa-plus me-2"></i>
                                    ステータス追加
                                </a>
                                <a href="{{ route('events.show', $event) }}" 
                                   class="btn btn-outline-light rounded-pill px-4">
                                    <i class="fas fa-eye me-2"></i>
                                    詳細表示
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="status-grid sortable" id="status-grid-{{ $event->id }}">
                        @forelse($event->progressStatuses->sortBy('order') as $status)
                            <div class="status-card card h-100 sortable-item" 
                                 data-status-id="{{ $status->id }}"
                                 style="--status-color: {{ $status->color }}">
                                <div class="card-body position-relative">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="drag-handle">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </div>
                                                <span class="badge status-badge-large fw-bold" 
                                                      style="background-color: {{ $status->color }};">
                                                    {{ $status->name }}
                                                </span>
                                                <small class="text-muted opacity-75">#{{ $status->order }}</small>
                                            </div>
                                            @if($status->description)
                                                <p class="card-text small text-muted mb-0">{{ $status->description }}</p>
                                            @endif
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('progress-statuses.show', $status) }}">
                                                    <i class="fas fa-eye me-2"></i>詳細表示
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('progress-statuses.edit', $status) }}">
                                                    <i class="fas fa-edit me-2"></i>編集
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" 
                                                       onclick="confirmDelete('{{ $status->name }}', '{{ route('progress-statuses.destroy', $status) }}')">
                                                    <i class="fas fa-trash me-2"></i>削除
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <!-- 使用統計 -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-4">
                                            <div class="stats-mini">
                                                <h6 class="text-primary mb-0">{{ $status->tasks->count() }}</h6>
                                                <small class="text-muted">使用中</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stats-mini">
                                                <h6 class="text-info mb-0">{{ $status->tasks->where('created_at', '>=', now()->subDays(7))->count() }}</h6>
                                                <small class="text-muted">7日間</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stats-mini">
                                                <h6 class="text-success mb-0">{{ $status->tasks->where('updated_at', '>=', now()->subDay())->count() }}</h6>
                                                <small class="text-muted">24時間</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 最近のタスク -->
                                    @if($status->tasks->count() > 0)
                                        <div class="task-list-section">
                                            <small class="text-muted fw-bold d-block mb-2">
                                                <i class="fas fa-tasks me-1"></i>最近のタスク:
                                            </small>
                                            <div class="task-list-mini">
                                                @foreach($status->tasks->sortByDesc('updated_at')->take(3) as $task)
                                                    <div class="task-item-mini d-flex justify-content-between align-items-center">
                                                        <a href="{{ route('tasks.show', $task) }}" 
                                                           class="text-decoration-none text-truncate flex-grow-1">
                                                            {{ Str::limit($task->title, 25) }}
                                                        </a>
                                                        <small class="text-muted">{{ $task->updated_at->format('m/d') }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-3 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 opacity-25"></i>
                                            <p class="mb-0 small">未使用のステータスです</p>
                                        </div>
                                    @endif

                                    <!-- カラーピッカープレビュー -->
                                    <div class="position-absolute" style="bottom: 15px; right: 15px;">
                                        <div class="color-picker-preview" 
                                             style="background-color: {{ $status->color }};"
                                             data-bs-toggle="tooltip" 
                                             title="カラー: {{ $status->color }}"
                                             onclick="openColorPicker({{ $status->id }}, '{{ $status->color }}')">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pt-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            作成: {{ $status->created_at->format('Y/m/d') }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-edit me-1"></i>
                                            更新: {{ $status->updated_at->format('m/d H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-plus-circle fa-3x mb-3 opacity-25"></i>
                                    <h5 class="text-muted">このイベントにはステータスがありません</h5>
                                    <p class="mb-3">新しいステータスを追加して、プロジェクトの進捗を管理しましょう。</p>
                                    <a href="{{ route('progress-statuses.create') }}?event_id={{ $event->id }}" 
                                       class="btn btn-primary rounded-pill px-4">
                                        <i class="fas fa-plus me-2"></i>最初のステータスを作成
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<!-- カラーピッカーモーダル -->
<div class="modal fade" id="colorPickerModal" tabindex="-1" aria-labelledby="colorPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="colorPickerModalLabel">ステータスカラーを変更</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="statusColor" class="form-label">カラーを選択</label>
                    <input type="color" class="form-control form-control-color" id="statusColor" title="Choose your color">
                </div>
                <div class="row">
                    <div class="col">
                        <h6>推奨カラー</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="color-preset" data-color="#e74c3c" style="background-color: #e74c3c;"></div>
                            <div class="color-preset" data-color="#f39c12" style="background-color: #f39c12;"></div>
                            <div class="color-preset" data-color="#f1c40f" style="background-color: #f1c40f;"></div>
                            <div class="color-preset" data-color="#27ae60" style="background-color: #27ae60;"></div>
                            <div class="color-preset" data-color="#3498db" style="background-color: #3498db;"></div>
                            <div class="color-preset" data-color="#9b59b6" style="background-color: #9b59b6;"></div>
                            <div class="color-preset" data-color="#667eea" style="background-color: #667eea;"></div>
                            <div class="color-preset" data-color="#764ba2" style="background-color: #764ba2;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" onclick="updateStatusColor()">カラーを更新</button>
            </div>
        </div>
    </div>
</div>
@endsection
                    </h5>
                    <h3>{{ $allStatuses->sum(function($status) { return $status->tasks->count(); }) }}</h3>
                    <p class="card-text">使用中タスク数</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </h5>
                    <h3>{{ number_format($allStatuses->avg(function($status) { return $status->tasks->count(); }), 1) }}</h3>
                    <p class="card-text">平均使用数</p>
                </div>
            </div>
        </div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ドラッグアンドドロップソート機能の初期化
    const sortableGrids = document.querySelectorAll('.sortable');
    sortableGrids.forEach(grid => {
        new Sortable(grid, {
            animation: 200,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                const statusId = evt.item.dataset.statusId;
                const newIndex = evt.newIndex;
                updateStatusOrder(statusId, newIndex);
            }
        });
    });

    // イベントフィルター機能
    const eventFilters = document.querySelectorAll('input[name="eventFilter"]');
    eventFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            const eventId = this.value;
            const eventSections = document.querySelectorAll('.event-section');
            
            if (eventId === 'all') {
                eventSections.forEach(section => section.style.display = 'block');
            } else {
                eventSections.forEach(section => {
                    if (section.dataset.eventId === eventId) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                });
            }
        });
    });

    // 展開/縮小機能
    document.getElementById('expandAll')?.addEventListener('click', function() {
        document.querySelectorAll('.task-list-mini').forEach(list => {
            list.style.maxHeight = 'none';
        });
    });

    document.getElementById('collapseAll')?.addEventListener('click', function() {
        document.querySelectorAll('.task-list-mini').forEach(list => {
            list.style.maxHeight = '120px';
        });
    });

    // ツールチップ初期化
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // カラープリセット選択
    document.querySelectorAll('.color-preset').forEach(preset => {
        preset.addEventListener('click', function() {
            const color = this.dataset.color;
            document.getElementById('statusColor').value = color;
        });
    });
});

// ステータス順序更新
function updateStatusOrder(statusId, newOrder) {
    fetch(`/progress-statuses/${statusId}/update-order`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order: newOrder
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'ステータスの順序を更新しました');
        } else {
            showAlert('error', 'エラーが発生しました');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'エラーが発生しました');
        location.reload();
    });
}

// カラーピッカー開く
let currentStatusId = null;
function openColorPicker(statusId, currentColor) {
    currentStatusId = statusId;
    document.getElementById('statusColor').value = currentColor;
    new bootstrap.Modal(document.getElementById('colorPickerModal')).show();
}

// ステータスカラー更新
function updateStatusColor() {
    const newColor = document.getElementById('statusColor').value;
    
    fetch(`/progress-statuses/${currentStatusId}/update-color`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            color: newColor
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // カラーの更新をDOMに反映
            const statusCard = document.querySelector(`[data-status-id="${currentStatusId}"]`);
            statusCard.style.setProperty('--status-color', newColor);
            statusCard.querySelector('.status-badge-large').style.backgroundColor = newColor;
            statusCard.querySelector('.color-picker-preview').style.backgroundColor = newColor;
            
            showAlert('success', 'ステータスカラーを更新しました');
            bootstrap.Modal.getInstance(document.getElementById('colorPickerModal')).hide();
        } else {
            showAlert('error', 'エラーが発生しました');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'エラーが発生しました');
    });
}

// 削除確認
function confirmDelete(statusName, deleteUrl) {
    if (confirm(`「${statusName}」を削除してもよろしいですか？\n\n注意: このステータスを使用しているタスクは「未設定」状態になります。`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfField);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// アラート表示
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = alertHtml;
    document.body.appendChild(tempDiv.firstElementChild);
    
    // 5秒後に自動削除
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 5000);
}

// カラープリセットスタイル
const presetStyle = document.createElement('style');
presetStyle.textContent = `
.color-preset {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: all 0.2s ease;
}
.color-preset:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}
`;
document.head.appendChild(presetStyle);
</script>
@endpush
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

function createDefaultStatuses() {
    if (!confirm('デフォルトの進捗ステータス（未着手、進行中、完了）を全イベントに作成しますか？')) return;
    
    fetch('/progress-statuses/create-defaults', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${data.count}個のステータスを作成しました。`);
            location.reload();
        } else {
            alert('デフォルトステータスの作成に失敗しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('デフォルトステータスの作成中にエラーが発生しました。');
    });
}
</script>
@endsection
