<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'team_id',
        'assigned_member_id',
        'progress_status_id',
        'title',
        'description',
        'due_date',
        'priority',
        'is_reminder_sent',
        'reminder_sent_at',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'priority' => 'integer',
        'is_reminder_sent' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * タスクが属するイベント
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * タスクが属するチーム
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * タスクに割り当てられたメンバー
     */
    public function assignedMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'assigned_member_id');
    }

    /**
     * タスクの進捗状況
     */
    public function progressStatus(): BelongsTo
    {
        return $this->belongsTo(ProgressStatus::class);
    }

    /**
     * タスクの履歴
     */
    public function histories(): HasMany
    {
        return $this->hasMany(TaskHistory::class);
    }

    /**
     * アクティブなタスクのみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 期限切れのタスクを取得
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereHas('progressStatus', function($q) {
                        $q->where('is_completed', false);
                    });
    }

    /**
     * 今日期限のタスクを取得
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    /**
     * 明日期限のタスクを取得
     */
    public function scopeDueTomorrow($query)
    {
        return $query->whereDate('due_date', today()->addDay());
    }

    /**
     * 優先度順でソート
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * 期限順でソート
     */
    public function scopeByDueDate($query)
    {
        return $query->orderBy('due_date');
    }

    /**
     * 期限切れかどうか判定
     */
    public function isOverdue(): bool
    {
        if (!$this->due_date || !$this->progressStatus) {
            return false;
        }

        return $this->due_date->isPast() && !$this->progressStatus->is_completed;
    }

    /**
     * 完了しているかどうか判定
     */
    public function isCompleted(): bool
    {
        return $this->progressStatus && $this->progressStatus->is_completed;
    }

    /**
     * リマインダーが必要かどうか判定
     */
    public function needsReminder(): bool
    {
        if (!$this->due_date || $this->is_reminder_sent || $this->isCompleted()) {
            return false;
        }

        // 期限の24時間前にリマインド送信
        return $this->due_date->subDay()->isPast();
    }

    /**
     * 優先度をテキストで取得
     */
    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            5 => '最高',
            4 => '高',
            3 => '中',
            2 => '低',
            1 => '最低',
            default => '中'
        };
    }
}
