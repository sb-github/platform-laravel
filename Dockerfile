FROM php:7-apache
RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        git \
        zlib1g-dev \
        vim \
        mlocate \
        mysql-server \
        && apt-get clean \
        && docker-php-ext-install zip mysqli pdo_mysql\
        && rm -rf /var/lib/apt/lists/* \
        && rm /etc/apache2/sites-available/000-default.conf

# Enable rewrite module
RUN a2enmod rewrite

RUN useradd -m -u 1000 artisanw
    
WORKDIR /var/www/html

# Download and Install Composer
RUN curl -s http://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Add vendor binaries to PATH
ENV PATH=/var/www/html/vendor/bin:$PATH

ONBUILD COPY composer.json composer.lock /var/www/html/
ONBUILD COPY database /var/www/html/database/

ONBUILD RUN composer install --prefer-dist --optimize-autoloader --no-dev --profile -vvv
ONBUILD COPY . /var/www/html
ONBUILD RUN rm -Rf tests/

# Configure directory permissions for the web server
ONBUILD RUN chown -R www-data:www-data /var/www/html/storage/

# Configure data volume
ONBUILD VOLUME /var/www/html/storage

COPY 000-default.conf /etc/apache2/sites-available/

RUN git clone -b develop https://github.com/sb-github/platform-laravel.git /var/www/html 
RUN composer install \
    && chmod -R 777 /var/www/html 

COPY .env /var/www/html
CMD bash service apache2 start ; bash 
