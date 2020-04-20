cp .env.example .env

echo -e "### Running Composer install ###";
composer install

echo -e "## Generating laravel app key ###";
php artisan key:generate

echo -e "### Linking storage to public folder ###";
php artisan storage:link

echo -e "### Running migrations ###";
php artisan migrate

echo -e "### Running npm install ###";
npm install

echo -e "Building app.js and main.css files";
npm run dev

echo -e "### Ready for action! ###";
