<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::all();
        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $activity = Activity::create($request->all());
        return response()->json($activity, 201);
    }

    public function show($id)
    {
        $activity = Activity::findOrFail($id);
        return response()->json($activity);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $activity = Activity::findOrFail($id);
        $activity->update($request->all());
        return response()->json($activity);
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        return response()->json(null, 204);
    }
}
