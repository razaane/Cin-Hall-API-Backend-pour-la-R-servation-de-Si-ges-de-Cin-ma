<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Film::with('genre', 'seances')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'genre_id' => 'required|exists:genres,id',
            'title' => 'required|string',
            'description' => 'required',
            'duration' => 'required|integer',
            'min_age' => 'integer',
        ]);

        $film = Film::create($validated);

        return response()->json($film, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Film::with('genre', 'seances')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $film = Film::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes',
            'duration' => 'sometimes|integer',
            'min_age' => 'sometimes|integer',
            'genre_id' => 'sometimes|exists:genres,id',
        ]);

        $film->update($validated);
        return response()->json($film);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Film::destroy($id);
        return response()->json(['message' => 'Film deleted']);
    }
}
