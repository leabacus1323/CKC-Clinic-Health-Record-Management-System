@extends('medicine.layouts')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Create medicine
                            <a href="{{ url('medicine') }}" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('medicine.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" />
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" rows="3" class="form-control"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Quantity</label>
                                <br/>
                                <input type="text" name="quantity" class="form-control"  /> 
                                @error('quantity') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Expiration Date</label>
                                <br/>
                                <input type="date" name="expiration_date" class="form-control"  /> 
                                @error('expiration_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label>Medicine type</label>
                                <br/>
                                <input type="text" name="medicine_type" class="form-control"  /> 
                                @error('medicine_type') <span class="text-danger">{{ $message }}</span> @enderror
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