FROM php:8.2-cli

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app
# Copy application code
COPY . /app/

# Copy composer config files and install dependencies
COPY composer.json composer.lock* /app/
RUN composer install --no-interaction --optimize-autoloader

# Command to keep a container always running
CMD ["tail", "-f", "/dev/null"]
