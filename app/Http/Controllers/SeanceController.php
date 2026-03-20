<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seance;
use OpenApi\Attributes as OA;

class SeanceController extends Controller
{
    #[OA\Get(path: '/api/seances', summary: 'Get all seances with their film', tags: ['Seances'])]
    #[OA\Response(response: 200, description: 'List of seances')]
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Seance::with('film')->get();
    }

    #[OA\Post(path: '/api/admin/seances', summary: 'Create a new seance', tags: ['Seances'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'film_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'room_id', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'start_time', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date-time'))]
    #[OA\Parameter(name: 'type', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: ['normale','vip']))]
    #[OA\Response(response: 201, description: 'Seance created successfully')]
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

    #[OA\Get(path: '/api/seances/{id}', summary: 'Get a seance by ID with its film', tags: ['Seances'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Seance retrieved successfully')]
    #[OA\Response(response: 404, description: 'Seance not found')]
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Seance::with('film')->findOrFail($id);
    }

    #[OA\Put(path: '/api/admin/seances/{id}', summary: 'Update a seance', tags: ['Seances'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'film_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'room_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'start_time', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date-time'))]
    #[OA\Parameter(name: 'type', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['normale','vip']))]
    #[OA\Response(response: 200, description: 'Seance updated successfully')]
    #[OA\Response(response: 404, description: 'Seance not found')]
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $seance = Seance::findOrFail($id);
        $seance->update($request->all());

        return $seance;
    }

    #[OA\Delete(path: '/api/admin/seances/{id}', summary: 'Delete a seance', tags: ['Seances'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Seance deleted successfully')]
    #[OA\Response(response: 404, description: 'Seance not found')]
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Seance::destroy($id);
        return response()->json(['message' => 'Seance deleted']);
    }

    #[OA\Get(path: '/api/seances/filter', summary: 'Filter seances by type, film_id or date', tags: ['Seances'])]
    #[OA\Parameter(name: 'type', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['normale','vip']))]
    #[OA\Parameter(name: 'film_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'date', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Response(response: 200, description: 'Filtered seances returned')]
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
