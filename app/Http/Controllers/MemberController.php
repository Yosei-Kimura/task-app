<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Team;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MemberController extends Controller
{
    /**
     * メンバー一覧を表示
     */
    public function index(Request $request): View
    {
        $query = Member::with(['teams.event', 'assignedTasks.progressStatus'])->active();
        
        // イベントによるフィルタリング
        if ($request->filled('event_id')) {
            $query->whereHas('teams', function ($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }
        
        // チームによるフィルタリング
        if ($request->filled('team_id')) {
            $query->whereHas('teams', function ($q) use ($request) {
                $q->where('teams.id', $request->team_id);
            });
        }
        
        // 検索機能
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slack_username', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // フィルター用のデータ
        $events = Event::active()->get();
        $teams = Team::active()->with('event')->get();
        
        // 統計用のデータ（フィルタリングなしの全メンバー）
        $allMembers = Member::active()->with(['teams.event', 'assignedTasks.progressStatus'])->get();

        return view('members.index', compact('members', 'events', 'teams', 'allMembers'));
    }

    /**
     * メンバー詳細を表示
     */
    public function show(Member $member): View
    {
        $member->load(['teams.event', 'assignedTasks.progressStatus', 'taskHistories.task']);

        return view('members.show', compact('member'));
    }

    /**
     * メンバー作成フォームを表示
     */
    public function create(): View
    {
        $teams = Team::active()->with('event')->get();

        return view('members.create', compact('teams'));
    }

    /**
     * メンバーを作成
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'team_ids' => 'required|array',
            'team_ids.*' => 'exists:teams,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'slack_user_id' => 'nullable|string|max:255',
            'slack_username' => 'nullable|string|max:255',
            'roles' => 'array',
            'roles.*' => 'in:leader,member',
        ]);

        $member = Member::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'slack_user_id' => $validated['slack_user_id'],
            'slack_username' => $validated['slack_username'],
        ]);

        // チームとの関係を作成
        foreach ($validated['team_ids'] as $index => $teamId) {
            $role = $validated['roles'][$index] ?? 'member';
            $member->teams()->attach($teamId, [
                'role' => $role,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('members.show', $member)
                        ->with('success', 'メンバーが作成されました。');
    }

    /**
     * メンバー編集フォームを表示
     */
    public function edit(Member $member): View
    {
        $member->load('teams', 'assignedTasks.progressStatus');
        $teams = Team::active()->with('event')->get();

        return view('members.edit', compact('member', 'teams'));
    }

    /**
     * メンバーを更新
     */
    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'team_ids' => 'required|array',
            'team_ids.*' => 'exists:teams,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'slack_user_id' => 'nullable|string|max:255',
            'slack_username' => 'nullable|string|max:255',
            'roles' => 'array',
            'roles.*' => 'in:leader,member',
            'is_active' => 'boolean',
        ]);

        $member->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'slack_user_id' => $validated['slack_user_id'],
            'slack_username' => $validated['slack_username'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // チームとの関係を更新
        $syncData = [];
        foreach ($validated['team_ids'] as $index => $teamId) {
            $role = $validated['roles'][$index] ?? 'member';
            $syncData[$teamId] = [
                'role' => $role,
                'is_active' => true,
                'updated_at' => now(),
            ];
        }
        $member->teams()->sync($syncData);

        return redirect()->route('members.show', $member)
                        ->with('success', 'メンバーが更新されました。');
    }

    /**
     * メンバーを削除
     */
    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')
                        ->with('success', 'メンバーが削除されました。');
    }
}
