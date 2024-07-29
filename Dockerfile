# Utiliser une image de base avec PHP 8.2 et Composer installés
FROM php:8.2-fpm

# Définir le répertoire de travail
WORKDIR /var/www

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nginx

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
COPY . .

# Copier la configuration Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Afficher la version de PHP et de Composer pour le diagnostic
RUN php -v && composer -V

# Installer les dépendances PHP avec des options de débogage
RUN composer install --no-dev --optimize-autoloader --verbose

# Copier les permissions appropriées
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

# Exposer le port 80 pour Nginx
EXPOSE 80

# Ajouter un script pour exécuter les migrations
COPY run-migrations.sh /usr/local/bin/run-migrations.sh
RUN chmod +x /usr/local/bin/run-migrations.sh

# Commande pour démarrer PHP-FPM et Nginx
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;' && /usr/local/bin/run-migrations.sh"]
