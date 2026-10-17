# Image de production de l'API Symfony (utilisée par Render)
FROM php:8.4-apache

# Extensions PHP : PostgreSQL, intl, zip. (openssl et sodium sont déjà inclus dans l'image.)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libicu-dev libzip-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APP_ENV=prod \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PORT=10000

WORKDIR /var/www/html

# 1. Les dépendances d'abord : Docker les garde en cache tant que composer.lock ne change pas
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# 2. Puis le code de l'application
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative \
    && mkdir -p var/cache var/log config/jwt \
    && chown -R www-data:www-data var config/jwt

# 3. Apache écoute sur le port fourni par Render ($PORT) et sert le dossier public/
RUN sed -ri 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000
ENTRYPOINT ["entrypoint.sh"]