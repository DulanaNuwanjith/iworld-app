@extends('layouts.finance')

@section('content')
<div class="flex-1 overflow-y-auto bg-white p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Nearest Payments</h2>
        </div>

        <div class="overflow-x-auto max-h-[1200px] bg-white shadow rounded-lg">
            <table class="table-fixed w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-200">
                    <tr class="text-center text-xs text-gray-600 uppercase">
                        <th class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words">Order Number</th>
                        <th class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words">Buyer Name</th>
                        <th class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words">Buyer ID</th>
                        <th class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words">Buyer Address</th>
                        <th class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words">Phone No 1</th>
                        <th class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words">Phone No 2</th>
                        <th class="font-bold sticky top-0 bg-gray-200  px-4 py-3 w-32 text-xs text-gray-600  uppercase whitespace-normal break-words text-left">Next Expected Payment Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr class="odd:bg-white even:bg-gray-50 border-b border-gray-200  text-left">
                            <td class="text-center px-4 py-3 font-bold break-words"></td>
                            <td class="px-4 py-3 break-words"></td>
                            <td class="px-4 py-3 break-words"></td>
                            <td class="px-4 py-3 break-words"></td>
                            <td class="px-4 py-3 break-words"></td>
                            <td class="px-4 py-3 break-words"></td>
                            <td class="px-4 py-3 break-words">
                                
                            </td>
                        </tr>

                        <tr>
                            <td colspan="7" class="px-4 py-3 text-center text-gray-500">No finance orders found.</td>
                        </tr>

                </tbody>
            </table>
        </div>

        <div class="py-4 flex justify-center">
            
        </div>

</div>
@endsection
