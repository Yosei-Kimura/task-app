<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'changed_by_member_id',
        'action',
        'old_values',
        'new_values',
        'comment',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * 履歴が属するタスク
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * 変更を行ったメンバー
     */
    public function changedByMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'changed_by_member_id');
    }

    /**
     * 最新の履歴から取得
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * 特定のアクションの履歴のみ取得
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * アクションをテキストで取得
     */
    public function getActionTextAttribute(): string
    {
        return match($this->action) {
            'created' => '作成',
            'updated' => '更新',
            'deleted' => '削除',
            'status_changed' => 'ステータス変更',
            'assigned' => '担当者変更',
            'due_date_changed' => '期限変更',
            'priority_changed' => '優先度変更',
            default => $this->action
        };
    }

    /**
     * 変更内容を取得
     */
    public function getChangesSummaryAttribute(): string
    {
        if (empty($this->old_values) || empty($this->new_values)) {
            return $this->action_text;
        }

        $changes = [];
        foreach ($this->new_values as $field => $newValue) {
            $oldValue = $this->old_values[$field] ?? null;
            if ($oldValue !== $newValue) {
                $changes[] = "{$field}: {$oldValue} → {$newValue}";
            }
        }

        return implode(', ', $changes);
    }
}
