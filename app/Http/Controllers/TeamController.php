<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Event;
use App\Models\Member;
use App\Services\SlackNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * チーム一覧を表示
     */
    public function index(): View
    {
        $teams = Team::with(['event', 'members'])
                     ->active()
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('teams.index', compact('teams'));
    }

    /**
     * チーム詳細を表示
     */
    public function show(Team $team): View
    {
        $team->load(['event', 'members', 'tasks.assignedMember', 'tasks.progressStatus']);

        return view('teams.show', compact('team'));
    }

    /**
     * チーム作成フォームを表示
     */
    public function create(): View
    {
        $events = Event::active()->get();

        return view('teams.create', compact('events'));
    }

    /**
     * チームを作成
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        $team = Team::create($validated);

        return redirect()->route('teams.show', $team)
                        ->with('success', 'チームが作成されました。');
    }

    /**
     * チーム編集フォームを表示
     */
    public function edit(Team $team): View
    {
        $events = Event::active()->get();

        return view('teams.edit', compact('team', 'events'));
    }

    /**
     * チームを更新
     */
    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
                        ->with('success', 'チームが更新されました。');
    }

    /**
     * チームを削除
     */
    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()->route('teams.index')
                        ->with('success', 'チームが削除されました。');
    }

    /**
     * チーム全体にSlack通知を送信
     */
    public function notify(Request $request, Team $team, SlackNotificationService $slackService)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500'
        ]);

        // Slackアカウントが設定されているメンバーを取得
        $membersWithSlack = $team->members()
            ->whereNotNull('slack_user_id')
            ->where('is_active', true)
            ->get();

        if ($membersWithSlack->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Slackアカウントが設定されているメンバーがいません。'
            ]);
        }

        $successCount = 0;
        $totalCount = $membersWithSlack->count();

        foreach ($membersWithSlack as $member) {
            $success = $slackService->sendTeamNotification($member, $validated['message'], $team);
            if ($success) {
                $successCount++;
            }
        }

        if ($successCount === $totalCount) {
            return response()->json([
                'success' => true,
                'message' => "チーム全体に通知を送信しました。（{$successCount}名）"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "一部の通知に失敗しました。（成功: {$successCount}/{$totalCount}）"
            ]);
        }
    }
}
