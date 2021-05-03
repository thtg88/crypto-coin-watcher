web: composer run-script optimize-laravel-cmd && vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:restart && php artisan queue:work --tries=3 --timeout=20 --max-jobs=1000 --max-time=3600
release: chmod -R 777 storage bootstrap/cache && php artisan migrate --force --seed
