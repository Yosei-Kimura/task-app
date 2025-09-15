<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * チームが属するイベント
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * チームが持つメンバー（多対多）
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_team')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps()
                    ->wherePivot('is_active', true);
    }

    /**
     * チームが持つタスク
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * アクティブなチームのみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * チームリーダーを取得
     */
    public function getLeaderAttribute()
    {
        return $this->members()->wherePivot('role', 'leader')->first();
    }

    /**
     * アクティブなメンバー数を取得
     */
    public function getActiveMemberCountAttribute()
    {
        return $this->members()->wherePivot('is_active', true)->count();
    }
}
