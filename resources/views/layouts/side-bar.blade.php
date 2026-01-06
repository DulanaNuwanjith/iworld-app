<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iworld</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="flex h-screen bg-gray-100">

    <script>
        const savedSidebarState = localStorage.getItem('sidebarCollapsed');
        const initialCollapsed = savedSidebarState ? JSON.parse(savedSidebarState) : false;
        window.__initialCollapsed = initialCollapsed;
        document.documentElement.style.setProperty('--sidebar-width', initialCollapsed ? '5rem' : '18rem');
    </script>

    <!-- Sidebar -->
    <aside x-data="{ collapsed: window.__initialCollapsed, initialized: false, open: false }" x-init="initialized = true"
        :class="[
            collapsed ? 'md:w-20 scroll-hidden' : 'md:w-72 overflow-y-auto scroll-hidden',
            { '-translate-x-full': !open, 'translate-x-0': open }
        ]"
        style="width: var(--sidebar-width);"
        class="fixed md:relative z-40 inset-y-0 left-0 transform md:translate-x-0
           transition-transform duration-300 ease-in-out
           bg-gradient-to-b from-gray-700 to-white sidebar-bg
           shadow-md flex flex-col min-h-screen">

        <!-- Toggle Button -->
        <div class="flex justify-end p-6">
            <button
                @click="collapsed = !collapsed; localStorage.setItem('sidebarCollapsed', JSON.stringify(collapsed)); document.documentElement.style.setProperty('--sidebar-width', collapsed ? '5rem' : '18rem');"
                class="bg-white border border-gray-300 rounded-full w-8 p-1 shadow hover:bg-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-900" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        :d="collapsed ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'" />
                </svg>
            </button>
        </div>

        <!-- Logo -->
        <div class="flex items-center justify-between p-4 border-b mt-4 mb-4" x-cloak>
            <a href="{{ route('dashboard') }}" :class="collapsed ? 'hidden' : 'block'">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-18 w-56 mr-2" />
            </a>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex flex-col justify-between flex-1 p-3 text-base font-bold text-gray-900" x-cloak>

            <!-- Menu Items -->
            <ul class="space-y-2">

                <!-- Title -->
                <li>
                    <a x-show="initialized && !collapsed" class="flex items-center bg-white px-4 py-2 rounded">
                        <span>Money Junction Iworld</span>
                    </a>
                </li>

                <!-- Dashboard (Everyone) -->
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('dashboard') ? 'bg-gray-200' : '' }}">
                        <img src="{{ asset('images/statisctics.png') }}" class="w-6 h-6 mr-5" />
                        <span x-show="initialized && !collapsed">Dashboard</span>
                    </a>
                </li>

                <!-- Invoice (SUPERADMIN, ADMIN, PHONESHOPOPERATOR) -->
                @if (auth()->user()->hasRole(['SUPERADMIN', 'ADMIN', 'PHONESHOPOPERATOR']))
                    <li>
                        <a href="{{ route('invoices.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('invoices.*') ? 'bg-gray-200' : '' }}">
                            <img src="{{ asset('images/invoice.png') }}" class="w-6 h-6 mr-5" />
                            <span x-show="initialized && !collapsed">Invoice</span>
                        </a>
                    </li>
                @endif

                <!-- Inventory (SUPERADMIN, ADMIN, PHONESHOPOPERATOR) -->
                @if (auth()->user()->hasRole(['SUPERADMIN', 'ADMIN', 'PHONESHOPOPERATOR']))
                    <li>
                        <a href="{{ route('inventory.sold') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('inventory.*') ? 'bg-gray-200' : '' }}">
                            <img src="{{ asset('images/iphone.png') }}" class="w-6 h-6 mr-5" />
                            <span x-show="initialized && !collapsed">Inventory</span>
                        </a>
                    </li>
                @endif

                <!-- Finance PLC (SUPERADMIN, ADMIN, FINANCECOORDINATOR, FINANCEMONITOR) -->
                @if (auth()->user()->hasRole(['SUPERADMIN', 'ADMIN', 'FINANCECOORDINATOR', 'FINANCEMONITOR']))
                    <li>
                        <a href="{{ route('finance.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('finance.*') ? 'bg-gray-200' : '' }}">
                            <img src="{{ asset('images/acquisition.png') }}" class="w-6 h-6 mr-5" />
                            <span x-show="initialized && !collapsed">Finance PLC</span>
                        </a>
                    </li>
                @endif

                <!-- Reports (SUPERADMIN, ADMIN) -->
                @if (auth()->user()->hasRole(['SUPERADMIN', 'ADMIN']))
                    <li>
                        <a href="{{ route('phoneShopReport.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('financeReport.*', 'phoneShopReport.*') ? 'bg-gray-200' : '' }}">
                            <img src="{{ asset('images/report.png') }}" class="w-6 h-6 mr-5" />
                            <span x-show="initialized && !collapsed">Reports</span>
                        </a>
                    </li>
                @endif

                <!-- Compensation (SUPERADMIN, ADMIN) -->
                @if (auth()->user()->hasRole(['SUPERADMIN', 'ADMIN']))
                    <li>
                        <a href="{{ route('workers.index') }}"
                            class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('workers.index') ? 'bg-gray-200' : '' }}">
                            <img src="{{ asset('images/wages.png') }}" class="w-6 h-6 mr-5" />
                            <span x-show="initialized && !collapsed">Compensation</span>
                        </a>
                    </li>
                @endif

            </ul>

            <!-- Profile & Logout -->
            <ul class="space-y-2 border-t pt-4 mt-4">

                <!-- Profile (Everyone) -->
                <li>
                    <a href="{{ route('profile.show') }}"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('profile.*') ? 'bg-gray-200' : '' }}">
                        <img src="{{ asset('images/employee.png') }}" class="w-6 h-6 mr-5" />
                        <span x-show="initialized && !collapsed">Profile</span>
                    </a>
                </li>

                <!-- Logout (Everyone) -->
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-4 py-2 rounded hover:bg-gray-200 text-left">
                            <img src="{{ asset('images/close.png') }}" class="w-6 h-6 mr-5" />
                            <span x-show="initialized && !collapsed">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </nav>

    </aside>

</body>

</html>
