<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Seance;

class SeanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Seance::with('film')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'film_id' => 'required|exists:films,id',
            'room_id' => 'required|exists:rooms,id',
            'start_time' => 'required|date',
            'type' => 'required|in:normale,vip'
        ]);

        return Seance::create($validated);
    }

    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        return Seance::with('film')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $seance = Seance::findOrFail($id);
        $seance->update($request->all());

        return $seance;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Seance::destroy($id);
        return response()->json(['message' => 'Seance deleted']);
    }

    public function filter(Request $request)
    {
        $query = Seance::with('film');
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('film_id')) {
            $query->where('film_id', $request->film_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('start_time', $request->date);
        }
        return $query->get();
    }
}
