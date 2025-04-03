<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApartmentController extends Controller
{
    public function store(Request $request)
    {
        // Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
        if (!Auth::check()) {
            return response()->json(['error' => 'Bejelentkezés szükséges'], 401);
        }

        // Validáció
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|integer|exists:apartment_types,id', // 🔹 Ellenőrizzük az id-t
            'max_capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
        ]);

        // 🔹 Kikeressük a type_name-et az adatbázisból
        $type = DB::table('apartment_types')->where('id', $validated['type_id'])->first();
        if (!$type) {
            return response()->json(['error' => 'Érvénytelen type_id'], 422);
        }

        // 🔹 Létrehozzuk az apartmant a megfelelő type_name-el
        $apartment = Apartment::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'type_name' => $type->type_name, // 🔹 Most már van type_name
            'max_capacity' => $validated['max_capacity'],
            'description' => $validated['description'] ?? null,
            'price_per_night' => $validated['price_per_night'],
        ]);

        return response()->json(['message' => 'Szállás sikeresen létrehozva!', 'apartment' => $apartment], 201);
    }
}
