@extends('course.layouts')
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">

                @session('status')
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                @endsession

                <div class="card">
                    <div class="card-header">
                        <h4>course List
                            <a href="{{ url('course/create') }}" class="btn btn-primary float-end">Add course</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-stiped table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($course as $course)
                                <tr>
                                    <td>{{ $course->id }}</td>
                                    <td>{{ $course->course_name }}</td>
                                    <td>{{ $course->course_description }}</td>
                                   
                                    <td>
                                    <a href="{{ route('course.edit', $course->id) }}" class="btn btn-success">Edit</a>
                                    <a href="{{ route('course.show', $course->id) }}" class="btn btn-info">Show</a>
                                    
                                        <form action="{{ route('course.destroy', $course->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection