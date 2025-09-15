<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ProgressStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * イベント一覧を表示
     */
    public function index(): View
    {
        $events = Event::with(['teams', 'tasks'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return view('events.index', compact('events'));
    }

    /**
     * イベント詳細を表示
     */
    public function show(Event $event): View
    {
        $event->load(['teams.members', 'tasks.assignedMember', 'tasks.progressStatus', 'progressStatuses']);

        return view('events.show', compact('event'));
    }

    /**
     * イベント作成フォームを表示
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * イベントを作成
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $event = Event::create($validated);

        // デフォルトの進捗状況を作成
        $this->createDefaultProgressStatuses($event);

        return redirect()->route('events.show', $event)
                        ->with('success', 'イベントが作成されました。');
    }

    /**
     * イベント編集フォームを表示
     */
    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    /**
     * イベントを更新
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $event->update($validated);

        return redirect()->route('events.show', $event)
                        ->with('success', 'イベントが更新されました。');
    }

    /**
     * イベントを削除
     */
    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events.index')
                        ->with('success', 'イベントが削除されました。');
    }

    /**
     * デフォルトの進捗状況を作成
     */
    private function createDefaultProgressStatuses(Event $event): void
    {
        $defaultStatuses = [
            ['name' => '未着手', 'color' => '#6c757d', 'order' => 1, 'is_completed' => false],
            ['name' => '作業中', 'color' => '#007bff', 'order' => 2, 'is_completed' => false],
            ['name' => 'レビュー中', 'color' => '#ffc107', 'order' => 3, 'is_completed' => false],
            ['name' => '完了', 'color' => '#28a745', 'order' => 4, 'is_completed' => true],
        ];

        foreach ($defaultStatuses as $status) {
            $event->progressStatuses()->create($status);
        }
    }
}
