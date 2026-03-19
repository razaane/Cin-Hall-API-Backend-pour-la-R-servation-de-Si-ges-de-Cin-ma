<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;

class RoomController extends Controller
{

    public function index()
    {
        $rooms = Room::all();

        return response()->json($rooms);
    }


    public function store(StoreRoomRequest $request)
    {
        $room = Room::create([
            'name' => $request->name,
            'type' => strtolower($request->type),
            'total_seats' => $request->total_seats
        ]);

        return response()->json([
            'message' => 'Salle créée avec succès',
            'room' => $room
        ], 201);
    }


    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'error' => 'Salle introuvable'
            ], 404);
        }

        return response()->json($room);
    }

    
    public function update(UpdateRoomRequest $request, $id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'error' => 'Salle introuvable'
            ], 404);
        }

        $room->update([
            'name' => $request->name ?? $room->name,
            'type' => $request->type ? strtolower($request->type) : $room->type,
            'total_seats' => $request->total_seats ?? $room->total_seats
        ]);

        return response()->json([
            'message' => 'Salle mise à jour',
            'room' => $room
        ]);
    }

    // 🔹 DELETE /rooms/{id}
    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'error' => 'Salle introuvable'
            ], 404);
        }

        $room->delete();

        return response()->json([
            'message' => 'Salle supprimée'
        ]);
    }
}
