composer install

npm install 

cp .env.example .env

php artisan migrate

php artisan db:seed

php artisan storage:link

php artisan db:seed --class=PermissionTableSeeder

php artisan db:seed --class=CreateAdminUserSeeder

#install redis 
composer require predis/predis
#after set up env run:
php artisan config:clear
php artisan cache:clear
#create channel
php artisan make:event StatusUpdated

