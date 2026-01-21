import { getCsrfToken } from './shared.js';
import { loadPetConfig } from './config.js';

async function setupCreateForm() {
    await loadPetConfig();
    const form = document.getElementById('pet-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span> Dodawanie...';

        errorMessage.classList.add('hidden');
        successMessage.classList.add('hidden');

        const formData = new FormData(form);
        const tags = formData.getAll('tags[]');
        const photoUrlsText = formData.get('photo_urls');
        const photoUrls = photoUrlsText ? photoUrlsText.split('\n').filter(url => url.trim()) : [];

        const data = {
            name: formData.get('name'),
            category: { id: 0, name: formData.get('category') },
            status: formData.get('status'),
            tags: tags.map(tag => ({ id: 0, name: tag })),
            photoUrls: photoUrls
        };

        try {
            const response = await fetch('/api/pet', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                successMessage.textContent = 'Zwierzę zostało pomyślnie dodane! Przekierowywanie...';
                successMessage.classList.remove('hidden');
                
                setTimeout(() => {
                    window.location.href = `/?status=${data.status}`;
                }, 1000);
            } else {
                throw new Error(result.message || 'Wystąpił błąd podczas dodawania zwierzęcia');
            }
        } catch (error) {
            errorMessage.textContent = error.message;
            errorMessage.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Dodaj zwierzaka';
        }
    });
}

document.addEventListener('DOMContentLoaded', setupCreateForm);
