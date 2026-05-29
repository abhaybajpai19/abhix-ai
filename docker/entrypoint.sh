#!/bin/sh
set -e

echo "Creating storage symlink..."
php artisan storage:link --force

echo "Starting application..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
