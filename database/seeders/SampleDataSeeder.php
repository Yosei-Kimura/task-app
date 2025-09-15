<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Team;
use App\Models\Member;
use App\Models\Task;
use App\Models\ProgressStatus;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // イベントを作成
        $event1 = Event::create([
            'name' => '春のイベント2025',
            'description' => '2025年春に開催する大型イベントです。',
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-31',
            'is_active' => true,
        ]);

        $event2 = Event::create([
            'name' => 'システム開発プロジェクト',
            'description' => '新システムの開発プロジェクトです。',
            'start_date' => '2025-01-01',
            'end_date' => '2025-06-30',
            'is_active' => true,
        ]);

        // 進捗状況を作成
        $statuses1 = [
            ['name' => '未着手', 'color' => '#6c757d', 'order' => 1, 'is_completed' => false],
            ['name' => '作業中', 'color' => '#007bff', 'order' => 2, 'is_completed' => false],
            ['name' => 'レビュー中', 'color' => '#ffc107', 'order' => 3, 'is_completed' => false],
            ['name' => '完了', 'color' => '#28a745', 'order' => 4, 'is_completed' => true],
        ];

        $statuses2 = [
            ['name' => 'TODO', 'color' => '#6c757d', 'order' => 1, 'is_completed' => false],
            ['name' => 'In Progress', 'color' => '#17a2b8', 'order' => 2, 'is_completed' => false],
            ['name' => 'Testing', 'color' => '#ffc107', 'order' => 3, 'is_completed' => false],
            ['name' => 'Done', 'color' => '#28a745', 'order' => 4, 'is_completed' => true],
        ];

        foreach ($statuses1 as $status) {
            $event1->progressStatuses()->create($status);
        }

        foreach ($statuses2 as $status) {
            $event2->progressStatuses()->create($status);
        }

        // チームを作成
        $team1 = Team::create([
            'event_id' => $event1->id,
            'name' => '企画チーム',
            'description' => 'イベントの企画を担当するチームです。',
            'color' => '#007bff',
            'is_active' => true,
        ]);

        $team2 = Team::create([
            'event_id' => $event1->id,
            'name' => '運営チーム',
            'description' => 'イベントの運営を担当するチームです。',
            'color' => '#28a745',
            'is_active' => true,
        ]);

        $team3 = Team::create([
            'event_id' => $event2->id,
            'name' => '開発チーム',
            'description' => 'システム開発を担当するチームです。',
            'color' => '#dc3545',
            'is_active' => true,
        ]);

        // メンバーを作成
        $members = [
            ['team_id' => $team1->id, 'name' => '田中太郎', 'email' => 'tanaka@example.com', 'slack_user_id' => 'U12345', 'slack_username' => 'tanaka', 'role' => 'leader'],
            ['team_id' => $team1->id, 'name' => '佐藤花子', 'email' => 'sato@example.com', 'slack_user_id' => 'U12346', 'slack_username' => 'sato', 'role' => 'member'],
            ['team_id' => $team2->id, 'name' => '鈴木一郎', 'email' => 'suzuki@example.com', 'slack_user_id' => 'U12347', 'slack_username' => 'suzuki', 'role' => 'leader'],
            ['team_id' => $team2->id, 'name' => '高橋美咲', 'email' => 'takahashi@example.com', 'slack_user_id' => 'U12348', 'slack_username' => 'takahashi', 'role' => 'member'],
            ['team_id' => $team3->id, 'name' => '山田健太', 'email' => 'yamada@example.com', 'slack_user_id' => 'U12349', 'slack_username' => 'yamada', 'role' => 'leader'],
            ['team_id' => $team3->id, 'name' => '中村愛', 'email' => 'nakamura@example.com', 'slack_user_id' => 'U12350', 'slack_username' => 'nakamura', 'role' => 'member'],
        ];

        foreach ($members as $memberData) {
            Member::create($memberData);
        }

        // タスクを作成
        $progressStatus1 = $event1->progressStatuses()->first();
        $progressStatus2 = $event1->progressStatuses()->skip(1)->first();
        $progressStatusDev = $event2->progressStatuses()->first();

        $tasks = [
            [
                'event_id' => $event1->id,
                'team_id' => $team1->id,
                'assigned_member_id' => $team1->members()->first()->id,
                'progress_status_id' => $progressStatus1->id,
                'title' => 'イベント会場の選定',
                'description' => '適切な会場を選定し、予約を取る',
                'due_date' => '2025-02-15 17:00:00',
                'priority' => 4,
            ],
            [
                'event_id' => $event1->id,
                'team_id' => $team1->id,
                'assigned_member_id' => $team1->members()->skip(1)->first()->id,
                'progress_status_id' => $progressStatus2->id,
                'title' => '広告デザインの作成',
                'description' => 'イベント用の広告素材を作成する',
                'due_date' => '2025-02-28 18:00:00',
                'priority' => 3,
            ],
            [
                'event_id' => $event1->id,
                'team_id' => $team2->id,
                'assigned_member_id' => $team2->members()->first()->id,
                'progress_status_id' => $progressStatus1->id,
                'title' => 'スタッフの募集と研修',
                'description' => 'イベント当日のスタッフを募集し、研修を行う',
                'due_date' => '2025-03-10 16:00:00',
                'priority' => 3,
            ],
            [
                'event_id' => $event2->id,
                'team_id' => $team3->id,
                'assigned_member_id' => $team3->members()->first()->id,
                'progress_status_id' => $progressStatusDev->id,
                'title' => 'データベース設計',
                'description' => 'システムのデータベース構造を設計する',
                'due_date' => '2025-01-31 17:00:00',
                'priority' => 5,
            ],
            [
                'event_id' => $event2->id,
                'team_id' => $team3->id,
                'assigned_member_id' => $team3->members()->skip(1)->first()->id,
                'progress_status_id' => $progressStatusDev->id,
                'title' => 'UI/UXデザインの作成',
                'description' => 'ユーザーインターフェースのデザインを作成する',
                'due_date' => '2025-02-14 18:00:00',
                'priority' => 4,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        $this->command->info('サンプルデータが作成されました！');
    }
}
