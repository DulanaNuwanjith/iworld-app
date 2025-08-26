<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iworld</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="flex h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-gray-700 to-white min-h-screen shadow-md flex flex-col">
        <!-- Logo -->
        <div class="flex items-center p-4 text-xl font-bold text-gray-700 border-b mb-4 mt-4">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-18 w-56 mr-2" />
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col justify-between flex-1 p-3 text-base font-bold text-black">
            <!-- Menu Items -->
            <ul class="space-y-2">
                <li>
                    <a class="flex items-centerc bg-white px-4 py-2 rounded">
                        <span>Money Junction Iworld</span>
                    </a>
                </li>

                <li>
                    <a href=""
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('dashboard') ? 'bg-gray-200' : '' }}">
                        <img src="{{ asset('images/statisctics.png') }}" alt="Dashboard" class="w-6 h-6 mr-5" />
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href=""
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('sampleDevelopment.*') ? 'bg-gray-200' : '' }}">
                        <img src="{{ asset('images/acquisition.png') }}" alt="" class="w-6 h-6 mr-5" />
                        <span>Sales & Invoices</span>
                    </a>
                </li>

                <li>
                    <a href=""
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('productCatalog.*') ? 'bg-gray-200' : '' }}">
                        <img src="{{ asset('images/stock.png') }}" alt="" class="w-6 h-6 mr-5" />
                        <span>Reports</span>
                    </a>
                </li>


            </ul>

            <!-- Profile and Logout as Sidebar Buttons -->
            <ul class="space-y-2 border-t pt-4 mt-4">
                <li>
                    <a href=""
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-200 {{ request()->routeIs('profile.edit') ? 'bg-gray-200' : '' }}">
                        <img src="{{ asset('images/employee.png') }}" alt="Profile Icon" class="w-6 h-6 mr-5" />
                        <span>Profile</span>
                    </a>
                </li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-4 py-2 rounded hover:bg-gray-200 text-left">
                            <img src="{{ asset('images/close.png') }}" alt="Logout Icon" class="w-6 h-6 mr-5" />
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </aside>

</body>

</html>
