# Bước 1: Dùng PHP image với PHP-FPM
FROM php:8.2-fpm

# Bước 2: Cài đặt các thư viện cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    libzip-dev \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Bước 3: Cài đặt các extension PHP cần thiết cho Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Bước 4: Cài đặt Composer (trình quản lý phụ thuộc PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Bước 5: Cấu hình thư mục ứng dụng
WORKDIR /var/www/html

# Bước 6: Sao chép mã nguồn Laravel vào container
COPY . .

# Bước 7: Đặt quyền cho thư mục để tránh lỗi Git và các vấn đề quyền
RUN chown -R www-data:www-data /var/www/html

# Bước 8: Cài đặt các phụ thuộc PHP của Laravel bằng Composer
RUN composer install --no-dev --optimize-autoloader

# Bước 9: Chạy migration và seeding dữ liệu (Lưu ý: đảm bảo DB container đang chạy)
RUN php artisan migrate --force && php artisan db:seed --force

# Bước 10: Đặt quyền cho các thư mục cần thiết (storage, cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Bước 11: Mở cổng 9000 cho PHP-FPM
EXPOSE 9000

# Bước 12: Khởi động PHP-FPM (hoặc bạn có thể dùng Nginx để serve ứng dụng)
CMD ["php-fpm"]
