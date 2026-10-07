@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Edit Dental Examination</h1>
                    <p class="text-gray-600">Update dental examination details</p>
                </div>
                <a href="{{ route('doctor.health-records') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('doctor.dental-exams.update', $exam->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Patient Info -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <strong>Patient:</strong> {{ optional($exam->patient->user)->name ?? 'N/A' }}
                        <span class="ml-4"><strong>Patient ID:</strong> {{ $exam->patient_id }}</span>
                        <span class="ml-4"><strong>Exam ID:</strong> {{ $exam->id }}</span>
                    </p>
                </div>

                <!-- Diagnosis -->
                <div class="mb-6">
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                        Diagnosis
                    </label>
                    <textarea id="diagnosis"
                              name="diagnosis"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter diagnosis...">{{ old('diagnosis', $exam->diagnosis) }}</textarea>
                    @error('diagnosis')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teeth Status Chart -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Teeth Status Chart</h3>
                    <p class="text-sm text-gray-600 mb-4">Click on each tooth to set its condition</p>

                    @php
                        $teethStatus = is_string($exam->teeth_status) ? json_decode($exam->teeth_status, true) : $exam->teeth_status;
                        if (!$teethStatus) {
                            $teethStatus = [];
                            for ($i = 1; $i <= 32; $i++) {
                                $teethStatus[$i] = 'healthy';
                            }
                        }
                    @endphp

                    <!-- Upper Teeth -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Upper Teeth (1-16)</h4>
                        <div class="grid grid-cols-8 gap-2">
                            @for ($i = 1; $i <= 16; $i++)
                                <div class="text-center">
                                    <div class="tooth-selector p-3 border-2 rounded-lg cursor-pointer transition
                                        @if(($teethStatus[$i] ?? 'healthy') === 'healthy') border-green-300 bg-green-50
                                        @elseif(($teethStatus[$i] ?? 'healthy') === 'cavity') border-red-300 bg-red-50
                                        @elseif(($teethStatus[$i] ?? 'healthy') === 'missing') border-gray-300 bg-gray-50
                                        @elseif(($teethStatus[$i] ?? 'healthy') === 'filled') border-blue-300 bg-blue-50
                                        @else border-amber-800 bg-amber-50
                                        @endif"
                                        data-tooth="{{ $i }}"
                                        onclick="toggleToothStatus({{ $i }})">
                                        <div class="font-bold text-sm">{{ $i }}</div>
                                        <div class="text-xs tooth-status-{{ $i }}">{{ ucfirst($teethStatus[$i] ?? 'healthy') }}</div>
                                    </div>
                                    <input type="hidden" name="teeth_status[{{ $i }}]" id="tooth-{{ $i }}" value="{{ $teethStatus[$i] ?? 'healthy' }}">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Lower Teeth -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Lower Teeth (17-32)</h4>
                        <div class="grid grid-cols-8 gap-2">
                            @for ($i = 17; $i <= 32; $i++)
                                <div class="text-center">
                                    <div class="tooth-selector p-3 border-2 rounded-lg cursor-pointer transition
                                        @if(($teethStatus[$i] ?? 'healthy') === 'healthy') border-green-300 bg-green-50
                                        @elseif(($teethStatus[$i] ?? 'healthy') === 'cavity') border-red-300 bg-red-50
                                        @elseif(($teethStatus[$i] ?? 'healthy') === 'missing') border-gray-300 bg-gray-50
                                        @elseif(($teethStatus[$i] ?? 'healthy') === 'filled') border-blue-300 bg-blue-50
                                        @else border-amber-800 bg-amber-50
                                        @endif"
                                        data-tooth="{{ $i }}"
                                        onclick="toggleToothStatus({{ $i }})">
                                        <div class="font-bold text-sm">{{ $i }}</div>
                                        <div class="text-xs tooth-status-{{ $i }}">{{ ucfirst($teethStatus[$i] ?? 'healthy') }}</div>
                                    </div>
                                    <input type="hidden" name="teeth_status[{{ $i }}]" id="tooth-{{ $i }}" value="{{ $teethStatus[$i] ?? 'healthy' }}">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Legend:</h4>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 border border-green-300 rounded mr-2"></div>
                                <span class="text-sm">Healthy</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 border border-red-300 rounded mr-2"></div>
                                <span class="text-sm">Cavity</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-500 border border-blue-300 rounded mr-2"></div>
                                <span class="text-sm">Filled</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-gray-500 border border-gray-300 rounded mr-2"></div>
                                <span class="text-sm">Missing</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-yellow-500 border border-amber-800 rounded mr-2"></div>
                                <span class="text-sm">Other</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('doctor.health-records') }}"
                       class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Update Examination
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const statuses = ['healthy', 'cavity', 'filled', 'missing', 'other'];
const statusColors = {
    'healthy': { border: 'border-green-300', bg: 'bg-green-50' },
    'cavity': { border: 'border-red-300', bg: 'bg-red-50' },
    'filled': { border: 'border-blue-300', bg: 'bg-blue-50' },
    'missing': { border: 'border-gray-300', bg: 'bg-gray-50' },
    'other': { border: 'border-amber-800', bg: 'bg-amber-50' }
};

function toggleToothStatus(toothNum) {
    const input = document.getElementById(`tooth-${toothNum}`);
    const currentStatus = input.value;
    const currentIndex = statuses.indexOf(currentStatus);
    const nextIndex = (currentIndex + 1) % statuses.length;
    const newStatus = statuses[nextIndex];

    input.value = newStatus;

    // Update visual
    const selector = document.querySelector(`[data-tooth="${toothNum}"]`);
    const statusText = document.querySelector(`.tooth-status-${toothNum}`);

    // Remove all color classes
    Object.values(statusColors).forEach(colors => {
        selector.classList.remove(colors.border, colors.bg);
    });

    // Add new color classes
    selector.classList.add(statusColors[newStatus].border, statusColors[newStatus].bg);
    statusText.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
}
</script>
@endsection
