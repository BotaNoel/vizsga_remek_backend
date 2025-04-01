<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'type_name' => 'required|string|exists:apartment_types,type_name',
            'max_capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
        ]);

        // Szállás létrehozása
        $apartment = Apartment::create([
            'user_id' => Auth::id(), // Bejelentkezett felhasználó ID-ja
            'name' => $validated['name'],
            'type_name' => $validated['type_name'],
            'max_capacity' => $validated['max_capacity'],
            'description' => $validated['description'] ?? null,
            'price_per_night' => $validated['price_per_night'],
        ]);

        return response()->json(['message' => 'Szállás sikeresen létrehozva!', 'apartment' => $apartment], 201);
    }
}
