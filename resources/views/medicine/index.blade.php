@extends('medicine.layouts')
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
                        <h4>medicine List
                            <a href="{{ url('medicine/create') }}" class="btn btn-primary float-end">Add medicine</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-stiped table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Expiration Date</th>
                                    <th>Medicine Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($medicine as $medicine)
                                <tr>
                                    <td>{{ $medicine->id }}</td>
                                    <td>{{ $medicine->name }}</td>
                                    <td>{{ $medicine->description }}</td>
                                    <td>{{ $medicine->quantity}}</td>
                                    <td>{{ $medicine->expiration_date}}</td>
                                    <td>{{ $medicine->medicine_type}}</td>
                                    <td>
                                    <a href="{{ route('medicine.edit', $medicine->id) }}" class="btn btn-success">Edit</a>
                                    <a href="{{ route('medicine.show', $medicine->id) }}" class="btn btn-info">Show</a>
                                    
                                        <form action="{{ route('medicine.destroy', $medicine->id) }}" method="POST" class="d-inline">
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