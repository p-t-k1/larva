# Larva Laravel Project

## Uruchomienie projektu przy użyciu Laravel Sail

```bash
# Skopiuj plik .env
cp .env.example .env

# Uruchom kontenery Docker
./vendor/bin/sail up

# W osobnym oknie terminala zainstaluj zależności i wygeneruj klucz
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

Projekt będzie dostępny pod adresem: `http://localhost`
