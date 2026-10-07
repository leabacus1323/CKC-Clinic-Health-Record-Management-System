@extends('admin.layout.navigation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Edit Dental Examination</h1>
                    <p class="text-gray-600">Update dental examination details</p>
                </div>
                <a href="{{ route('admin.dental.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('admin.dental.update', $exam->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Patient Info (Read-only) -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Patient Information</h3>
                    <p><strong>Patient:</strong> {{ $exam->patient && $exam->patient->user ? $exam->patient->user->name : 'N/A' }}</p>
                    <p><strong>Doctor:</strong> {{ $exam->doctor && $exam->doctor->user ? $exam->doctor->user->name : 'N/A' }}</p>
                </div>

                <!-- Diagnosis -->
                <div class="mb-6">
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosis
                    </label>
                    <textarea id="diagnosis" 
                              name="diagnosis" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              placeholder="Enter diagnosis...">{{ old('diagnosis', $exam->diagnosis) }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teeth Chart -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teeth Status Chart
                    </label>
                    
                    <!-- Legend -->
                    <div class="flex flex-wrap gap-4 mb-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span>Healthy</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span>Cavity</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                            <span>Filled</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gray-500 rounded mr-2"></div>
                            <span>Missing</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-orange-500 rounded mr-2"></div>
                            <span>Other</span>
                        </div>
                    </div>

                    @php
                        $teethStatus = is_array($exam->teeth_status) ? $exam->teeth_status : (is_string($exam->teeth_status) ? json_decode($exam->teeth_status, true) : []);
                        if (!is_array($teethStatus)) {
                            $teethStatus = array_fill(1, 32, 'healthy');
                        }
                    @endphp

                    <!-- Upper Teeth (1-16) -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium mb-2">Upper Teeth</h4>
                        <div class="grid grid-cols-8 gap-2">
                            @for($i = 1; $i <= 16; $i++)
                                @php
                                    $status = $teethStatus[$i] ?? 'healthy';
                                    $colorClass = [
                                        'healthy' => 'bg-green-500',
                                        'cavity' => 'bg-red-500',
                                        'filled' => 'bg-yellow-500',
                                        'missing' => 'bg-gray-500',
                                        'other' => 'bg-orange-500'
                                    ][$status] ?? 'bg-green-500';
                                @endphp
                                <div class="relative">
                                    <input type="hidden" name="teeth_status[{{ $i }}]" id="tooth-{{ $i }}" value="{{ $status }}">
                                    <div class="tooth-selector {{ $colorClass }} text-white p-2 rounded cursor-pointer text-center hover:opacity-80"
                                         data-tooth="{{ $i }}"
                                         onclick="toggleToothStatus({{ $i }})">
                                        {{ $i }}
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Lower Teeth (17-32) -->
                    <div>
                        <h4 class="text-sm font-medium mb-2">Lower Teeth</h4>
                        <div class="grid grid-cols-8 gap-2">
                            @for($i = 17; $i <= 32; $i++)
                                @php
                                    $status = $teethStatus[$i] ?? 'healthy';
                                    $colorClass = [
                                        'healthy' => 'bg-green-500',
                                        'cavity' => 'bg-red-500',
                                        'filled' => 'bg-yellow-500',
                                        'missing' => 'bg-gray-500',
                                        'other' => 'bg-orange-500'
                                    ][$status] ?? 'bg-green-500';
                                @endphp
                                <div class="relative">
                                    <input type="hidden" name="teeth_status[{{ $i }}]" id="tooth-{{ $i }}" value="{{ $status }}">
                                    <div class="tooth-selector {{ $colorClass }} text-white p-2 rounded cursor-pointer text-center hover:opacity-80"
                                         data-tooth="{{ $i }}"
                                         onclick="toggleToothStatus({{ $i }})">
                                        {{ $i }}
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.dental.index') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition">
                        Update Dental Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const statuses = ['healthy', 'cavity', 'filled', 'missing', 'other'];
const statusColors = {
    'healthy': 'bg-green-500',
    'cavity': 'bg-red-500',
    'filled': 'bg-yellow-500',
    'missing': 'bg-gray-500',
    'other': 'bg-orange-500'
};

function toggleToothStatus(toothNumber) {
    const input = document.getElementById(`tooth-${toothNumber}`);
    const selector = document.querySelector(`[data-tooth="${toothNumber}"]`);
    
    const currentStatus = input.value;
    const currentIndex = statuses.indexOf(currentStatus);
    const nextIndex = (currentIndex + 1) % statuses.length;
    const nextStatus = statuses[nextIndex];
    
    input.value = nextStatus;
    
    // Remove all color classes
    Object.values(statusColors).forEach(colorClass => {
        selector.classList.remove(colorClass);
    });
    
    // Add new color class
    selector.classList.add(statusColors[nextStatus]);
}
</script>
@endsection
