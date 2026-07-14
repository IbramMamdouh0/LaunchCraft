FROM php:8.4-cli

# Install system dependencies and MongoDB extension
RUN apt-get update && apt-get install -y \
    libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

WORKDIR /app