export const statusLabels = {
    'available': 'Dostępny',
    'pending': 'W trakcie adopcji',
    'sold': 'Adoptowany'
};

export const categoryLabels = {
    'Dogs': 'Psy',
    'Cats': 'Koty',
    'Birds': 'Ptaki',
    'Fish': 'Ryby',
    'Rabbits': 'Króliki'
};

export const tagLabels = {
    'friendly': 'Przyjazny',
    'playful': 'Zabawny',
    'young': 'Młody',
    'trained': 'Wyszkolony',
    'vaccinated': 'Zaszczepiony'
};

export function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}
