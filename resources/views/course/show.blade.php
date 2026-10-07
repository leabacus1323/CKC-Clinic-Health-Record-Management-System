@extends('course.layouts')

@section('content')
<div id="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden z-40"></div>
    <div class="relative overflow-x-auto">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Show course Detail
                            <a href="{{ url('course') }}" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <p>
                                {{ $course->course_name }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <p>
                                {!! $course->course_description !!}
                            </p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection