web: composer run-script optimize-laravel-cmd && vendor/bin/heroku-php-apache2 public/
scheduler: php artisan schedule:work
worker: php artisan horizon
release: chmod -R 777 storage bootstrap/cache && php artisan migrate --force --seed
