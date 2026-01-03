<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Iworld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-white text-gray-900">

    <div class="flex h-full w-full">
        @include('layouts.side-bar')

        <div class="flex-1 overflow-y-auto relative bg-white">
            <!-- ✅ TAB NAVIGATION (VISIBLE IN ALL PAGES) -->
            <div class="sticky top-0 z-50 flex space-x-4 border-b border-gray-300 bg-white p-5">

                {{-- Sold Phones Details Tab (PhoneShopOperator or SuperAdmin) --}}
                @if (auth()->user()->hasRole(['PHONESHOPOPERATOR', 'SUPERADMIN', 'ADMIN']))
                    <a href="{{ route('inventory.sold') }}"
                        class="pb-2 px-3 font-semibold 
            {{ request()->routeIs('inventory.sold') ? 'border-b-2 border-gray-500 text-gray-600' : 'text-gray-600' }}">
                        Sold Phones Details
                    </a>
                @endif

                {{-- Phone Inventory Tab (Admin or SuperAdmin) --}}
                @if (auth()->user()->hasRole(['ADMIN', 'SUPERADMIN']))
                    <a href="{{ route('inventory.index') }}"
                        class="pb-2 px-3 font-semibold 
            {{ request()->routeIs('inventory.index') ? 'border-b-2 border-gray-500 text-gray-600' : 'text-gray-600' }}">
                        Phone Inventory
                    </a>
                @endif

            </div>

            <!-- ✅ PAGE CONTENT -->
            <div class="pt-4 px-4">
                @yield('content')
            </div>
        </div>
    </div>


</body>

</html>
