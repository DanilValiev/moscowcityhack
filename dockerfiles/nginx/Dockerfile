FROM nginx:1.19

RUN echo "Europe/Moscow" > /etc/timezone \
    && ln -sf /usr/share/zoneinfo/Europe/Moscow /etc/localtime \
    && dpkg-reconfigure -f noninteractive tzdata

RUN mkdir -p /var/www/public/bundles/
COPY . /var/www/

COPY ./dockerfiles/nginx/*.conf /etc/nginx/
COPY ./dockerfiles/nginx/conf.d/*  /etc/nginx/conf.d/
#COPY --from=registry.bank131.ru/131/merchant-cabinet-api/php-fpm:latest /var/www/public/bundles /var/www/public/bundles