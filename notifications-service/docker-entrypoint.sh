#!/bin/sh
set -e

export COMPOSER_ALLOW_SUPERUSER=1
composer clear-cache
composer install --no-interaction --prefer-dist

# Wait for RabbitMQ to be ready
echo "Waiting for RabbitMQ to be up..."
until nc -z rabbitmq 5672; do
  echo "RabbitMQ is not up yet. Waiting..."
  sleep 2
done
echo "RabbitMQ is up."

# Start the Messenger Consumer in the background
echo "Starting Messenger Consumer..."
php bin/console messenger:consume amqp --no-interaction --verbose &

# Start the PHP built-in server
echo "Starting PHP server..."
exec php -S 0.0.0.0:8002 -t public
