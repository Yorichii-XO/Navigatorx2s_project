<?php
namespace App\Http\Controllers;

use App\Models\UrlVisit;
use App\Models\Category;
use Illuminate\Http\Request;

class UrlVisitController extends Controller
{
    public function index()
    {
        $urlVisits = UrlVisit::with('category')->get();
        return response()->json($urlVisits);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'url' => 'required|url',
            'visit_time' => 'required|date',
            'duration' => 'required|integer',
        ]);

        $urlVisit = UrlVisit::create($request->all());
        return response()->json($urlVisit, 201);
    }

    public function show($id)
    {
        $urlVisit = UrlVisit::with('category')->findOrFail($id);
        return response()->json($urlVisit);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'url' => 'required|url',
            'visit_time' => 'required|date',
            'duration' => 'required|integer',
        ]);

        $urlVisit = UrlVisit::findOrFail($id);
        $urlVisit->update($request->all());
        return response()->json($urlVisit);
    }

    public function destroy($id)
    {
        $urlVisit = UrlVisit::findOrFail($id);
        $urlVisit->delete();
        return response()->json(null, 204);
    }
}
