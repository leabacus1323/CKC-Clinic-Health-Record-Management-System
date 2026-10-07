@extends('course.layouts')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Create course 
                            <a href="{{ url('course') }}" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('course.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="course_name" class="form-control" />
                                @error('course_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="course_description" rows="3" class="form-control"></textarea>
                                @error('course_description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection