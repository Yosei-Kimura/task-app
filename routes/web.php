<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProgressStatusController;

// ダッシュボード
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// イベント管理
Route::resource('events', EventController::class);

// チーム管理
Route::resource('teams', TeamController::class);
Route::post('teams/{team}/notify', [TeamController::class, 'notify'])->name('teams.notify');

// メンバー管理
Route::resource('members', MemberController::class);

// ステータス管理
Route::resource('progress-statuses', ProgressStatusController::class);

// タスク管理
Route::resource('tasks', TaskController::class);

// タスクの特別なアクション
Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
Route::patch('tasks/{task}/assign', [TaskController::class, 'assignMember'])->name('tasks.assign-member');

// Slack通知テスト用（開発環境のみ）
if (app()->environment('local')) {
    Route::get('test/slack-reminder/{task}', function (App\Models\Task $task, App\Services\SlackNotificationService $slackService) {
        $result = $slackService->sendTaskReminder($task);
        return response()->json(['success' => $result, 'message' => $result ? 'リマインダーが送信されました' : 'リマインダーの送信に失敗しました']);
    })->name('test.slack-reminder');
    
    // 一時的なマイグレーション実行ルート
    Route::get('migrate-tables', function () {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            return "<h1>マイグレーション完了</h1><pre>" . $output . "</pre><p><a href='/'>ダッシュボードに戻る</a></p>";
        } catch (Exception $e) {
            return "<h1>マイグレーションエラー</h1><pre>" . $e->getMessage() . "</pre>";
        }
    })->name('migrate.tables');
}
