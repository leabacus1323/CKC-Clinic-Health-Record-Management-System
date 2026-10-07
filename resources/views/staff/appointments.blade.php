@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Staff Mode Indicator -->
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="text-green-800 font-medium">Staff Duties Mode</span>
        </div>
    </div>

    <!-- Header -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">My Appointments Dashboard</h1>
                <p class="text-gray-600">Manage your patient appointments and schedule</p>
            </div>
            <a href="{{ route('staff.dashboard') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Pending Appointments Section -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <span class="bg-yellow-100 text-yellow-800 w-6 h-6 rounded-full flex items-center justify-center text-sm mr-3">{{ $pendingAppointments->count() }}</span>
                Pending Appointments
            </h2>
            <p class="text-gray-600 text-sm">Appointments awaiting your confirmation</p>
        </div>

        <div class="p-6">
            @if($pendingAppointments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($pendingAppointments as $appointment)
                        <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="font-medium text-gray-800">{{ $appointment->patient->user->name ?? 'N/A' }}</h3>
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Pending</span>
                            </div>

                            <div class="text-sm text-gray-600 space-y-1 mb-4">
                                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('M j, Y') }}</p>
                                <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}</p>
                                @if($appointment->reason)
                                    <p><strong>Reason:</strong> {{ $appointment->reason }}</p>
                                @endif
                            </div>

                            <div class="flex space-x-2">
                                <form action="{{ route('staff.appointments.update-status', $appointment->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Confirmed">
                                    <button type="button" onclick="showConfirmModal('confirm', {{ $appointment->id }}, '{{ $appointment->patient->user->name ?? 'this patient' }}')" class="w-full bg-green-600 text-white text-sm px-3 py-2 rounded hover:bg-green-700 transition">
                                        Confirm
                                    </button>
                                </form>
                                <form action="{{ route('staff.appointments.update-status', $appointment->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Cancelled">
                                    <button type="button" onclick="showConfirmModal('cancel', {{ $appointment->id }}, '{{ $appointment->patient->user->name ?? 'this patient' }}')" class="w-full bg-red-600 text-white text-sm px-3 py-2 rounded hover:bg-red-700 transition">
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-green-800 mb-2">No Pending Appointments</h3>
                        <p class="text-green-600">Great! You have no appointments waiting for confirmation.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Appointment Calendar
            </h2>
            <p class="text-gray-600 text-sm">Click on dates to view appointments</p>
        </div>

        <div class="p-6">
            <!-- Calendar Navigation -->
            <div class="flex items-center justify-between mb-6">
                <button onclick="changeMonth(-1)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous
                </button>
                <h3 id="calendar-month-year" class="text-xl font-semibold text-gray-800"></h3>
                <button onclick="changeMonth(1)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center">
                    Next
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                <!-- Day Headers -->
                <div class="p-3 text-center text-sm font-medium text-gray-500">Sun</div>
                <div class="p-3 text-center text-sm font-medium text-gray-500">Mon</div>
                <div class="p-3 text-center text-sm font-medium text-gray-500">Tue</div>
                <div class="p-3 text-center text-sm font-medium text-gray-500">Wed</div>
                <div class="p-3 text-center text-sm font-medium text-gray-500">Thu</div>
                <div class="p-3 text-center text-sm font-medium text-gray-500">Fri</div>
                <div class="p-3 text-center text-sm font-medium text-gray-500">Sat</div>
            </div>

            <!-- Calendar Days -->
            <div id="calendar-grid" class="grid grid-cols-7 gap-1">
                <!-- Calendar days will be generated by JavaScript -->
            </div>

            <!-- Selected Date Details -->
            <div id="selected-date-details" class="mt-6 hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 id="selected-date-title" class="text-lg font-medium text-blue-800 mb-3"></h4>
                    <div id="selected-date-appointments" class="space-y-2">
                        <!-- Appointments for selected date will be shown here -->
                    </div>
                </div>
            </div>

            <!-- No Appointments Message -->
            <div id="no-appointments-message" class="mt-6 hidden">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                    <p class="text-gray-600">No appointments scheduled for this date.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Calendar data from backend
        const calendarData = @json($calendarData);
        const currentDate = new Date(); // Use actual current date (August 2025)
        let displayMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

        function generateCalendar() {
            const year = displayMonth.getFullYear();
            const month = displayMonth.getMonth();

            // Update month/year display
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            document.getElementById('calendar-month-year').textContent = `${monthNames[month]} ${year}`;

            // Get first day of month and number of days
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const calendarGrid = document.getElementById('calendar-grid');
            calendarGrid.innerHTML = '';

            // Generate 42 days (6 weeks)
            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + i);

                // Use timezone-safe date formatting
                const dateStr = cellDate.getFullYear() + '-' +
                    String(cellDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(cellDate.getDate()).padStart(2, '0');
                const isCurrentMonth = cellDate.getMonth() === month;
                const isToday = cellDate.toDateString() === currentDate.toDateString();
                const hasAppointments = calendarData[dateStr] && calendarData[dateStr].length > 0;

                const cell = document.createElement('div');
                cell.className = `
                    relative p-2 h-16 border border-gray-200 cursor-pointer transition-colors
                    ${isCurrentMonth ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 text-gray-400'}
                    ${isToday ? 'border-blue-500 bg-blue-50' : ''}
                    ${hasAppointments ? 'border-green-300' : ''}
                `;

                cell.onclick = () => showDateDetails(dateStr, cellDate);

                // Date number
                const dateNumber = document.createElement('div');
                dateNumber.className = `text-sm font-medium ${isToday ? 'text-blue-600' : ''}`;
                dateNumber.textContent = cellDate.getDate();
                cell.appendChild(dateNumber);

                // Appointment indicator and label
                if (hasAppointments) {
                    const appointments = calendarData[dateStr];
                    const hasConfirmed = appointments.some(apt => apt.status === 'Confirmed');

                    // Create appointment label
                    const label = document.createElement('div');
                    label.className = `absolute bottom-1 left-1 text-xs px-1 py-0.5 rounded text-black font-medium bg-green-600 border border-green-700`;
                    label.textContent = 'Appointment';
                    cell.appendChild(label);

                    // Create count indicator if multiple appointments
                    if (appointments.length > 1) {
                        const countIndicator = document.createElement('div');
                        countIndicator.className = `absolute top-1 right-1 w-5 h-5 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold`;
                        countIndicator.textContent = appointments.length;
                        cell.appendChild(countIndicator);
                    }
                }

                calendarGrid.appendChild(cell);
            }
        }

        function showDateDetails(dateStr, date) {
            const appointments = calendarData[dateStr] || [];
            const detailsDiv = document.getElementById('selected-date-details');
            const noAppointmentsDiv = document.getElementById('no-appointments-message');
            const titleDiv = document.getElementById('selected-date-title');
            const appointmentsDiv = document.getElementById('selected-date-appointments');

            // Format date for display
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            titleDiv.textContent = `Appointments for ${date.toLocaleDateString('en-US', options)}`;

            if (appointments.length > 0) {
                detailsDiv.classList.remove('hidden');
                noAppointmentsDiv.classList.add('hidden');

                appointmentsDiv.innerHTML = '';
                appointments.forEach(appointment => {
                    const appointmentDiv = document.createElement('div');
                    appointmentDiv.className = `
                        flex items-center justify-between p-3 rounded-lg border
                        ${appointment.status === 'Pending' ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200'}
                    `;

                    appointmentDiv.innerHTML = `
                        <div>
                            <p class="font-medium text-gray-800">${appointment.patient_name}</p>
                            <p class="text-sm text-gray-600">${appointment.time}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs px-2 py-1 rounded-full ${
                                appointment.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'
                            }">
                                ${appointment.status}
                            </span>
                            <a href="/doctor/patients/${appointment.patient_id}"
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                View Patient
                            </a>
                        </div>
                    `;

                    appointmentsDiv.appendChild(appointmentDiv);
                });
            } else {
                detailsDiv.classList.add('hidden');
                noAppointmentsDiv.classList.remove('hidden');
            }
        }

        function changeMonth(direction) {
            displayMonth.setMonth(displayMonth.getMonth() + direction);
            generateCalendar();

            // Hide any open date details
            document.getElementById('selected-date-details').classList.add('hidden');
            document.getElementById('no-appointments-message').classList.add('hidden');
        }

        // Initialize calendar on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateCalendar();
        });
    </script>    <!-- All Appointments Table -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">All Appointments</h2>
            <p class="text-gray-600 text-sm">Complete list of all your appointments</p>
        </div>

        <div class="p-6">
            @if($appointments->count() > 0)
                <div class="space-y-4">
                    @foreach($appointments as $appointment)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-3">
                                        <h3 class="text-lg font-medium text-gray-800">
                                            {{ $appointment->patient->user->name ?? 'N/A' }}
                                        </h3>
                                        <span class="inline-block px-3 py-1 text-sm rounded-full
                                            @if($appointment->status === 'Pending') bg-yellow-100 text-yellow-800
                                            @elseif($appointment->status === 'Confirmed') bg-green-100 text-green-800
                                            @elseif($appointment->status === 'Completed') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $appointment->status }}
                                        </span>
                                    </div>

                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><strong>Patient ID:</strong> {{ $appointment->patient_id }}</p>
                                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y') }}</p>
                                        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}</p>
                                        @if($appointment->reason)
                                            <p><strong>Reason:</strong> {{ $appointment->reason }}</p>
                                        @endif
                                        <p><strong>Scheduled:</strong> {{ $appointment->created_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>

                                <div class="text-right space-y-2">
                                    <!-- Status Update Form -->
                                    <form action="{{ route('staff.appointments.update-status', $appointment->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center space-x-2">
                                            <select name="status" class="text-sm px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <option value="Pending" {{ $appointment->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Confirmed" {{ $appointment->status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="Completed" {{ $appointment->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="Cancelled" {{ $appointment->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            <button type="button" onclick="showStatusModal({{ $appointment->id }})" class="bg-blue-600 text-white text-sm px-3 py-1 rounded hover:bg-blue-700 transition">
                                                Update
                                            </button>
                                        </div>
                                    </form>

                                    <div class="space-x-2">
                                        <a href="{{ route('staff.patient-details', $appointment->patient_id) }}"
                                           class="text-green-600 hover:text-green-800 text-sm">
                                            View Patient
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @else
                <!-- No appointments -->
                <div class="text-center py-12">
                    <div class="bg-gray-50 p-8 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">No Appointments Found</h3>
                        <p class="text-gray-600">You don't have any appointments yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
    <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-lg shadow-xl p-6 max-w-md w-half mx-4 z-10">
        <h3 id="modalTitle" class="text-xl font-semibold text-gray-800 mb-2"></h3>
        <p id="modalMessage" class="text-gray-600 mb-6"></p>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                Cancel
            </button>
            <form id="modalForm" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" id="modalConfirmBtn" class="px-4 py-2 text-white rounded transition">
                    Confirm
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function showConfirmModal(action, appointmentId, patientName) {
        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('modalTitle');
        const message = document.getElementById('modalMessage');
        const form = document.getElementById('modalForm');
        const confirmBtn = document.getElementById('modalConfirmBtn');
        const statusInput = document.createElement('input');

        if (action === 'confirm') {
            title.textContent = 'Confirm Appointment';
            message.textContent = `Are you sure you want to confirm the appointment with ${patientName}?`;
            confirmBtn.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded transition';
            confirmBtn.textContent = 'Confirm';
            statusInput.name = 'status';
            statusInput.value = 'Confirmed';
            form.action = `/staff/appointments/${appointmentId}/status`;
        } else if (action === 'cancel') {
            title.textContent = 'Cancel Appointment';
            message.textContent = `Are you sure you want to cancel the appointment with ${patientName}? This action cannot be undone.`;
            confirmBtn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition';
            confirmBtn.textContent = 'Cancel Appointment';
            statusInput.name = 'status';
            statusInput.value = 'Cancelled';
            form.action = `/staff/appointments/${appointmentId}/status`;
        }

        const existingInput = form.querySelector('input[name="status"]');
        if (existingInput) existingInput.remove();
        form.appendChild(statusInput);

        modal.style.display = 'flex';
    }

    function showStatusModal(appointmentId) {
        const select = document.querySelector(`form[action*="/${appointmentId}/status"] select`);
        const status = select ? select.value : 'Confirmed';
        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('modalTitle');
        const message = document.getElementById('modalMessage');
        const form = document.getElementById('modalForm');
        const confirmBtn = document.getElementById('modalConfirmBtn');
        const statusInput = document.createElement('input');

        title.textContent = 'Update Appointment Status';
        message.textContent = `Are you sure you want to change this appointment status to "${status}"?`;
        confirmBtn.className = 'px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition';
        confirmBtn.textContent = 'Update';
        statusInput.name = 'status';
        statusInput.value = status;

        const existingInput = form.querySelector('input[name="status"]');
        if (existingInput) existingInput.remove();
        form.appendChild(statusInput);
        form.action = `/staff/appointments/${appointmentId}/status`;

        modal.style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection
