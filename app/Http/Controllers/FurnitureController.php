<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FurnitureController extends Controller
{
    public function load(Request $request)
    {
        $user = $request->user();
        
        // Decode JSON properly
        $owned = $user->owned_furniture;
        $positions = $user->furniture_positions;
        
        // If positions is null or empty, return empty object
        if (is_null($positions) || $positions === '[]' || $positions === '') {
            $positions = [];
        } elseif (is_string($positions)) {
            $positions = json_decode($positions, true);
        }
        
        // If owned is null, return empty array
        if (is_null($owned)) {
            $owned = [];
        } elseif (is_string($owned)) {
            $owned = json_decode($owned, true);
        }
        
        return response()->json([
            'owned' => $owned ?: [],
            'positions' => $positions ?: []
        ]);
    }
    
    public function save(Request $request)
    {
        $user = $request->user();
        
        // Get the data from request
        $owned = $request->input('owned', []);
        $positions = $request->input('positions', []);
        
        // Store as JSON strings
        $user->owned_furniture = json_encode($owned);
        $user->furniture_positions = json_encode($positions);
        $user->save();
        
        return response()->json(['success' => true]);
    }
}