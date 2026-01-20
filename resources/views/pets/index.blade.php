<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pets - {{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Pets</h1>
                <p class="mt-2 text-gray-600">Browse our available pets</p>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Available Pets</h2>
                        <div class="flex gap-2">
                            <a href="#available" class="inline-flex items-center px-3 py-1.5 border border-green-500 text-green-600 rounded-full text-sm font-medium hover:bg-green-50">
                                Available
                            </a>
                            <a href="#pending" class="inline-flex items-center px-3 py-1.5 border border-yellow-500 text-yellow-600 rounded-full text-sm font-medium hover:bg-yellow-50">
                                Pending
                            </a>
                            <a href="#sold" class="inline-flex items-center px-3 py-1.5 border border-red-500 text-red-600 rounded-full text-sm font-medium hover:bg-red-50">
                                Sold
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6" id="pets-container">
                    <!-- Pets will be loaded here dynamically -->
                    <div class="text-center col-span-full py-12 text-gray-500">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                        <p class="mt-2">Loading pets...</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Simple script to demonstrate fetching pets
            fetch('/api/pets/findByStatus?status[]=available')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('pets-container');
                    if (data.length === 0) {
                        container.innerHTML = '<div class="text-center col-span-full py-12 text-gray-500">No pets available</div>';
                        return;
                    }

                    container.innerHTML = data.map(pet => `
                        <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            ${pet.photo_urls && pet.photo_urls.length > 0
                                ? `<img src="${pet.photo_urls[0]}" alt="${pet.name}" class="w-full h-48 object-cover">`
                                : `<div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">No photo</div>`
                            }
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900">${pet.name || 'Unnamed Pet'}</h3>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${
                                        pet.status === 'available'
                                            ? 'bg-green-100 text-green-800'
                                            : pet.status === 'pending'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : 'bg-red-100 text-red-800'
                                    }">
                                        ${pet.status || 'unknown'}
                                    </span>
                                    ${pet.category
                                        ? `<span class="text-sm text-gray-500">${pet.category.name}</span>`
                                        : ''
                                    }
                                </div>
                                ${pet.tags && pet.tags.length > 0
                                    ? `<div class="mt-3 flex flex-wrap gap-1">
                                        ${pet.tags.map(tag => `<span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs">${tag.name}</span>`).join('')}
                                       </div>`
                                    : ''
                                }
                            </div>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error fetching pets:', error);
                    const container = document.getElementById('pets-container');
                    container.innerHTML = '<div class="text-center col-span-full py-12 text-red-500">Failed to load pets. Please try again later.</div>';
                });
        </script>
    </body>
</html>
