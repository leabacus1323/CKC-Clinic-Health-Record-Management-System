# Use the official PHP image as the base image
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Install Composer
COPY --from=composer:2.7.6 /usr/bin/composer /usr/bin/composer

# Copy application contents and set permissions
COPY --chown=www-data:www-data . .

# Install PHP extensions (if any additional required)
# Note: mysqli and pdo extensions are already installed above
# RUN docker-php-ext-install <additional-extensions>

# Install Node dependencies and build assets
RUN npm install \
    && npm run build

# Change to non-root user
USER www-data

# Change access permission to current user
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000

CMD ["php-fpm"]
