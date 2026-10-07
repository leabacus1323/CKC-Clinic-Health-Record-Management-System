<?php

namespace App\Http\Controllers;
use App\Models\Course;
use Illuminate\Http\Request;


class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $course = Course::all();
        return view('course.index', compact('course'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('course.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string',
            'course_description' => 'required|string',
        ]);

        Course::create([
            'course_name' => $request->course_name,
            'course_description' => $request->course_description,
           
        ]);

        return redirect('/course')->with('status','Course Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::findOrFail($id); 
        return view('course.show', compact('course')); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $course = Course::findOrFail($id);  
        return view('course.edit', compact('course')); 
    }

    /**
     * Update the specified resource in storage.
     */

   
    
    public function update(Request $request, string $id)
    {
        $request->validate([
            'course_name' => 'required|string',
            'course_description' => 'required|string',
          
        ]);
        $course = Course::findOrFail($id); // Fetch the course instance
       
        $course->update([
            'course_name' => $request->course_name,
            'course_description' => $request->course_description,            
        ]);
     
        return redirect('/course')->with('status','Course Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);  
        $course->delete();
        return redirect('/course')->with('status','Course Deleted Successfully');
    }

}