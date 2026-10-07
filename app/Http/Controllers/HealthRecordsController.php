<?php

namespace App\Http\Controllers;

use App\Models\HealthRecords;
use Illuminate\Http\Request;

class HealthRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $healthRecords = HealthRecords::all();
        return view('health-records.index', compact('healthRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthRecords $healthRecords)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HealthRecords $healthRecords)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HealthRecords $healthRecords)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthRecords $healthRecords)
    {
        //
    }
}
