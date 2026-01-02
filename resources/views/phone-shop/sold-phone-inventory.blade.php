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
    @extends('layouts.inventory')

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

                            {{-- Filters --}}
                            <div class="flex justify-start">
                                <button onclick="toggleFilterForm()"
                                    class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6">
                                    <img src="{{ asset('icons/filter.png') }}" class="w-6 h-6" alt="Filter Icon">
                                    Filters
                                </button>
                            </div>

                            <div id="filterFormContainerSold" class="mt-4 hidden">
                                <form id="filterFormSold" method="GET" action="{{ route('inventory.sold') }}"
                                    class="mb-6 flex gap-6 items-center flex-wrap">

                                    {{-- Phone Type --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Phone Type
                                        </label>

                                        <input type="hidden" name="phone_type" id="soldPhoneTypeInput"
                                            value="{{ request('phone_type') }}">

                                        <button type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleSoldPhoneTypeDropdown(event)">
                                            <span id="selectedSoldPhoneType">
                                                {{ request('phone_type') ?? 'Select Phone Type' }}
                                            </span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="soldPhoneTypeDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="soldPhoneTypeSearch" onkeyup="filterSoldPhoneTypes()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md">

                                            <div onclick="selectSoldPhoneType('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                All Phone Types
                                            </div>

                                            @foreach ($phoneTypes as $type)
                                                <div onclick="selectSoldPhoneType('{{ $type }}')"
                                                    class="sold-phone-type-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $type }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- EMI --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            EMI
                                        </label>

                                        <input type="hidden" name="emi" id="soldEmiInput" value="{{ request('emi') }}">

                                        <button type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleSoldEmiDropdown(event)">
                                            <span id="selectedSoldEmi">
                                                {{ request('emi') ?? 'Select EMI' }}
                                            </span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="soldEmiDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="soldEmiSearch" onkeyup="filterSoldEmis()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md">

                                            <div onclick="selectSoldEmi('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                All EMIs
                                            </div>

                                            @foreach ($emis as $emi)
                                                <div onclick="selectSoldEmi('{{ $emi }}')"
                                                    class="sold-emi-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $emi }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="flex items-end space-x-2 mt-2">
                                        <button type="submit"
                                            class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                            Apply Filters
                                        </button>

                                        <button type="button" onclick="clearFiltersSold()"
                                            class="mt-4 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                                            Clear
                                        </button>
                                    </div>
                                </form>
                            </div>


                            <div class="flex-1">

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Sold Phone Inventory Records
                                    </h1>
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
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-48 text-xs text-gray-600 uppercase">
                                                EMI Number
                                            </th>

                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase">
                                                Model Details
                                            </th>

                                            {{-- Buyer Details --}}
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase">
                                                Buyer Details
                                            </th>

                                            {{-- Sold Price --}}
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase">
                                                Sold Price
                                            </th>

                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($soldInventories as $inventory)
                                            <tr class="text-center">

                                                <!-- EMI -->
                                                <td class="px-4 py-2">
                                                    <span class="font-semibold">{{ $inventory->emi }}</span><br>
                                                    <span
                                                        class="text-xs text-gray-500">{{ Carbon::parse($inventory->sold_date)->format('Y-m-d') }}</span>
                                                </td>

                                                <!-- Model -->
                                                <td class="px-4 py-2 text-left">
                                                    <span class="font-semibold">Model:</span>
                                                    {{ $inventory->phone_type }}<br>
                                                    <span class="font-semibold">Capacity:</span>
                                                    {{ $inventory->capacity }}<br>
                                                    <span class="font-semibold">Colour:</span> {{ $inventory->colour }}
                                                </td>

                                                <!-- Buyer Details -->
                                                <td class="px-4 py-2 text-left">
                                                    <span class="font-semibold">Name:</span>
                                                    {{ $inventory->customer_name ?? '-' }}<br>

                                                    <span class="font-semibold">Phone:</span>
                                                    {{ $inventory->customer_phone ?? '-' }}<br>

                                                    <span class="font-semibold">Invoice:</span>
                                                    {{ $inventory->invoice_number ?? '-' }}
                                                </td>

                                                <!-- Sold Price -->
                                                <td class="px-4 py-2 text-center">
                                                    LKR {{ number_format($inventory->selling_price ?? 0, 2) }}
                                                </td>

                                                <!-- Status -->
                                                <td class="px-4 py-2">
                                                    <button
                                                        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
                                                        onclick="openExchangeModal('{{ $inventory->id }}', '{{ $inventory->customer_name }}')">
                                                        Return / Exchange
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                                    No sold phone records found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">
                                {{ $soldInventories->links() }}
                            </div>
                        </div>

                        <!-- Exchange Modal -->
                        <div id="exchangeModal"
                            class="fixed inset-0 hidden bg-black bg-opacity-50 z-50 flex items-center justify-center min-h-screen">
                            <div class="bg-white rounded-lg shadow-lg w-96 max-w-full p-6 relative">
                                <h2 class="text-lg font-semibold mb-4">Return / Exchange Phone</h2>

                                <form id="exchangeForm" method="POST" action="{{ route('inventory.exchange') }}">
                                    @csrf
                                    <input type="hidden" name="inventory_id" id="exchangeInventoryId">

                                    <!-- Buyer Name (readonly) -->
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Exchanger Name</label>
                                        <input type="text" id="exchangeBuyerName"
                                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-gray-100"
                                            readonly>
                                    </div>

                                    <!-- Cost -->
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Cost</label>
                                        <input type="number" name="cost"
                                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm"
                                            required>
                                    </div>

                                    <!-- Note (optional) -->
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Note</label>
                                        <textarea name="note" id="exchangeNote"
                                            class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm" placeholder="Enter note (optional)"></textarea>
                                    </div>

                                    <div class="flex justify-end space-x-2 mt-4">
                                        <button type="button" onclick="closeExchangeModal()"
                                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Cancel</button>
                                        <button type="submit"
                                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Return
                                            Phone
                                        </button>
                                    </div>
                                </form>
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
            const form = document.getElementById('filterFormContainerSold');
            form.classList.toggle('hidden');
        }
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

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const phoneTypeMenu = document.getElementById("soldPhoneTypeDropdownMenu");
            const emiMenu = document.getElementById("soldEmiDropdownMenu");

            phoneTypeMenu.addEventListener("click", e => e.stopPropagation());
            emiMenu.addEventListener("click", e => e.stopPropagation());

            window.toggleSoldPhoneTypeDropdown = function(e) {
                e.stopPropagation();
                closeAllSoldDropdowns();
                phoneTypeMenu.classList.toggle("hidden");
            }

            window.selectSoldPhoneType = function(val) {
                document.getElementById("soldPhoneTypeInput").value = val;
                document.getElementById("selectedSoldPhoneType").textContent = val || "Select Phone Type";
                closeAllSoldDropdowns();
            }

            window.filterSoldPhoneTypes = function() {
                const f = document.getElementById("soldPhoneTypeSearch").value.toLowerCase();
                document.querySelectorAll(".sold-phone-type-option")
                    .forEach(o => o.style.display = o.textContent.toLowerCase().includes(f) ? "block" : "none");
            }

            window.toggleSoldEmiDropdown = function(e) {
                e.stopPropagation();
                closeAllSoldDropdowns();
                emiMenu.classList.toggle("hidden");
            }

            window.selectSoldEmi = function(val) {
                document.getElementById("soldEmiInput").value = val;
                document.getElementById("selectedSoldEmi").textContent = val || "Select EMI";
                closeAllSoldDropdowns();
            }

            window.filterSoldEmis = function() {
                const f = document.getElementById("soldEmiSearch").value.toLowerCase();
                document.querySelectorAll(".sold-emi-option")
                    .forEach(o => o.style.display = o.textContent.toLowerCase().includes(f) ? "block" : "none");
            }

            function closeAllSoldDropdowns() {
                phoneTypeMenu.classList.add("hidden");
                emiMenu.classList.add("hidden");
            }

            document.addEventListener("click", closeAllSoldDropdowns);

            window.clearFiltersSold = function() {
                window.location.href = "{{ route('inventory.sold') }}";
            }

        });
    </script>

    <script>
        function openExchangeModal(inventoryId, buyerName, note = '') {
            document.getElementById('exchangeInventoryId').value = inventoryId;
            document.getElementById('exchangeBuyerName').value = buyerName;
            document.getElementById('exchangeNote').value = note; // prefill note if exists
            document.getElementById('exchangeModal').classList.remove('hidden');
        }

        function closeExchangeModal() {
            document.getElementById('exchangeModal').classList.add('hidden');
        }
    </script>
@endsection
