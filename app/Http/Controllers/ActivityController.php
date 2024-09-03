<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request)
    {
        $activity = Activity::create([
            'user_id' => $request->user()->id,
            'activity_type' => $request->activity_type,
            'details' => $request->details,
        ]);

        return response()->json($activity, 201);
    }

    public function index(Request $request)
    {
        $activities = Activity::where('user_id', $request->user()->id)->get();
        return response()->json($activities);
    }
}