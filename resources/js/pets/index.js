import { statusLabels, categoryLabels, tagLabels, getCsrfToken } from './shared.js';

export function loadPets() {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status') || 'available';
    const container = document.getElementById('pets-container');

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

            if (pets.length === 0) {
                container.innerHTML = '<div class="text-center col-span-full py-12 text-gray-500">Brak dostępnych zwierząt</div>';
                return;
            }

            container.innerHTML = pets.map(pet => `
                <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    ${pet.photo_urls && pet.photo_urls.length > 0
                ? `<img src="${pet.photo_urls[0]}" alt="${pet.name}" class="w-full h-48 bg-gray-100 object-cover">`
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

export async function deletePet(petId, petName) {
    if (!confirm(`Czy na pewno chcesz usunąć zwierzaka "${petName}"?\n\nTa operacja jest nieodwracalna.`)) {
        return;
    }

    try {
        const response = await fetch(`/api/pet/${petId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
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

export async function openEditModal(petId) {
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

        document.querySelectorAll('input[name="editTags"]').forEach(cb => cb.checked = false);

        if (pet.tags && pet.tags.length > 0) {
            pet.tags.forEach(tag => {
                const checkbox = document.querySelector(`input[name="editTags"][value="${tag.name}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }

        document.getElementById('editModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading pet data:', error);
        alert('Nie udało się załadować danych zwierzaka');
    }
}

export function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editForm').reset();
    document.querySelectorAll('input[name="editTags"]').forEach(cb => cb.checked = false);
}

export function setupEditForm() {
    document.getElementById('editForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const petId = parseInt(document.getElementById('editPetId').value);
        const name = document.getElementById('editName').value.trim();
        const status = document.getElementById('editStatus').value;
        const categoryName = document.getElementById('editCategory').value;
        const photoUrlsStr = document.getElementById('editPhotoUrls').value.trim();

        const selectedTags = Array.from(document.querySelectorAll('input[name="editTags"]:checked'))
            .map(cb => cb.value);

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

        if (selectedTags.length > 0) {
            petData.tags = selectedTags.map((tag, index) => ({
                id: index,
                name: tag
            }));
        }

        try {
            const response = await fetch('/api/pet', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
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
}

window.loadPets = loadPets;
window.deletePet = deletePet;
window.openEditModal = openEditModal;
window.closeEditModal = closeEditModal;

document.addEventListener('DOMContentLoaded', () => {
    setupEditForm();
    loadPets();
});
