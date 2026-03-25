<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GameController extends Controller
{
    public function town()
    {
        $user = auth()->user();
        
        return view('town', [
            'user' => $user
        ]);
    }
    
    public function sync(Request $request)
    {
        $user = auth()->user();
        
        // Update all game data
        if ($request->has('coins')) {
            $user->coins = $request->coins;
        }
        
        if ($request->has('townLevel')) {
            $user->town_level = $request->townLevel;
        }
        
        if ($request->has('population')) {
            $user->population = $request->population;
        }
        
        if ($request->has('wood')) {
            $user->wood = $request->wood;
        }
        
        if ($request->has('stone')) {
            $user->stone = $request->stone;
        }
        
        if ($request->has('food')) {
            $user->food = $request->food;
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Game data synced'
        ]);
    }
    
    public function getUserData()
    {
        $user = auth()->user();
        
        return response()->json([
            'coins' => $user->coins,
            'level' => $user->level,
            'townLevel' => $user->town_level ?? 1,
            'population' => $user->population ?? 0,
            'wood' => $user->wood ?? 120,
            'stone' => $user->stone ?? 80,
            'food' => $user->food ?? 50
        ]);
    }
}