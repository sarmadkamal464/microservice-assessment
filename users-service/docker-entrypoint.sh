#!/bin/sh
set -e

# Wait for the database to be ready
wait-for-it database:5432 -- echo "Database is up"

export COMPOSER_ALLOW_SUPERUSER=1
composer clear-cache
composer install --no-interaction --prefer-dist

# Run migrations only if the migrations table does not exist
if ! php bin/console doctrine:migrations:status --no-interaction | grep -q "No migrations"; then
    echo "Running migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction --verbose || (echo "Migration failed" && exit 1)
else
    echo "No new migrations to run."
fi

# Create test database if it doesn't exist
php bin/console doctrine:database:create --env=test --if-not-exists

# Run migrations on the test database
php bin/console doctrine:migrations:migrate --no-interaction --env=test

# Start the PHP built-in server
exec php -S 0.0.0.0:8000 -t public