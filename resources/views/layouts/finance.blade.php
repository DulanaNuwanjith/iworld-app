<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>StretchTec</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-white text-gray-900">

    <div class="flex h-full w-full">
        @include('layouts.side-bar')

        <div class="flex-1 overflow-y-auto relative bg-white">
            <!-- ✅ TAB NAVIGATION (VISIBLE IN ALL PAGES) -->
            <div class="sticky top-0 z-50 flex space-x-4 border-b border-gray-300 bg-white p-5">
                {{-- Inquiry Details Tab: visible to Customer Coordinator, Admin, Superadmin --}}

                {{-- Finance Tab --}}
                <a href="{{ route('finance.index') }}"
                    class="pb-2 px-3 font-semibold 
       {{ request()->routeIs('finance.index') ? 'border-b-2 border-gray-500 text-gray-600' : 'text-gray-600' }}">
                    Finance
                </a>

                {{-- Nearest Expected Payments Tab --}}
                <a href="{{ route('finance.nearestPayments') }}"
                    class="pb-2 px-3 font-semibold 
       {{ request()->routeIs('finance.nearestPayments') ? 'border-b-2 border-gray-500 text-gray-600' : 'text-gray-600' }}">
                    Nearest Expected Payments
                </a>

                {{-- Sample Preparation Production Tab: visible to Production Officer, Admin, Superadmin --}}
                {{-- @if (in_array($role, ['PRODUCTIONOFFICER', 'ADMIN', 'SUPERADMIN']))
                <a href="{{ route('sample-preparation-production.index') }}"
                   class="pb-2 px-3 font-semibold {{ request()->routeIs('sample-preparation-production.*') ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600' }}">
                    Sample Production Development
                </a>
            @endif --}}
            </div>

            <!-- ✅ PAGE CONTENT -->
            <div class="pt-4 px-4">
                @yield('content')
            </div>
        </div>
    </div>


</body>

</html>
