<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function store(Request $request)
    {
        $timeEntry = TimeEntry::create([
            'user_id' => $request->user()->id,
            'start_time' => now(),
        ]);

        return response()->json($timeEntry, 201);
    }

    public function end(Request $request, $id)
    {
        $timeEntry = TimeEntry::find($id);
        if ($timeEntry && $timeEntry->user_id === $request->user()->id) {
            $timeEntry->end_time = now();
            $timeEntry->save();
            return response()->json($timeEntry);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function index(Request $request)
    {
        $timeEntries = TimeEntry::where('user_id', $request->user()->id)->get();
        return response()->json($timeEntries);
    }
}