web: vendor/bin/heroku-php-nginx -C nginx.conf public/
worker: php artisan queue:work --verbose --tries=3 --timeout=600