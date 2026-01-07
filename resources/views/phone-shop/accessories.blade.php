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

                            <div id="filterFormContainerAccessories" class="mt-4 hidden">
                                <form id="filterFormAccessories" method="GET" action="{{ route('inventory.accessories') }}"
                                    class="mb-6 flex gap-6 items-center flex-wrap">

                                    {{-- Name --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label for="nameDropdown"
                                            class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="hidden" name="name" id="nameInput" value="{{ request('name') }}">
                                        <button id="nameDropdown" type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleNameDropdown(event)">
                                            <span id="selectedName">{{ request('name') ?? 'Select Name' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="nameDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="nameSearch" onkeyup="filterNames()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectName('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Names
                                            </div>
                                            @foreach ($accessories->pluck('name')->unique() as $name)
                                                <div onclick="selectName('{{ $name }}')"
                                                    class="name-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Type --}}
                                    <div class="relative inline-block text-left w-48">
                                        <label for="typeDropdown"
                                            class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                        <input type="hidden" name="type" id="typeInput" value="{{ request('type') }}">
                                        <button id="typeDropdown" type="button"
                                            class="inline-flex w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 h-10"
                                            onclick="toggleTypeDropdown(event)">
                                            <span id="selectedType">{{ request('type') ?? 'Select Type' }}</span>
                                            <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.25 8.29a.75.75 0 0 1-.02-1.08z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div id="typeDropdownMenu"
                                            class="absolute z-40 mt-1 w-full bg-white border rounded-lg shadow-lg hidden max-h-48 overflow-y-auto p-2">
                                            <input type="text" id="typeSearch" onkeyup="filterTypes()"
                                                placeholder="Search..." class="w-full px-2 py-1 text-sm border rounded-md"
                                                autocomplete="off">
                                            <div onclick="selectType('')"
                                                class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">All Types
                                            </div>
                                            @foreach ($accessories->pluck('type')->unique() as $type)
                                                <div onclick="selectType('{{ $type }}')"
                                                    class="type-option px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm">
                                                    {{ $type }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="flex items-end space-x-2 mt-2">
                                        <button type="submit"
                                            class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Apply
                                            Filters</button>
                                        <button type="button" onclick="clearFiltersAccessories()"
                                            class="mt-4 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Clear</button>
                                    </div>
                                </form>
                            </div>

                            <div class="flex-1">

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Accessories Inventory Records
                                    </h1>

                                    <div class="flex space-x-3">

                                        <button
                                            onclick="document.getElementById('addAccessoryModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Accessories Stocks
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Accessory Modal -->
                            <div id="addAccessoryModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center py-5">

                                <div class="w-full max-w-[700px] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-4 transform transition-all scale-95 max-h-[calc(100vh-10rem)] overflow-y-auto"
                                    onclick="event.stopPropagation()">

                                    <div class="max-w-[600px] mx-auto p-8">

                                        <h2
                                            class="text-2xl font-semibold mb-8 text-gray-900 mt-4 text-center dark:text-white">
                                            Add Accessories
                                        </h2>

                                        <form action="{{ route('storeAccessory') }}" method="POST">
                                            @csrf

                                            <!-- ACCESSORY ITEMS -->
                                            <div id="accessoryItemsContainer"></div>

                                            <button type="button" id="addAccessoryItemBtn"
                                                class="mt-4 px-4 py-2 bg-green-500 text-white rounded text-sm">
                                                + Add Accessory
                                            </button>

                                            <!-- MASTER FIELDS -->
                                            <div class="grid grid-cols-2 gap-4 mt-6">

                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Supplier
                                                    </label>
                                                    <input type="text" name="supplier" required
                                                        class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white text-sm">
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Date
                                                    </label>
                                                    <input type="date" name="date" required
                                                        class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white text-sm">
                                                </div>

                                            </div>

                                            <div class="mt-6">
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Commission (Optional)
                                                </label>
                                                <input type="number" step="0.01" name="commission"
                                                    class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:text-white text-sm"
                                                    placeholder="Enter commission amount">
                                            </div>

                                            <!-- ACTIONS -->
                                            <div class="flex justify-end mt-8 space-x-3">
                                                <button type="button"
                                                    onclick="document.getElementById('addAccessoryModal').classList.add('hidden')"
                                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>

                                                <button type="submit"
                                                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                    Save Accessories
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
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase break-words">
                                                Name / Model
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-48 text-xs text-gray-600 uppercase break-words">
                                                Accessory Type
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Quantity
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-40 text-xs text-gray-600 uppercase break-words">
                                                Cost (Single peice)
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-40 text-xs text-gray-600 uppercase break-words">
                                                Supplier
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase break-words">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($accessories as $accessory)
                                            <tr
                                                class="text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">

                                                <!-- Name + Date + Commission -->
                                                <td class="px-4 py-2">
                                                    <span class="font-semibold">{{ $accessory->name }}</span> <br>
                                                    <span
                                                        class="text-xs text-gray-500">{{ $accessory->date->format('d M Y') }}</span>
                                                    @if ($accessory->commission && $accessory->commission > 0)
                                                        <div class="mt-1 text-sm text-green-600">
                                                            Commission: LKR {{ number_format($accessory->commission, 2) }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <!-- Type -->
                                                <td class="px-4 py-2">{{ $accessory->type }}</td>

                                                <!-- Quantity -->
                                                <td class="px-4 py-2">{{ $accessory->quantity }}</td>

                                                <!-- Cost -->
                                                <td class="px-4 py-2 font-medium">
                                                    LKR {{ number_format($accessory->cost ?? 0, 2) }}
                                                </td>

                                                <!-- Supplier -->
                                                <td class="px-4 py-2">{{ $accessory->supplier }}</td>

                                                <!-- Actions -->
                                                <td class="px-4 py-2">
                                                    @if(auth()->user()->role === 'ADMIN' || auth()->user()->role === 'SUPERADMIN')
                                                    <div class="inline-flex items-center justify-center gap-2">
                                                        <button type="button"
                                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm"
                                                            onclick="openRemoveModal({{ $accessory->id }}, '{{ $accessory->name }}', {{ $accessory->quantity }})">
                                                            Remove
                                                        </button>
                                                        <!-- Delete Button -->
                                                        <form id="delete-form-{{ $accessory->id }}"
                                                            action="{{ route('accessories.destroy', $accessory->id) }}"
                                                            method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 mt-3 rounded text-sm"
                                                                onclick="confirmDelete('{{ $accessory->id }}')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-2 text-center text-gray-500">No
                                                    accessories found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">
                                {{ $accessories->links() }}
                            </div>
                            <!-- Remove Damaged Modal -->
                            <div id="removeDamagedModal"
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-80">
                                    <h2 class="text-lg font-semibold mb-4">Remove Damaged Item</h2>
                                    <form id="removeDamagedForm" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="qty" class="block text-sm font-medium mb-1">Quantity</label>
                                            <input type="number" name="qty" id="removeQty" min="1"
                                                class="w-full border rounded px-3 py-2" required>
                                            <p id="maxQtyInfo" class="text-xs text-gray-500 mt-1"></p>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                                                onclick="closeRemoveModal()">Cancel</button>
                                            <button type="submit"
                                                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Remove</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>
                                function openRemoveModal(id, name, maxQty) {
                                    const modal = document.getElementById('removeDamagedModal');
                                    modal.classList.remove('hidden');

                                    // Set max info
                                    document.getElementById('maxQtyInfo').innerText = `Available quantity: ${maxQty}`;

                                    // Set form action
                                    const form = document.getElementById('removeDamagedForm');
                                    form.action = `/accessories/remove-damaged/${id}`;

                                    // Set max attribute on input
                                    const input = document.getElementById('removeQty');
                                    input.value = '';
                                    input.max = maxQty;
                                }

                                function closeRemoveModal() {
                                    const modal = document.getElementById('removeDamagedModal');
                                    modal.classList.add('hidden');
                                }
                            </script>

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
            const form = document.getElementById('filterFormContainerAccessories');
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
        let accessoryIndex = 0;

        const accessoryTypes = [
            'Charging Docks',
            'Data Cables',
            'Airpods',
            'Handfrees',
            'Power Banks'
        ];

        document.addEventListener('DOMContentLoaded', () => {
            const addBtn = document.getElementById('addAccessoryItemBtn');
            if (addBtn) addBtn.addEventListener('click', addAccessoryItem);

            addAccessoryItem(); // keep one by default
        });

        function addAccessoryItem() {
            const container = document.getElementById('accessoryItemsContainer');

            const optionsHTML = accessoryTypes.map(type =>
                `<button type="button"
                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                onclick="selectAccessoryType(this, '${type}')">${type}</button>`
            ).join('');

            const itemHTML = `
        <div class="item-group border rounded-md p-4 mb-4 bg-gray-50">

            <!-- ACCESSORY TYPE -->
            <div class="relative w-full">
                <label class="block text-sm font-medium mb-1">Accessory Type</label>
                <button type="button"
                    class="dropdown-btn flex justify-between w-full rounded-md bg-white px-3 py-2 font-semibold text-sm border items-center"
                    onclick="toggleAccessoryDropdown(this)">
                    <span class="selected-accessory">Select Accessory Type</span>
                    <span class="dropdown-arrow transform transition-transform duration-200">▼</span>
                </button>


                <div class="dropdown-menu-accessory hidden absolute z-10 mt-2 w-full bg-white border rounded shadow">
                    ${optionsHTML}
                </div>

                <input type="hidden" name="items[${accessoryIndex}][type]" class="input-accessory">
            </div>

            <!-- NAME -->
            <div class="mt-3">
                <label class="block text-sm font-medium">Name / Model</label>
                <input type="text"
                    name="items[${accessoryIndex}][name]"
                    class="w-full mt-1 px-3 py-2 border rounded"
                    required>
            </div>

            <!-- QTY + COST -->
            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="block text-sm font-medium">Quantity</label>
                    <input type="number"
                        name="items[${accessoryIndex}][quantity]"
                        min="1"
                        value="1"
                        class="w-full mt-1 px-3 py-2 border rounded"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium">Cost</label>
                    <input type="number"
                        step="0.01"
                        name="items[${accessoryIndex}][cost]"
                        class="w-full mt-1 px-3 py-2 border rounded"
                        required>
                </div>
            </div>

            <!-- REMOVE -->
            <div class="flex justify-end mt-3">
                <button type="button"
                    onclick="removeAccessoryItem(this)"
                    class="px-3 py-1 bg-red-500 text-white rounded text-sm">
                    Remove
                </button>
            </div>

        </div>
        `;

            container.insertAdjacentHTML('beforeend', itemHTML);
            accessoryIndex++;
        }

        function removeAccessoryItem(button) {
            const container = document.getElementById('accessoryItemsContainer');
            const items = container.querySelectorAll('.item-group');

            if (items.length > 1) {
                button.closest('.item-group').remove();
            } else {
                alert('At least one accessory is required.');
            }
        }

        function toggleAccessoryDropdown(btn) {
            const menu = btn.nextElementSibling;
            const arrow = btn.querySelector('.dropdown-arrow');

            // Close all other dropdowns and reset their arrows
            document.querySelectorAll('.dropdown-menu-accessory').forEach(m => {
                if (m !== menu) {
                    m.classList.add('hidden');
                    m.previousElementSibling.querySelector('.dropdown-arrow').classList.remove('rotate-180');
                }
            });

            // Toggle current menu
            menu.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }

        function selectAccessoryType(button, value) {
            const parent = button.closest('.relative');
            parent.querySelector('.selected-accessory').innerText = value;
            parent.querySelector('.input-accessory').value = value;
            parent.querySelector('.dropdown-menu-accessory').classList.add('hidden');
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // ---- NAME ----
            const nameMenu = document.getElementById("nameDropdownMenu");
            nameMenu.addEventListener("click", e => e.stopPropagation());

            window.toggleNameDropdown = function(e) {
                e.stopPropagation();
                closeAllAccessoryDropdowns();
                nameMenu.classList.toggle("hidden");
            }

            window.selectName = function(val) {
                document.getElementById("nameInput").value = val;
                document.getElementById("selectedName").textContent = val || "Select Name";
                closeAllAccessoryDropdowns();
            }

            window.filterNames = function() {
                const f = document.getElementById("nameSearch").value.toLowerCase();
                document.querySelectorAll(".name-option").forEach(o => o.style.display = o.textContent
                    .toLowerCase().includes(f) ? "block" : "none");
            }

            // ---- TYPE ----
            const typeMenu = document.getElementById("typeDropdownMenu");
            typeMenu.addEventListener("click", e => e.stopPropagation());

            window.toggleTypeDropdown = function(e) {
                e.stopPropagation();
                closeAllAccessoryDropdowns();
                typeMenu.classList.toggle("hidden");
            }

            window.selectType = function(val) {
                document.getElementById("typeInput").value = val;
                document.getElementById("selectedType").textContent = val || "Select Type";
                closeAllAccessoryDropdowns();
            }

            window.filterTypes = function() {
                const f = document.getElementById("typeSearch").value.toLowerCase();
                document.querySelectorAll(".type-option").forEach(o => o.style.display = o.textContent
                    .toLowerCase().includes(f) ? "block" : "none");
            }

            function closeAllAccessoryDropdowns() {
                nameMenu.classList.add("hidden");
                typeMenu.classList.add("hidden");
            }

            document.addEventListener("click", closeAllAccessoryDropdowns);

            window.clearFiltersAccessories = function() {
                window.location.href = window.location.pathname;
            }
        });
    </script>
@endsection
