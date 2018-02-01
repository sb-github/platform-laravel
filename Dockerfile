FROM php:7-apache
RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
        zlib1g-dev \
        mlocate \
        mysql-server \
        && apt-get clean \
        && docker-php-ext-install zip mysqli pdo_mysql\
        && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN useradd -m -u 1000 artisanw
RUN curl -s http://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer
ENV PATH=/var/www/html/vendor/bin:$PATH
ONBUILD COPY composer.json composer.lock /var/www/html/
ONBUILD COPY database /var/www/html/database/
ONBUILD RUN composer install --prefer-dist --optimize-autoloader --no-dev --profile -vvv
ONBUILD COPY . /var/www/html
ONBUILD RUN rm -Rf tests/
ONBUILD RUN chown -R www-data:www-data /var/www/html/storage/
ONBUILD VOLUME /var/www/html/storage

WORKDIR /var/www/html
ADD . .

RUN rm /etc/apache2/sites-available/000-default.conf
COPY 000-default.conf /etc/apache2/sites-available/

COPY .env.production .env

RUN composer install \
    && chmod -R 777 /var/www/html
EXPOSE 8000

CMD bash service apache2 start ; bash
