web: composer run-script optimize-laravel-cmd && vendor/bin/heroku-php-apache2 public/
worker: php artisan horizon
release: chmod -R 777 storage bootstrap/cache && php artisan migrate:fresh --force --seed && php artisan coin-prices:fetch-new
