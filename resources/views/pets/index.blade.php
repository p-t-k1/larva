<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zwierzaki - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pets/index.js'])
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
                <select id="editCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Brak kategorii</option>
                    <option value="Dogs">Psy</option>
                    <option value="Cats">Koty</option>
                    <option value="Birds">Ptaki</option>
                    <option value="Fish">Ryby</option>
                    <option value="Rabbits">Króliki</option>
                </select>
            </div>

            <div>
                <label for="editPhotoUrls" class="block text-sm font-medium text-gray-700 mb-1">URL zdjęć (po przecinku)</label>
                <input type="text" id="editPhotoUrls" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/photo1.jpg, https://example.com/photo2.jpg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tagi</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="editTags" value="friendly" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Przyjazny</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="editTags" value="playful" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Zabawny</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="editTags" value="young" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Młody</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="editTags" value="trained" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Wyszkolony</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="editTags" value="vaccinated" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Zaszczepiony</span>
                    </label>
                </div>
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
</body>
</html>
