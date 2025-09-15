<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SlackNotificationService;

class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Slack reminders for tasks approaching their due date';

    /**
     * Execute the console command.
     */
    public function handle(SlackNotificationService $slackService): int
    {
        $this->info('タスクリマインダーの送信を開始します...');

        try {
            $sentCount = $slackService->sendDueReminders();
            
            if ($sentCount > 0) {
                $this->info("{$sentCount}件のリマインダーを送信しました。");
            } else {
                $this->info('送信するリマインダーはありませんでした。');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('リマインダーの送信中にエラーが発生しました: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
