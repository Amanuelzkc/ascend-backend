<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->get()->map(function($n) {
            $data = $n->toArray();
            $data['createdAt'] = $n->created_at->toIso8601String();
            return $data;
        });

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'link' => 'nullable|string',
            'read' => 'boolean',
        ]);

        $notification = Notification::create($validated);
        return response()->json($notification, 201);
    }

    public function show(Notification $notification)
    {
        return $notification;
    }

    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'read' => 'boolean',
        ]);

        $notification->update($validated);
        return response()->json($notification);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return response()->json(null, 204);
    }

    public function bulkUpdate(Request $request)
    {
        if ($request->has('readAll')) {
            Notification::query()->update(['read' => true]);
        } elseif ($request->has('id')) {
            Notification::where('id', $request->id)->update(['read' => true]);
        }
        return response()->json(['success' => true]);
    }

    public function bulkDelete(Request $request)
    {
        if ($request->has('deleteAll')) {
            Notification::truncate();
        } elseif ($request->has('id')) {
            Notification::where('id', $request->id)->delete();
        }
        return response()->json(['success' => true]);
    }
}
