# Larva Laravel Project

**Wersja Laravela: 12.x**

## Uruchomienie projektu przy użyciu Laravel Sail

```bash
# Skopiuj plik .env
cp .env.example .env

# Uruchom kontenery Docker
./vendor/bin/sail up

# W osobnym oknie terminala zainstaluj zależności i wygeneruj klucz
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate

# Uruchom migracje bazy danych (obowiązkowe do poprawnego działania)
./vendor/bin/sail artisan migrate

# Zainstaluj zależności zależności Node.js i zbuduj assets Tailwind
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Projekt będzie dostępny pod adresem: `http://localhost`
