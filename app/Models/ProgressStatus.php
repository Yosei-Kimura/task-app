<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgressStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'color',
        'order',
        'is_completed',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_completed' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * 進捗状況が属するイベント
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * この進捗状況を持つタスク
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * アクティブな進捗状況のみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 表示順でソート
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * 完了状態の進捗状況のみ取得
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * 進行中状態の進捗状況のみ取得
     */
    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false);
    }
}
