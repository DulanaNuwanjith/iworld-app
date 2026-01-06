@php use Carbon\Carbon; @endphp

<head>

    <!-- Import Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title> Iworld-Inventory </title>
</head>
<div class="flex h-full w-full bg-white">
    @extends('layouts.compensation')

    @section('content')
        <div class="flex-1 overflow-y-hidden">
            <div class="">
                <div class="w-full px-6 lg:px-2">
                    <div class="bg-white overflow-hidden">
                        <div class="p-4 text-gray-900">
                            {{-- Style for Sweet Alert --}}
                            <style>
                                /* Toast style */
                                .swal2-toast {
                                    font-size: 0.875rem;
                                    padding: 0.75rem 1rem;
                                    border-radius: 8px;
                                    background-color: #ffffff !important;
                                    position: relative;
                                    box-sizing: border-box;
                                    color: #6c757d !important;
                                    /* Medium gray */
                                }

                                .swal2-toast .swal2-title,
                                .swal2-toast .swal2-html-container {
                                    color: #495057 !important;
                                    /* Darker gray */
                                }

                                .swal2-toast .swal2-icon {
                                    color: #6c757d !important;
                                    /* Icon gray */
                                }

                                .swal2-shadow {
                                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                                }

                                .swal2-toast::after {
                                    content: '';
                                    position: absolute;
                                    bottom: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 3px;
                                    background-color: #6c757d;
                                    /* Gray underline */
                                    border-radius: 0 0 8px 8px;
                                }
                            </style>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    // Success toast
                                    @if (session('success'))
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'success',
                                            title: '{{ session('success') }}',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            customClass: {
                                                popup: 'swal2-toast swal2-shadow'
                                            },
                                        });
                                    @endif

                                    // Error toast
                                    @if (session('error'))
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'error',
                                            title: '{{ session('error') }}',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            customClass: {
                                                popup: 'swal2-toast swal2-shadow'
                                            },
                                            iconColor: '#6c757d'
                                        });
                                    @endif

                                    // Validation errors toast
                                    @if ($errors->any())
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'warning',
                                            title: 'Validation Errors',
                                            html: `{!! implode('<br>', $errors->all()) !!}`,
                                            showConfirmButton: false,
                                            timer: 3000,
                                            timerProgressBar: true,
                                            customClass: {
                                                popup: 'swal2-toast swal2-shadow'
                                            },
                                            iconColor: '#6c757d'
                                        });
                                    @endif
                                });

                                // Delete confirmation
                                function confirmDelete(id) {
                                    Swal.fire({
                                        title: '<span style="color:#495057;">Are you sure?</span>',
                                        html: '<span style="color:#6c757d;">This worker will be permanently deleted!</span>',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc3545', // Red
                                        cancelButtonColor: '#adb5bd', // Light gray
                                        confirmButtonText: 'Yes, delete it!',
                                        cancelButtonText: 'Cancel',
                                        background: '#ffffff',
                                        customClass: {
                                            popup: 'swal2-toast swal2-shadow'
                                        },
                                        iconColor: '#6c757d'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            document.getElementById(`delete-form-${id}`).submit();
                                        }
                                    });
                                }
                            </script>

                            {{-- Filters --}}
                            <div class="flex justify-start">
                                <button onclick="toggleFilterForm()"
                                    class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6">
                                    <img src="{{ asset('icons/filter.png') }}" class="w-6 h-6" alt="Filter Icon">
                                    Filters
                                </button>
                            </div>

                            <div id="filterFormContainerWorkerDetails" class="mt-4 hidden">

                            </div>


                            <div class="flex-1">

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Workers Details Records
                                    </h1>
                                    <div class="flex space-x-3">
                                        <button
                                            onclick="document.getElementById('addWorkerModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Workers Details Record
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Worker Modal -->
                            <div id="addWorkerModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center py-5">

                                <div class="w-full max-w-[700px] bg-white rounded-2xl shadow-2xl p-4 transform transition-all scale-95
        max-h-[calc(100vh-10rem)] overflow-y-auto"
                                    onclick="event.stopPropagation()">

                                    <div class="max-w-[600px] mx-auto p-8">

                                        <h2 class="text-2xl font-semibold mb-8 text-gray-900 mt-4 text-center">
                                            Add New Worker
                                        </h2>

                                        <form action="{{ route('workers.store') }}" method="POST">
                                            @csrf

                                            <div class="space-y-4">

                                                <!-- Name -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">
                                                        Name
                                                    </label>
                                                    <input type="text" name="name" required
                                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                </div>

                                                <!-- National ID -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">
                                                        National ID No
                                                    </label>
                                                    <input type="text" name="national_id" required
                                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                </div>

                                                <!-- Address -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">
                                                        Address
                                                    </label>
                                                    <textarea name="address" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                                                </div>

                                                <!-- Phones -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">
                                                            Phone No 01
                                                        </label>
                                                        <input type="text" name="phone_1" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>

                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">
                                                            Phone No 02
                                                        </label>
                                                        <input type="text" name="phone_2"
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <!-- Joined Date & Job Title -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">
                                                            Joined Date
                                                        </label>
                                                        <input type="date" name="joined_date" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>

                                                    <div class="w-1/2">
                                                        <label class="block text-sm font-medium text-gray-700">
                                                            Job Title
                                                        </label>
                                                        <input type="text" name="job_title" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <!-- Salary -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">
                                                        Basic Salary (LKR)
                                                    </label>
                                                    <input type="number" name="basic_salary" step="0.01" min="0"
                                                        required
                                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
                                                        placeholder="Enter salary amount">
                                                </div>

                                                <!-- Note -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">
                                                        Note
                                                    </label>
                                                    <textarea name="note" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                                                </div>

                                            </div>

                                            <!-- Buttons -->
                                            <div class="flex justify-end gap-3 mt-6">
                                                <button type="button"
                                                    onclick="document.getElementById('addWorkerModal').classList.add('hidden')"
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>

                                                <button type="submit"
                                                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                    Save Worker
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Table --}}
                            <div id="sampleInquiryRecordsScroll" class="overflow-x-auto bg-white shadow rounded-lg">
                                <!-- Spinner -->
                                <div id="pageLoadingSpinner"
                                    class="fixed inset-0 z-50 bg-white bg-opacity-80 flex flex-col items-center justify-center">
                                    <svg class="animate-spin h-10 w-10 text-gray-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4">
                                        </circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <p class="mt-3 text-gray-700 font-semibold">Loading data...</p>
                                </div>
                                <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-200 text-left">
                                        <tr class="text-center">

                                            <!-- Worker Name -->
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-48 text-xs text-gray-600 uppercase">
                                                Worker Name
                                            </th>

                                            <!-- Job Details -->
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase">
                                                Job Details
                                            </th>

                                            <!-- Contact Details -->
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase">
                                                Contact Details
                                            </th>

                                            <!-- Salary -->
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase">
                                                Basic Salary
                                            </th>

                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase">
                                                Note
                                            </th>

                                            <!-- Action -->
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">

                                        @forelse ($workers as $worker)
                                            <tr class="text-center" id="worker-row-{{ $worker->id }}">

                                                <!-- Worker Name (NOT editable) -->
                                                <td class="px-4 py-2">
                                                    <span class="font-semibold">{{ $worker->name }}</span><br>
                                                    <span class="text-xs text-gray-500">{{ $worker->national_id }}</span>
                                                </td>

                                                <!-- Job Title + Joined Date -->
                                                <td class="px-4 py-2 text-left">
                                                    <span class="font-semibold">Title:</span>
                                                    <span class="view-mode">{{ $worker->job_title }}</span>

                                                    <input type="text" name="job_title"
                                                        value="{{ $worker->job_title }}"
                                                        class="edit-mode hidden w-full mt-1 px-2 py-1 border rounded text-sm">

                                                    <br>

                                                    <span class="font-semibold">Joined:</span>
                                                    {{ \Carbon\Carbon::parse($worker->joined_date)->format('Y-m-d') }}
                                                </td>

                                                <!-- Contact Details -->
                                                <td class="px-4 py-2 text-left">

                                                    <span class="font-semibold">Phone 1:</span>
                                                    <span class="view-mode">{{ $worker->phone_1 }}</span>

                                                    <input type="text" name="phone_1" value="{{ $worker->phone_1 }}"
                                                        class="edit-mode hidden w-full mt-1 px-2 py-1 border rounded text-sm">

                                                    <br>

                                                    <span class="font-semibold">Phone 2:</span>
                                                    <span class="view-mode">{{ $worker->phone_2 ?? '—' }}</span>

                                                    <input type="text" name="phone_2" value="{{ $worker->phone_2 }}"
                                                        class="edit-mode hidden w-full mt-1 px-2 py-1 border rounded text-sm">

                                                    <br>

                                                    <span class="font-semibold">Address:</span>
                                                    <span class="view-mode text-xs text-gray-600">
                                                        {{ Str::limit($worker->address, 30) }}
                                                    </span>

                                                    <textarea name="address" class="edit-mode hidden w-full mt-1 px-2 py-1 border rounded text-sm" rows="2">{{ $worker->address }}</textarea>
                                                </td>

                                                <!-- Salary -->
                                                <td class="px-4 py-2 text-center">
                                                    <span class="view-mode font-semibold">
                                                        LKR {{ number_format($worker->basic_salary, 2) }}
                                                    </span>

                                                    <input type="number" name="basic_salary" step="0.01"
                                                        value="{{ $worker->basic_salary }}"
                                                        class="edit-mode hidden w-full mt-1 px-2 py-1 border rounded text-sm">
                                                </td>

                                                <!-- NOTE (UNCHANGED – same as you had) -->
                                                <td class="px-4 py-3 text-xs text-center whitespace-normal break-words">
                                                    <form action="{{ route('workers.update-note', $worker->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <textarea name="note" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" rows="2" required>{{ old('note', $worker->note) }}</textarea>

                                                        <button type="submit"
                                                            class="w-full mt-1 px-2 py-1 bg-gray-500 text-white rounded
                hover:bg-gray-600 transition-all duration-200 text-xs">
                                                            Save
                                                        </button>
                                                    </form>
                                                </td>

                                                <!-- Action -->
                                                <td class="px-4 py-2">

                                                    <!-- Edit -->
                                                    <button onclick="enableEdit({{ $worker->id }})"
                                                        class="view-mode bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                                                        Edit
                                                    </button>

                                                    <!-- Save -->
                                                    <button onclick="saveWorker({{ $worker->id }})"
                                                        class="edit-mode hidden bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                                        Save
                                                    </button>

                                                    <!-- Cancel -->
                                                    <button onclick="cancelEdit({{ $worker->id }})"
                                                        class="edit-mode hidden bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm mt-1">
                                                        Cancel
                                                    </button>

                                                    <!-- Delete -->
                                                    <form id="delete-form-{{ $worker->id }}"
                                                        action="{{ route('workers.destroy', $worker->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            onclick="confirmDelete({{ $worker->id }})"
                                                            class="view-mode bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm mt-1">
                                                            Delete
                                                        </button>
                                                    </form>

                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                                    No worker records found.
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>
                            <div class="py-6 flex justify-center">
                                {{ $workers->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const spinner = document.getElementById("pageLoadingSpinner");

            // Show spinner immediately
            spinner.classList.remove("hidden");

            // Wait for table to render completely
            window.requestAnimationFrame(() => {
                spinner.classList.add("hidden"); // hide spinner after rendering
            });
        });
    </script>

    <script>
        function toggleFilterForm() {
            const form = document.getElementById('filterFormContainerWorkerDetails');
            form.classList.toggle('hidden');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('#addWorkerModal form');
            const submitBtn = document.getElementById('createPhoneBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Submitting...';
            });
        });
    </script>

    <script>
        function enableEdit(id) {
            const row = document.getElementById(`worker-row-${id}`);
            row.querySelectorAll('.view-mode').forEach(e => e.classList.add('hidden'));
            row.querySelectorAll('.edit-mode').forEach(e => e.classList.remove('hidden'));
        }

        function cancelEdit(id) {
            const row = document.getElementById(`worker-row-${id}`);
            row.querySelectorAll('.edit-mode').forEach(e => e.classList.add('hidden'));
            row.querySelectorAll('.view-mode').forEach(e => e.classList.remove('hidden'));
        }

        function saveWorker(id) {
            const row = document.getElementById(`worker-row-${id}`);

            fetch(`/workers/${id}/update-inline`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        job_title: row.querySelector('[name="job_title"]').value,
                        phone_1: row.querySelector('[name="phone_1"]').value,
                        phone_2: row.querySelector('[name="phone_2"]').value,
                        address: row.querySelector('[name="address"]').value,
                        basic_salary: row.querySelector('[name="basic_salary"]').value,
                        _method: 'PATCH'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update view-mode spans dynamically
                        row.querySelector('[name="job_title"]').previousElementSibling.textContent = row.querySelector(
                            '[name="job_title"]').value;
                        row.querySelector('[name="phone_1"]').previousElementSibling.textContent = row.querySelector(
                            '[name="phone_1"]').value;
                        row.querySelector('[name="phone_2"]').previousElementSibling.textContent = row.querySelector(
                            '[name="phone_2"]').value || '—';
                        row.querySelector('[name="address"]').previousElementSibling.textContent = row.querySelector(
                                '[name="address"]').value.length > 30 ? row.querySelector('[name="address"]').value
                            .substring(0, 30) + '...' : row.querySelector('[name="address"]').value;
                        row.querySelector('[name="basic_salary"]').previousElementSibling.textContent = 'LKR ' +
                            parseFloat(row.querySelector('[name="basic_salary"]').value).toFixed(2);

                        // Hide edit mode, show view mode
                        cancelEdit(id);

                        // Show success toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal2-toast swal2-shadow'
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Failed to update worker',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-toast swal2-shadow'
                        }
                    });
                });
        }
    </script>
@endsection
