# Utilisation de PHP 8.3 avec Apache
FROM php:8.3-apache

# Installation des dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo_mysql mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activation du module Apache mod_rewrite
RUN a2enmod rewrite

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copie uniquement composer.json et composer.lock d'abord (optimisation du cache)
COPY composer.json composer.lock ./

# Installation des dépendances Composer (sans exécuter le reste du projet)
RUN composer install --no-dev --optimize-autoloader

# Ensuite, on copie tout le reste du projet
COPY . .

# Exposition du port Apache
EXPOSE 80

# Commande au démarrage du conteneur
CMD ["sh", "-c", "composer install && composer ss"]
