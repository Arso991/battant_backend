#!/bin/sh

# Attendre que la base de données soit prête (ajuster si nécessaire)
echo "Waiting for database..."
sleep 10

# Exécuter les migrations
echo "Running migrations..."
php artisan migrate --force

# Exécuter les seeders si nécessaire
# php artisan db:seed --force

# Exécuter l'application Laravel
exec "$@"
