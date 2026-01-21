<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Pets - {{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="mb-8 flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Zwierzęta</h1>
                    <p class="mt-2 text-gray-600">Przeglądaj nasze dostępne zwierzęta</p>
                </div>
                <a href="{{ route('pets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Dodaj zwierzę
                </a>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-800">Dostępne zwierzęta</h2>
                        <div class="flex gap-2">
                            <a href="?status=available" class="inline-flex items-center px-3 py-1.5 border border-green-500 text-green-600 rounded-full text-sm font-medium hover:bg-green-50">
                                Dostępny
                            </a>
                            <a href="?status=pending" class="inline-flex items-center px-3 py-1.5 border border-yellow-500 text-yellow-600 rounded-full text-sm font-medium hover:bg-yellow-50">
                                W trakcie adopcji
                            </a>
                            <a href="?status=sold" class="inline-flex items-center px-3 py-1.5 border border-red-500 text-red-600 rounded-full text-sm font-medium hover:bg-red-50">
                                Adoptowany
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6" id="pets-container">
                    <!-- Pets will be loaded here dynamically -->
                    <div class="text-center col-span-full py-12 text-gray-500">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                        <p class="mt-2">Ładowanie zwierząt...</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const statusLabels = {
                'available': 'Dostępny',
                'pending': 'W trakcie adopcji',
                'sold': 'Adoptowany'
            };

            const categoryLabels = {
                'Dogs': 'Psy',
                'Cats': 'Koty',
                'Birds': 'Ptaki',
                'Fish': 'Ryby',
                'Rabbits': 'Króliki'
            };

            const tagLabels = {
                'friendly': 'Przyjazny',
                'playful': 'Zabawny',
                'young': 'Młody',
                'trained': 'Wyszkolony',
                'vaccinated': 'Zaszczepiony'
            };

            function loadPets() {
                const params = new URLSearchParams(window.location.search);
                const status = params.get('status') || 'available';
                const container = document.getElementById('pets-container');

                // Show loading
                container.innerHTML = '<div class="text-center col-span-full py-12 text-gray-500"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div><p class="mt-2">Ładowanie zwierząt...</p></div>';

                fetch(`/api/pets/findByStatus?status[]=${status}`)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Wystąpił błąd podczas pobierania danych');
                            });
                        }
                        return response.json();
                    })
                    .then(response => {
                        const pets = response.data;
                        const meta = response.meta;

                        if (pets.length === 0) {
                            container.innerHTML = '<div class="text-center col-span-full py-12 text-gray-500">Brak dostępnych zwierząt</div>';
                            return;
                        }

                        container.innerHTML = pets.map(pet => `
                            <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                ${pet.photo_urls && pet.photo_urls.length > 0
                                    ? `<img src="${pet.photo_urls[0]}" alt="${pet.name}" class="w-full h-48 object-cover">`
                                    : `<div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">Brak zdjęcia</div>`
                                }
                                <div class="p-4">
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-semibold text-gray-900">${pet.name || 'Zwierzę bez nazwy'}</h3>
                                        <button onclick="deletePet(${pet.id}, '${pet.name}')" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Usuń zwierzaka">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${
                                            pet.status === 'available'
                                                ? 'bg-green-100 text-green-800'
                                                : pet.status === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-red-100 text-red-800'
                                        }">
                                            ${statusLabels[pet.status] || 'Nieznany'}
                                        </span>
                                        ${pet.category
                                            ? `<span class="text-sm text-gray-500">${categoryLabels[pet.category.name] || pet.category.name}</span>`
                                            : ''
                                        }
                                    </div>
                                    ${pet.tags && pet.tags.length > 0
                                        ? `<div class="mt-3 flex flex-wrap gap-1">
                                            ${pet.tags.map(tag => `<span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs">${tagLabels[tag.name] || tag.name}</span>`).join('')}
                                           </div>`
                                        : ''
                                    }
                                </div>
                            </div>
                        `).join('');
                    })
                    .catch(error => {
                        console.error('Error fetching pets:', error);
                        container.innerHTML = `<div class="text-center col-span-full py-12 text-red-600">${error.message}</div>`;
                    });
            }

            async function deletePet(petId, petName) {
                if (!confirm(`Czy na pewno chcesz usunąć zwierzaka "${petName}"?\n\nTa operacja jest nieodwracalna.`)) {
                    return;
                }

                try {
                    const response = await fetch(`/api/pet/${petId}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        }
                    });

                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.message || 'Wystąpił błąd podczas usuwania zwierzaka');
                    }

                    loadPets();
                } catch (error) {
                    console.error('Error deleting pet:', error);
                    alert(`Błąd: ${error.message}`);
                }
            }

            // Load pets on page load
            loadPets();
        </script>
    </body>
</html>
