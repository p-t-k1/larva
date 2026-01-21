let petConfig = null;

export async function loadPetConfig() {
    if (petConfig) {
        return petConfig;
    }

    try {
        const response = await fetch('/api/config/pet-options');
        if (!response.ok) {
            throw new Error('Nie udało się załadować konfiguracji zwierzaków');
        }
        petConfig = await response.json();
        return petConfig;
    } catch (error) {
        console.error('Error loading pet config:', error);
        return {
            statuses: [],
            categories: [],
            tags: []
        };
    }
}

export function getStatusLabels() {
    if (!petConfig) return {};
    return Object.fromEntries(
        petConfig.statuses.map(s => [s.value, s.label])
    );
}

export function getCategoryLabels() {
    if (!petConfig) return {};
    return Object.fromEntries(
        petConfig.categories.map(c => [c.value, c.label])
    );
}

export function getTagLabels() {
    if (!petConfig) return {};
    return Object.fromEntries(
        petConfig.tags.map(t => [t.value, t.label])
    );
}

export function getDefaultStatus() {
    if (!petConfig || !petConfig.statuses.length) return 'available';
    return petConfig.statuses[0].value;
}
