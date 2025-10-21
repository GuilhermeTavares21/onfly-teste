#!/bin/bash
set -e

composer install --no-interaction

# Gerar APP_KEY se não existir
if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
  php artisan key:generate
fi

# Cachear as configs (para garantir leitura do .env)
php artisan config:cache

# Esperar o banco de dados estar pronto
echo "⏳ Aguardando o banco de dados ficar pronto..."
until php artisan tinker --execute="DB::connection()->getPdo()" 2>/dev/null; do
  sleep 2
done

# Rodar migrations e seeders
php artisan migrate --force
php artisan db:seed --force

# Iniciar o servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000
