<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<div class="flex h-full w-full">
    @extends('layouts.reports')

    @section('content')
        <div class="flex h-full w-full bg-white">
            <div class="flex-1 p-8">
                <!-- Header -->
                <div class="flex justify-center items-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Iworld Reports Generator</h2>
                </div>

                <!-- Flex container for all forms -->
                <div class="flex flex-wrap justify-center items-stretch gap-6 max-w-7xl mx-auto pb-32">



                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdownDispatch(button, type) {
            const dropdownMenu = button.nextElementSibling;
            document.querySelectorAll('.dropdown-menu-' + type).forEach(menu => {
                if (menu !== dropdownMenu) menu.classList.add('hidden');
            });
            dropdownMenu.classList.toggle('hidden');
        }

        function selectDropdownOptionDispatch(button, selectedValue, type) {
            const dropdown = button.closest('.relative');
            dropdown.querySelector('.selected-' + type).innerText = selectedValue;
            dropdown.querySelector('.input-' + type).value = selectedValue;
            dropdown.querySelector('.dropdown-menu-' + type).classList.add('hidden');
        }

        function filterDropdownOptionsDispatch(input) {
            const filter = input.value.toLowerCase();
            const options = input.closest('[class^="dropdown-menu-"]').querySelectorAll('.dropdown-option');
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(filter) ? 'block' : 'none';
            });
        }

        document.addEventListener('click', function(event) {
            document.querySelectorAll('[class^="dropdown-menu-"]').forEach(menu => {
                if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
    <script>
        function toggleDropdown(button, type) {
            const menu = button.nextElementSibling;
            document.querySelectorAll('.dropdown-menu-' + type).forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
        }

        function selectDropdownOption(button, type) {
            const dropdown = button.closest('.relative');
            dropdown.querySelector('.selected-' + type).innerText = button.innerText;
            dropdown.querySelector('.input-' + type).value = button.innerText;
            dropdown.querySelector('.dropdown-menu-' + type).classList.add('hidden');
        }

        function filterDropdown(input, type) {
            const filter = input.value.toLowerCase();
            const options = input.closest('.dropdown-menu-' + type).querySelectorAll('.dropdown-option');
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(filter) ? 'block' : 'none';
            });
        }

        document.addEventListener('click', function(event) {
            document.querySelectorAll('[class^="dropdown-menu-"]').forEach(menu => {
                if (!menu.contains(event.target) && !menu.previousElementSibling.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
