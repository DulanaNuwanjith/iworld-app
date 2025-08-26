<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stretchtec Dashboard</title>
</head>
<div class="flex h-full w-full font-sans bg-white">
    @include('layouts.side-bar')

    <div class="flex-1 overflow-y-auto p-6 md:p-10">
        <div class="flex justify-between items-start md:items-center mb-5">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-5">Iworld Management Dashboard</h1>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10">
                <h2 class=" text-lg font-semibold text-gray-900 dark:text-white text-center">
                    Welcome, {{ Auth::user()->name }}!
                </h2>
            </div>
        </div>

        
    </div>
</div>


