<?php

namespace App\Http\Controllers;

use App\Models\ProgressStatus;
use App\Models\Event;
use Illuminate\Http\Request;

class ProgressStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $eventId = $request->get('event_id');
        
        $query = ProgressStatus::query()->where('is_active', true);
        
        if ($eventId) {
            $query->where('event_id', $eventId);
        }
        
        $progressStatuses = $query->with('event')
            ->orderBy('event_id')
            ->orderBy('order')
            ->paginate(20);
        
        $events = Event::where('is_active', true)->get();
        
        return view('progress-statuses.index', compact('progressStatuses', 'events', 'eventId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $eventId = $request->get('event_id');
        $events = Event::where('is_active', true)->get();
        
        return view('progress-statuses.create', compact('events', 'eventId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_completed' => 'boolean',
        ]);

        // orderが指定されていない場合、最大値+1を設定
        if (!isset($validated['order'])) {
            $maxOrder = ProgressStatus::where('event_id', $validated['event_id'])->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        $validated['is_completed'] = $request->has('is_completed');

        ProgressStatus::create($validated);

        return redirect()->route('progress-statuses.index', ['event_id' => $validated['event_id']])
            ->with('success', 'ステータスが正常に作成されました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgressStatus $progressStatus)
    {
        $progressStatus->load('event', 'tasks');
        
        return view('progress-statuses.show', compact('progressStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgressStatus $progressStatus)
    {
        $events = Event::where('is_active', true)->get();
        
        return view('progress-statuses.edit', compact('progressStatus', 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgressStatus $progressStatus)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'order' => 'nullable|integer',
            'is_completed' => 'boolean',
        ]);

        $validated['is_completed'] = $request->has('is_completed');

        $progressStatus->update($validated);

        return redirect()->route('progress-statuses.index', ['event_id' => $validated['event_id']])
            ->with('success', 'ステータスが正常に更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgressStatus $progressStatus)
    {
        $eventId = $progressStatus->event_id;
        
        // 関連するタスクのステータスをnullに設定
        $progressStatus->tasks()->update(['progress_status_id' => null]);
        
        $progressStatus->update(['is_active' => false]);

        return redirect()->route('progress-statuses.index', ['event_id' => $eventId])
            ->with('success', 'ステータスが正常に削除されました。');
    }
}
