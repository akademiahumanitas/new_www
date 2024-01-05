FROM wordpress:6.4.2-php8.1-fpm-alpine

COPY wp-content/ /var/www/html/wp-content/

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
