<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Film;
use OpenApi\Attributes as OA;

class FilmController extends Controller
{
    #[OA\Get(path: '/api/films', summary: 'Get all films with genre and seances', tags: ['Films'])]
    #[OA\Response(response: 200, description: 'List of films')]
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Film::with('genre', 'seances')->get();
    }

    #[OA\Post(path: '/api/admin/films', summary: 'Create a new film', tags: ['Films'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'genre_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'title', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'description', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'duration', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'min_age', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 201, description: 'Film created successfully')]
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

    #[OA\Get(path: '/api/films/{id}', summary: 'Get a film by ID with genre and seances', tags: ['Films'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Film retrieved successfully')]
    #[OA\Response(response: 404, description: 'Film not found')]
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Film::with('genre', 'seances')->findOrFail($id);
    }

    #[OA\Put(path: '/api/admin/films/{id}', summary: 'Update a film', tags: ['Films'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'genre_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'title', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'description', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'duration', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'min_age', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Film updated successfully')]
    #[OA\Response(response: 404, description: 'Film not found')]
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
            'image' => 'sometimes|string|nullable',
            'trailer_url' => 'sometimes|string|nullable'
        ]);

        $film->update($validated);
        return response()->json($film);
    }

    #[OA\Delete(path: '/api/admin/films/{id}', summary: 'Delete a film', tags: ['Films'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Film deleted successfully')]
    #[OA\Response(response: 404, description: 'Film not found')]
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Film::destroy($id);
        return response()->json(['message' => 'Film deleted']);
    }
}
