@php use Carbon\Carbon; @endphp

<head>

    <!-- Import Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title> Iworld-Finance </title>
</head>
<div class="flex h-full w-full bg-white">
    @extends('layouts.finance')

    @section('content')
        <div class="flex-1 overflow-y-hidden">
            <div class="">
                <div class="w-full px-6 lg:px-2">
                    <div class="bg-white overflow-hidden">
                        <div class="p-4 text-gray-900">
                            {{-- Style for Sweet Alert --}}
                            <style>
                                .swal2-toast {
                                    font-size: 0.875rem;
                                    padding: 0.75rem 1rem;
                                    border-radius: 8px;
                                    background-color: #ffffff !important;
                                    position: relative;
                                    box-sizing: border-box;
                                    color: #3b82f6 !important;
                                }

                                .swal2-toast .swal2-title,
                                .swal2-toast .swal2-html-container {
                                    color: #3b82f6 !important;
                                }

                                .swal2-toast .swal2-icon {
                                    color: #3b82f6 !important;
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
                                    background-color: #3b82f6;
                                    border-radius: 0 0 8px 8px;
                                }
                            </style>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
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
                                        });
                                    @endif

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
                                        });
                                    @endif
                                });
                            </script>

                            <script>
                                function confirmDelete(id) {
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: "This record will be permanently deleted!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3b82f6',
                                        cancelButtonColor: '#6c757d',
                                        confirmButtonText: 'Yes, delete it!',
                                        background: '#ffffff',
                                        color: '#3b82f6',
                                        customClass: {
                                            popup: 'swal2-toast swal2-shadow'
                                        }
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
                                <button onclick="toggleReportForm()"
                                    class="bg-white border border-gray-500 text-gray-500 hover:text-gray-600 hover:border-gray-600 font-semibold py-1 px-3 rounded shadow flex items-center gap-2 mb-6 ml-2">
                                    Generate Report
                                </button>
                            </div>

                            <div id="filterFormContainer" class="hidden mt-4">

                            </div>

                            {{-- Generate Reports for Customer Coordinator --}}
                            <div class="flex-1">
                                <div id="reportFormContainer" class="hidden mt-4">

                                </div>

                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="text-2xl font-bold text-gray-800">Finance Records
                                    </h1>

                                    <div class="flex space-x-3">

                                        <button
                                            onclick="document.getElementById('addFinanceModal').classList.remove('hidden')"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded shadow">
                                            + Add Finance Order
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Table --}}
                            <div id="sampleInquiryRecordsScroll"
                                class="overflow-x-auto max-h-[1200px] bg-white shadow rounded-lg">
                                <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                                    <thead class="bg-gray-200 text-left">
                                        <tr class="text-center">
                                            <th
                                                class="font-bold sticky left-0 top-0 z-20 bg-white px-4 py-3 w-32 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Order No
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Buyer Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Item Details
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-36 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Mails & Passwords
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-32 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Price & Payment
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Note
                                            </th>
                                            <th
                                                class="font-bold sticky top-0 bg-gray-200 px-4 py-3 w-56 text-xs text-gray-600 uppercase whitespace-normal break-words">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($financeOrders as $order)
                                            <tr class="text-center">
                                                <!-- Order No -->
                                                <td class="sticky left-0 bg-white px-4 py-3 text-xs">
                                                    {{ $order->order_number }}
                                                </td>

                                                <!-- Buyer Details -->
                                                <td class="px-4 py-3 text-xs text-left">
                                                    <div>Created: {{ $order->item_created_date }}</div>
                                                    <div>Name: {{ $order->buyer_name }}</div>
                                                    <div>ID: {{ $order->buyer_id }}</div>
                                                    <div>Address: {{ $order->buyer_address }}</div>
                                                    <div>Phone 1: {{ $order->phone_1 }}</div>
                                                    <div>Phone 2: {{ $order->phone_2 }}</div>
                                                    <div class="mt-1 flex gap-1">
                                                        @if ($order->id_photo)
                                                            <img src="{{ asset('storage/' . $order->id_photo) }}"
                                                                alt="ID Photo" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                        @if ($order->electricity_bill_photo)
                                                            <img src="{{ asset('storage/' . $order->electricity_bill_photo) }}"
                                                                alt="Electricity Bill"
                                                                class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Item Details -->
                                                <td class="px-4 py-3 text-xs text-left">
                                                    <div>Item: {{ $order->item_name }}</div>
                                                    <div>EMI: {{ $order->emi_number }}</div>
                                                    <div>Colour: {{ $order->colour }}</div>
                                                    <div class="mt-1 flex gap-1">
                                                        @if ($order->photo_1)
                                                            <img src="{{ asset('storage/' . $order->photo_1) }}"
                                                                alt="Photo 1" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                        @if ($order->photo_2)
                                                            <img src="{{ asset('storage/' . $order->photo_2) }}"
                                                                alt="Photo 2" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                        @if ($order->photo_about)
                                                            <img src="{{ asset('storage/' . $order->photo_about) }}"
                                                                alt="About Photo" class="w-16 h-16 object-cover rounded">
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Mails & Passwords -->
                                                <td class="px-4 py-3 text-xs text-left">
                                                    <div>iCloud: {{ $order->icloud_mail }}</div>
                                                    <div>Password: {{ $order->icloud_password }}</div>
                                                    <div>Screen Lock: {{ $order->screen_lock_password }}</div>
                                                </td>

                                                <!-- Price & Payment -->
                                                <td class="px-4 py-3 text-xs">
                                                    {{ $order->price ?? '-' }}
                                                </td>

                                                <!-- Note -->
                                                <td class="px-4 py-3 text-xs">
                                                    {{ $order->note ?? '-' }}
                                                </td>

                                                <!-- Actions -->
                                                <td class="px-4 py-3 text-xs">
                                                    <!-- Edit button -->
                                                    <a href=""
                                                        class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                                        Edit
                                                    </a>

                                                    <!-- Delete button -->
                                                    <form action="{{ route('finance.destroy', $order->id) }}"
                                                        method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            onclick="return confirm('Are you sure you want to delete this order?')"
                                                            class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="py-6 flex justify-center">

                            </div>

                            <!-- Add Finance Modal -->
                            <div id="addFinanceModal"
                                class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center py-5">
                                <div class="w-full max-w-[700px] bg-white rounded-2xl shadow-2xl p-4 transform transition-all scale-95 max-h-[calc(100vh-10rem)] overflow-y-auto"
                                    onclick="event.stopPropagation()">
                                    <div class="max-w-[600px] mx-auto p-8">
                                        <h2
                                            class="text-2xl font-semibold mb-8 text-gray-900 mt-4 text-center">
                                            Add New Finance Order
                                        </h2>

                                        <form action="{{ route('financeOrders.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="space-y-4">

                                                <!-- Item Created Date -->
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 ">Item
                                                        Created Date</label>
                                                    <input type="date" name="item_created_date" required
                                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                </div>

                                                <!-- Buyer Info -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 ">Buyer
                                                            Name</label>
                                                        <input type="text" name="buyer_name" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Buyer
                                                            ID</label>
                                                        <input type="text" name="buyer_id" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">Buyer
                                                        Address</label>
                                                    <textarea name="buyer_address" required
                                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                                                </div>

                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Phone
                                                            1</label>
                                                        <input type="text" name="phone_1" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Phone
                                                            2</label>
                                                        <input type="text" name="phone_2"
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <!-- ID Photo & Electricity Bill Photo -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">ID
                                                            Photo</label>
                                                        <input type="file" name="id_photo" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">House
                                                            Electricity Bill</label>
                                                        <input type="file" name="electricity_bill_photo"
                                                            accept="image/*" class="w-full mt-1 text-sm">
                                                    </div>
                                                </div>

                                                <!-- Item Details -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 ">Item
                                                            Name</label>
                                                        <input type="text" name="item_name" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/2">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">EMI
                                                            Number</label>
                                                        <input type="text" name="emi_number" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">Colour</label>
                                                    <input type="text" name="colour" required
                                                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                </div>

                                                <!-- Photos -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Photo
                                                            1</label>
                                                        <input type="file" name="photo_1" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                    <div class="w-1/3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Photo
                                                            2</label>
                                                        <input type="file" name="photo_2" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                    <div class="w-1/3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Photo
                                                            About</label>
                                                        <input type="file" name="photo_about" accept="image/*"
                                                            class="w-full mt-1 text-sm">
                                                    </div>
                                                </div>

                                                <!-- iCloud Details -->
                                                <div class="flex gap-4">
                                                    <div class="w-1/3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">iCloud
                                                            Mail</label>
                                                        <input type="email" name="icloud_mail" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">iCloud
                                                            Password</label>
                                                        <input type="text" name="icloud_password" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                    <div class="w-1/3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700">Screen
                                                            Lock Password</label>
                                                        <input type="text" name="screen_lock_password" required
                                                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm">
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- Buttons -->
                                            <div class="flex justify-end gap-3 mt-6">
                                                <button type="button"
                                                    onclick="document.getElementById('addFinanceModal').classList.add('hidden')"
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                    Create Finance Order
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
        </div>
    </div>

    <script>
        function toggleFilterForm() {
            const form = document.getElementById('filterFormContainer');
            form.classList.toggle('hidden');
        }

        function toggleReportForm() {
            const form = document.getElementById('reportFormContainer');
            form.classList.toggle('hidden');
        }
    </script>
@endsection
