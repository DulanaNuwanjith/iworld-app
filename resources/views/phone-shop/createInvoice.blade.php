@php use Carbon\Carbon; @endphp

<head>

    <!-- Import Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title> Iworld-Invoice </title>
</head>
<div class="flex h-full w-full bg-white">
    @extends('layouts.create-invoice')

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
                                        html: '<span style="color:#6c757d;">This record will be permanently deleted!</span>',
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

                            <div class="flex-1">

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Invoice Records
                                    </h1>

                                    <div class="flex space-x-3">

                                        <button
                                            onclick="document.getElementById('addPhoneModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Invoice
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Phone Stock Modal -->
                            <div id="addPhoneModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center py-5">
                                <div class="w-full max-w-[700px] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-4 transform transition-all scale-95 max-h-[calc(100vh-10rem)] overflow-y-auto"
                                    onclick="event.stopPropagation()">
                                    <div class="max-w-[600px] mx-auto p-8">

                                        <h2 class="text-2xl font-semibold mb-8 text-gray-900 mt-4 text-center">
                                            Add Phone Stock
                                        </h2>

                                        <!-- Unified Form -->
                                        <form id="unifiedOrderForm" action=""
                                            method="POST">
                                            @csrf

                                            <div id="itemsContainer"></div>

                                            <button type="button" id="addItemBtn"
                                                class="mt-4 px-4 py-2 bg-green-500 text-white rounded text-sm">
                                                + Add Phone
                                            </button>

                                            <!-- MASTER FIELDS -->
                                            <div class="grid grid-cols-2 gap-4 mt-3">
                                                <div class="mt-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">
                                                        Stock Type
                                                    </label>
                                                    <div class="relative w-full text-left">
                                                        <button type="button"
                                                            class="dropdown-btn inline-flex justify-between w-full rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                                            onclick="toggleDropdownStock(this)">
                                                            <span class="selected-stock">Select Stock Type</span>
                                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </button>

                                                        <div
                                                            class="dropdown-menu-stock hidden absolute z-10 mt-2 w-full rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black/5 max-h-48 overflow-y-auto">
                                                            <div class="py-1 options-container flex flex-col"
                                                                role="listbox" tabindex="-1">
                                                                <button type="button"
                                                                    class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600"
                                                                    onclick="selectDropdownStock(this, 'Exchange')">Exchange</button>
                                                                <button type="button"
                                                                    class="dropdown-option w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600"
                                                                    onclick="selectDropdownStock(this, 'Direct Import')">Direct
                                                                    Import</button>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="stock_type" class="input-stock"
                                                            value="">
                                                    </div>
                                                </div>

                                                <div class="mt-6">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Date
                                                    </label>
                                                    <input type="date" name="date" required
                                                        class="w-full mt-1 px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white text-sm">
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Supplier
                                                </label>
                                                <input type="text" name="supplier" required
                                                    class="w-full mt-1 px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white text-sm">
                                            </div>

                                            <!-- ACTIONS -->
                                            <div class="flex justify-end mt-6 space-x-3">
                                                <button type="button"
                                                    onclick="document.getElementById('addPhoneModal').classList.add('hidden')"
                                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>

                                                <button type="submit" id="createPhoneBtn"
                                                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                    Save Stock
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
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-48 text-xs text-gray-600 uppercase break-words">
                                                EMI Number
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Model Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-40 text-xs text-gray-600 uppercase break-words">
                                                Supplier
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Stock Type
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-40 text-xs text-gray-600 uppercase break-words">
                                                Cost
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Note
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">
                                
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
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('#addPhoneModal form');
            const submitBtn = document.getElementById('createPhoneBtn');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Submitting...';
            });
        });
    </script>

@endsection
