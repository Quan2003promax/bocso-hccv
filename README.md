composer install

npm install 

cp .env.example .env

php artisan migrate

php artisan db:seed

php artisan storage:link

php artisan db:seed --class=PermissionTableSeeder

php artisan db:seed --class=CreateAdminUserSeeder
