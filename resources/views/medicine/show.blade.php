@extends('medicine.layouts')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Show medicine Detail
                            <a href="{{ url('medicine') }}" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <p>
                                {{ $medicine->name }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <p>
                                {!! $medicine->description !!}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label>Quantity</label>
                            <br/>
                            <p>
                                {{ $medicine->quantity  }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label>Expiration Date</label>
                            <br/>
                            <p>
                                {{ $medicine->expiration_date  }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label>Medicine Type</label>
                            <br/>
                            <p>
                                {{ $medicine->medicine_type  }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection