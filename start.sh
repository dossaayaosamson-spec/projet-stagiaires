#!/bin/sh
set -eu

# La démo repart d'une base propre à chaque démarrage.
if [ -f database/database.sqlite ]; then
    rm database/database.sqlite
fi
touch database/database.sqlite

php artisan migrate --force
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"