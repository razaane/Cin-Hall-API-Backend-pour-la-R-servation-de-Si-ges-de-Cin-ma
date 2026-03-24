<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use OpenApi\Attributes as OA;

class GenreController extends Controller
{
    #[OA\Get(path: '/api/genres',summary: 'Get all genres with their films',tags: ['Genres'])]
    #[OA\Response(response: 200, description: 'List of genres')]
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Genre::with('films')->get();
    }

    #[OA\Post(path: '/api/admin/genres', summary: 'Create a new genre',tags: ['Genres'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'name', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 201, description: 'Genre created successfully')]
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:genres'
        ]);

        return Genre::create($request->all());
    }

    #[OA\Get(path: '/api/genres/{id}', summary: 'Get a single genre by ID with its films', tags: ['Genres'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Genre retrieved successfully')]
    #[OA\Response(response: 404, description: 'Genre not found')]
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Genre::with('films')->findOrFail($id);
    }

    #[OA\Put(path: '/api/admin/genres/{id}', summary: 'Update a genre',tags: ['Genres'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'name', in: 'query', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Genre updated successfully')]
    #[OA\Response(response: 404, description: 'Genre not found')]
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $genre = Genre::findOrFail($id);
        $request->validate(['name' => 'required|unique:genres,name,' . $id]);
        $genre->update($request->all());
        return $genre;
    }

    #[OA\Delete(path: '/api/admin/genres/{id}', summary: 'Delete a genre',tags: ['Genres'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Genre deleted successfully')]
    #[OA\Response(response: 404, description: 'Genre not found')]
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Genre::destroy($id);
        return response()->json(['message' => 'Genre deleted']);
    }
}
