<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)->get();
        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === $request->user()->id) {
            $notification->read = true;
            $notification->save();
            return response()->json($notification);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}