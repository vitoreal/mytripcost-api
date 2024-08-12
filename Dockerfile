FROM php:8.3.10-fpm as php

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nano \
    sudo \
    wget

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets pdo mysqli intl fileinfo

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copiando todos os arquivos do sistema para a imagem - Tem que ficar abaixo do WORKDIR
COPY . .

ENV PORT=8000
ENTRYPOINT [ "docker/entrypoint.sh" ]

# ==============================================================================
#  node
FROM node:18-alpine as node

WORKDIR /var/www
COPY . .

RUN npm install --global cross-env
RUN npm install

VOLUME /var/www/node_modules