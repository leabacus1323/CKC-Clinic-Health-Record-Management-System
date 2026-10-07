<?php

namespace App\Http\Controllers;

use App\Models\EducationalLevel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EducationalLevelController extends Controller
{
    /**
     * Store a newly created educational level.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'level_name' => 'required|string|max:255',
            'year_level' => 'nullable|string|max:255',
        ]);

        // Check if level with same name already exists
        $existing = EducationalLevel::where('level_name', $request->level_name)
            ->where('year_level', $request->year_level ?? '')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'id' => $existing->id,
                'message' => 'Educational level already exists'
            ]);
        }

        $level = EducationalLevel::create([
            'level_name' => $request->level_name,
            'year_level' => $request->year_level ?? '',
        ]);

        return response()->json([
            'success' => true,
            'id' => $level->id,
            'message' => 'Educational level created successfully'
        ]);
    }

    /**
     * Display a listing of educational levels.
     */
    public function index()
    {
        return EducationalLevel::all();
    }
}
