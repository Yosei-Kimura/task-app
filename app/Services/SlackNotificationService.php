<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Member;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationService
{
    private string $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = env('SLACK_WEBHOOK_URL', '');
    }

    /**
     * タスクの期限リマインドを送信
     */
    public function sendTaskReminder(Task $task): bool
    {
        if (empty($this->webhookUrl)) {
            Log::warning('Slack webhook URL is not configured');
            return false;
        }

        if (!$task->assignedMember || !$task->assignedMember->hasSlackAccount()) {
            Log::info('Task has no assigned member with Slack account', ['task_id' => $task->id]);
            return false;
        }

        $member = $task->assignedMember;
        $message = $this->buildReminderMessage($task, $member);

        try {
            $response = Http::post($this->webhookUrl, [
                'text' => "タスク期限リマインド",
                'channel' => "@{$member->slack_username}",
                'username' => 'タスク管理Bot',
                'icon_emoji' => ':bell:',
                'attachments' => [
                    [
                        'color' => $this->getTaskColor($task),
                        'title' => $task->title,
                        'title_link' => route('tasks.show', $task),
                        'text' => $message,
                        'fields' => [
                            [
                                'title' => 'イベント',
                                'value' => $task->event->name,
                                'short' => true
                            ],
                            [
                                'title' => 'チーム',
                                'value' => $task->team->name,
                                'short' => true
                            ],
                            [
                                'title' => '期限',
                                'value' => $task->due_date->format('Y年m月d日 H:i'),
                                'short' => true
                            ],
                            [
                                'title' => '優先度',
                                'value' => $task->priority_text,
                                'short' => true
                            ]
                        ],
                        'footer' => 'タスク管理システム',
                        'ts' => now()->timestamp
                    ]
                ]
            ]);

            if ($response->successful()) {
                // リマインド送信済みフラグを更新
                $task->update([
                    'is_reminder_sent' => true,
                    'reminder_sent_at' => now()
                ]);

                Log::info('Slack reminder sent successfully', ['task_id' => $task->id, 'member_id' => $member->id]);
                return true;
            } else {
                Log::error('Failed to send Slack reminder', [
                    'task_id' => $task->id,
                    'response_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending Slack reminder', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * タスクのステータス変更通知を送信
     */
    public function sendStatusChangeNotification(Task $task, string $oldStatus, string $newStatus, ?Member $changedBy = null): bool
    {
        if (empty($this->webhookUrl)) {
            return false;
        }

        // チームメンバー全員に通知（Slackアカウントがある場合のみ）
        $members = $task->team->members()->whereNotNull('slack_user_id')->get();
        
        if ($members->isEmpty()) {
            return false;
        }

        $message = $this->buildStatusChangeMessage($task, $oldStatus, $newStatus, $changedBy);

        try {
            // チャンネルまたはDMに送信
            $response = Http::post($this->webhookUrl, [
                'text' => "タスクステータス更新",
                'username' => 'タスク管理Bot',
                'icon_emoji' => ':information_source:',
                'attachments' => [
                    [
                        'color' => $this->getTaskColor($task),
                        'title' => $task->title,
                        'title_link' => route('tasks.show', $task),
                        'text' => $message,
                        'fields' => [
                            [
                                'title' => 'イベント',
                                'value' => $task->event->name,
                                'short' => true
                            ],
                            [
                                'title' => 'チーム',
                                'value' => $task->team->name,
                                'short' => true
                            ],
                            [
                                'title' => '変更前',
                                'value' => $oldStatus,
                                'short' => true
                            ],
                            [
                                'title' => '変更後',
                                'value' => $newStatus,
                                'short' => true
                            ]
                        ],
                        'footer' => 'タスク管理システム',
                        'ts' => now()->timestamp
                    ]
                ]
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception while sending Slack status change notification', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * リマインドメッセージを構築
     */
    private function buildReminderMessage(Task $task, Member $member): string
    {
        $timeUntilDue = $task->due_date->diffForHumans(now(), true);
        
        if ($task->isOverdue()) {
            $urgency = ":warning: *期限を過ぎています！*";
        } elseif ($task->due_date->isToday()) {
            $urgency = ":clock3: *今日が期限です！*";
        } elseif ($task->due_date->isTomorrow()) {
            $urgency = ":exclamation: *明日が期限です！*";
        } else {
            $urgency = ":bell: 期限が近づいています";
        }

        $message = "{$urgency}\n\n";
        $message .= "こんにちは {$member->name}さん\n";
        $message .= "担当タスクの期限についてお知らせします。\n\n";
        
        if ($task->description) {
            $message .= "**詳細:**\n{$task->description}\n\n";
        }
        
        $message .= "期限まで残り: *{$timeUntilDue}*\n";
        $message .= "ステータス: {$task->progressStatus->name ?? '未設定'}\n\n";
        $message .= "タスクの詳細を確認し、必要に応じてステータスを更新してください。";

        return $message;
    }

    /**
     * ステータス変更メッセージを構築
     */
    private function buildStatusChangeMessage(Task $task, string $oldStatus, string $newStatus, ?Member $changedBy): string
    {
        $changedByText = $changedBy ? "{$changedBy->name}さん" : "誰か";
        
        $message = "{$changedByText}がタスクのステータスを更新しました。\n\n";
        $message .= "*{$oldStatus}* → *{$newStatus}*\n\n";
        
        if ($task->description) {
            $message .= "**詳細:**\n{$task->description}";
        }

        return $message;
    }

    /**
     * タスクの色を取得
     */
    private function getTaskColor(Task $task): string
    {
        if ($task->isOverdue()) {
            return 'danger';
        }

        if ($task->progressStatus) {
            return $task->progressStatus->color;
        }

        return match($task->priority) {
            5 => '#dc3545', // 最高 - 赤
            4 => '#fd7e14', // 高 - オレンジ
            3 => '#ffc107', // 中 - 黄色
            2 => '#20c997', // 低 - ティール
            1 => '#6c757d', // 最低 - グレー
            default => '#6c757d'
        };
    }

    /**
     * チーム全体に通知を送信
     */
    public function sendTeamNotification(Member $member, string $message, $team): bool
    {
        if (empty($this->webhookUrl)) {
            Log::warning('Slack webhook URL is not configured');
            return false;
        }

        if (!$member->hasSlackAccount()) {
            Log::info('Member has no Slack account', ['member_id' => $member->id]);
            return false;
        }

        try {
            $response = Http::post($this->webhookUrl, [
                'text' => "チーム通知",
                'channel' => "@{$member->slack_username}",
                'username' => 'タスク管理Bot',
                'icon_emoji' => ':mega:',
                'attachments' => [
                    [
                        'color' => $team->color ?? '#007bff',
                        'title' => "チーム「{$team->name}」からのお知らせ",
                        'text' => "こんにちは {$member->name}さん\n\n{$message}",
                        'fields' => [
                            [
                                'title' => 'イベント',
                                'value' => $team->event->name,
                                'short' => true
                            ],
                            [
                                'title' => 'チーム',
                                'value' => $team->name,
                                'short' => true
                            ]
                        ],
                        'footer' => 'タスク管理システム',
                        'ts' => now()->timestamp
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('Slack team notification sent successfully', [
                    'member_id' => $member->id, 
                    'team_id' => $team->id
                ]);
                return true;
            } else {
                Log::error('Failed to send Slack team notification', [
                    'member_id' => $member->id,
                    'team_id' => $team->id,
                    'response_code' => $response->status(),
                    'response_body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending Slack team notification', [
                'member_id' => $member->id,
                'team_id' => $team->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * リマインドが必要なタスクを取得して通知を送信
     */
    public function sendDueReminders(): int
    {
        $tasks = Task::active()
            ->whereNotNull('due_date')
            ->where('is_reminder_sent', false)
            ->whereHas('assignedMember', function($query) {
                $query->whereNotNull('slack_user_id');
            })
            ->whereHas('progressStatus', function($query) {
                $query->where('is_completed', false);
            })
            ->get()
            ->filter(function($task) {
                return $task->needsReminder();
            });

        $sentCount = 0;
        foreach ($tasks as $task) {
            if ($this->sendTaskReminder($task)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }
}
