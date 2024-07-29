# Utiliser une image de base avec PHP et Composer installés
FROM php:8.1-fpm

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
    curl

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
COPY . .

# Afficher la version de PHP et de Composer pour le diagnostic
RUN php -v && composer -V

# Installer les dépendances PHP avec des options de débogage
RUN composer install --no-dev --optimize-autoloader --verbose

# Copier les permissions appropriées
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

# Exposer le port 9000 et démarrer PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
