# Larva Laravel Project

<img src="screenshot.jpg" alt="Screenshot" style="max-width: 600px">

## Stack technologiczny

- **Backend:** Laravel 12.x
- **Frontend:** Blade templates + Tailwind CSS
- **Baza danych:** MySQL (via Docker)
- **Środowisko:** Laravel Sail (Docker)

## API Endpoints

- GET /api/pets/findByStatus?status[]=available
- POST /api/pet
- PUT /api/pet
- DELETE /api/pet/{id}
- GET /api/pet/config

## Uruchomienie projektu przy użyciu Laravel Sail

```bash
# Skopiuj plik .env
cp .env.example .env

# Zainstaluj zależności PHP (pierwsza instalacja bez Sail)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs

# Uruchom kontenery Docker w tle
./vendor/bin/sail up -d

# Wygeneruj klucz aplikacji
./vendor/bin/sail artisan key:generate

# Uruchom migracje bazy danych
./vendor/bin/sail artisan migrate

# Zainstaluj zależności Node.js i zbuduj assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Projekt będzie dostępny pod adresem: `http://localhost`

## Uruchomienie testów

```bash
# Uruchom wszystkie testy
./vendor/bin/sail artisan test

# Uruchom testy z pokryciem kodu
./vendor/bin/sail artisan test --coverage
```
