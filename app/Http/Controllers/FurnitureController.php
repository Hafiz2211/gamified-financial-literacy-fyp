<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FurnitureController extends Controller
{
    public function load(Request $request)
    {
        $user = $request->user();
        
        $owned = $user->owned_furniture;
        $positions = $user->furniture_positions;
        
        return response()->json([
            'owned' => $owned ? (is_array($owned) ? $owned : json_decode($owned, true)) : [],
            'positions' => $positions ? (is_array($positions) ? $positions : json_decode($positions, true)) : []
        ]);
    }
    
    public function save(Request $request)
    {
        $user = $request->user();
        
        $user->owned_furniture = json_encode($request->owned);
        $user->furniture_positions = json_encode($request->positions);
        $user->save();
        
        return response()->json(['success' => true]);
    }
}