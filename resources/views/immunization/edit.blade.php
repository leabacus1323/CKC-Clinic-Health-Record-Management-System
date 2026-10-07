<x-app-layout>
    <form action="{{ route('immunization.update', $immunization->id) }}" method="POST">
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ $immunization->name }}" />
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" rows="3" class="form-control">{!! $immunization->description !!}</textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</x-app-layout>
