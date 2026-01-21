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

        <!-- Edit Modal -->
        <div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.7);">
            <div class="overflow-y-auto p-6 shadow-2xl rounded-lg bg-white" style="width: 50%; max-height: 90vh;">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Edytuj zwierzaka</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="editForm" class="flex flex-col gap-2">
                    <input type="hidden" id="editPetId">

                    <div>
                        <label for="editName" class="block text-sm font-medium text-gray-700 mb-1">Nazwa zwierzaka *</label>
                        <input type="text" id="editName" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select id="editStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="available">Dostępny</option>
                            <option value="pending">W trakcie adopcji</option>
                            <option value="sold">Adoptowany</option>
                        </select>
                    </div>

                    <div>
                        <label for="editCategory" class="block text-sm font-medium text-gray-700 mb-1">Kategoria</label>
                        <input type="text" id="editCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="np. Dogs, Cats, Birds">
                    </div>

                    <div>
                        <label for="editPhotoUrls" class="block text-sm font-medium text-gray-700 mb-1">URL zdjęć (po przecinku)</label>
                        <input type="text" id="editPhotoUrls" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/photo1.jpg, https://example.com/photo2.jpg">
                    </div>

                    <div>
                        <label for="editTags" class="block text-sm font-medium text-gray-700 mb-1">Tagi (po przecinku)</label>
                        <input type="text" id="editTags" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="friendly, playful, young">
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Anuluj
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                            Zapisz zmiany
                        </button>
                    </div>
                </form>
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
                                        <div class="flex gap-1">
                                            <button onclick="openEditModal(${pet.id})" class="text-blue-500 hover:text-blue-700 transition-colors p-1" title="Edytuj zwierzaka">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button onclick="deletePet(${pet.id}, '${pet.name}')" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Usuń zwierzaka">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
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

            async function openEditModal(petId) {
                try {
                    const params = new URLSearchParams(window.location.search);
                    const status = params.get('status') || 'available';

                    const response = await fetch(`/api/pets/findByStatus?status[]=${status}`);
                    if (!response.ok) throw new Error('Failed to fetch pet data');

                    const data = await response.json();
                    const pet = data.data.find(p => p.id === petId);

                    if (!pet) throw new Error('Pet not found');

                    document.getElementById('editPetId').value = pet.id;
                    document.getElementById('editName').value = pet.name || '';
                    document.getElementById('editStatus').value = pet.status || 'available';
                    document.getElementById('editCategory').value = pet.category ? pet.category.name : '';
                    document.getElementById('editPhotoUrls').value = pet.photo_urls ? pet.photo_urls.join(', ') : '';
                    document.getElementById('editTags').value = pet.tags ? pet.tags.map(t => t.name).join(', ') : '';

                    document.getElementById('editModal').classList.remove('hidden');
                } catch (error) {
                    console.error('Error loading pet data:', error);
                    alert('Nie udało się załadować danych zwierzaka');
                }
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
                document.getElementById('editForm').reset();
            }

            document.getElementById('editForm').addEventListener('submit', async (e) => {
                e.preventDefault();

                const petId = parseInt(document.getElementById('editPetId').value);
                const name = document.getElementById('editName').value.trim();
                const status = document.getElementById('editStatus').value;
                const categoryName = document.getElementById('editCategory').value.trim();
                const photoUrlsStr = document.getElementById('editPhotoUrls').value.trim();
                const tagsStr = document.getElementById('editTags').value.trim();

                const petData = {
                    id: petId,
                    name: name,
                    status: status
                };

                if (categoryName) {
                    petData.category = {
                        id: 0,
                        name: categoryName
                    };
                }

                if (photoUrlsStr) {
                    petData.photoUrls = photoUrlsStr.split(',').map(url => url.trim()).filter(url => url);
                }

                if (tagsStr) {
                    petData.tags = tagsStr.split(',').map((tag, index) => ({
                        id: index,
                        name: tag.trim()
                    })).filter(tag => tag.name);
                }

                try {
                    const response = await fetch('/api/pet', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify(petData)
                    });

                    if (!response.ok) {
                        const error = await response.json();
                        throw new Error(error.message || 'Wystąpił błąd podczas aktualizacji zwierzaka');
                    }

                    closeEditModal();
                    loadPets();
                } catch (error) {
                    console.error('Error updating pet:', error);
                    alert(`Błąd: ${error.message}`);
                }
            });

            // Load pets on page load
            loadPets();
        </script>
    </body>
</html>
