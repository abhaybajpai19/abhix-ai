FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpq-dev \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --optimize-autoloader

RUN npm install
RUN npm install tailwindcss @tailwindcss/vite --save-dev
RUN npm run build

RUN php artisan config:clear
RUN php artisan cache:clear

EXPOSE $PORT

CMD php artisan serve --host=0.0.0.0 --port=$PORT
