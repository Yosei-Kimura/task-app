<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'slack_user_id',
        'slack_username',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * メンバーが属するチーム（多対多）
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'member_team')
                    ->withPivot('role', 'is_active')
                    ->withTimestamps()
                    ->wherePivot('is_active', true);
    }

    /**
     * メンバーが属するチーム（deprecated - 後方互換性のため）
     */
    public function team(): BelongsTo
    {
        // 最初に所属しているチームを返す
        $firstTeam = $this->teams()->first();
        return $firstTeam ? $this->belongsTo(Team::class)->where('id', $firstTeam->id) : $this->belongsTo(Team::class, 'id', 'id')->whereRaw('0 = 1');
    }

    /**
     * メンバーに割り当てられたタスク
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_member_id');
    }

    /**
     * メンバーが作成したタスク履歴
     */
    public function taskHistories(): HasMany
    {
        return $this->hasMany(TaskHistory::class, 'changed_by_member_id');
    }

    /**
     * アクティブなメンバーのみ取得
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * チームリーダーのみ取得
     */
    public function scopeLeaders($query)
    {
        return $query->whereHas('teams', function($q) {
            $q->wherePivot('role', 'leader');
        });
    }

    /**
     * Slackアカウントが設定されているメンバー
     */
    public function scopeWithSlack($query)
    {
        return $query->whereNotNull('slack_user_id');
    }

    /**
     * 特定のチームでリーダーかどうか判定
     */
    public function isLeaderOf(Team $team): bool
    {
        return $this->teams()->where('team_id', $team->id)->wherePivot('role', 'leader')->exists();
    }

    /**
     * 特定のチームでのロールを取得
     */
    public function getRoleInTeam(Team $team): ?string
    {
        $teamMember = $this->teams()->where('team_id', $team->id)->first();
        return $teamMember ? $teamMember->pivot->role : null;
    }

    /**
     * リーダーかどうか判定（deprecated - 後方互換性のため）
     */
    public function isLeader(): bool
    {
        // 最初のチームでリーダーかどうかを判定
        $firstTeam = $this->teams()->first();
        return $firstTeam ? $this->isLeaderOf($firstTeam) : false;
    }

    /**
     * Slackアカウントが設定されているか判定
     */
    public function hasSlackAccount(): bool
    {
        return !empty($this->slack_user_id);
    }
}
