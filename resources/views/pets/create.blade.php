<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dodaj zwierzę - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pets/create.js'])
</head>
<body class="bg-gray-50 min-h-screen">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('pets.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Powrót do listy
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Dodaj nowe zwierzę</h1>
        <p class="mt-2 text-gray-600">Wypełnij formularz, aby dodać nowe zwierzę do bazy</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form id="pet-form" class="flex flex-col gap-6">
            @csrf

            <div id="error-message" class="hidden text-center py-2 text-red-600"></div>

            <div id="success-message" class="hidden text-center py-2 text-green-600"></div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Imię *</label>
                <input type="text" id="name" name="name" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Kategoria *</label>
                <select id="category" name="category" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <option value="">Wybierz kategorię</option>
                    <option value="Dogs">Psy</option>
                    <option value="Cats">Koty</option>
                    <option value="Birds">Ptaki</option>
                    <option value="Fish">Ryby</option>
                    <option value="Rabbits">Króliki</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                <select id="status" name="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border">
                    <option value="available">Dostępny</option>
                    <option value="pending">W trakcie adopcji</option>
                    <option value="sold">Adoptowany</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tagi</label>
                <div class="space-y-2">
                    <label class="inline-flex items-center mr-4">
                        <input type="checkbox" name="tags[]" value="friendly" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Przyjazny</span>
                    </label>
                    <label class="inline-flex items-center mr-4">
                        <input type="checkbox" name="tags[]" value="playful" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Zabawny</span>
                    </label>
                    <label class="inline-flex items-center mr-4">
                        <input type="checkbox" name="tags[]" value="young" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Młody</span>
                    </label>
                    <label class="inline-flex items-center mr-4">
                        <input type="checkbox" name="tags[]" value="trained" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Wyszkolony</span>
                    </label>
                    <label class="inline-flex items-center mr-4">
                        <input type="checkbox" name="tags[]" value="vaccinated" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Zaszczepiony</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="photo_urls" class="block text-sm font-medium text-gray-700">
                    URL zdjęć <span class="text-gray-500 text-xs">(każdy URL w nowej linii)</span>
                </label>
                <textarea id="photo_urls" name="photo_urls" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"
                          placeholder="https://example.com/photo1.jpg&#10;https://example.com/photo2.jpg"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" id="submit-btn"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    Dodaj zwierzaka
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
