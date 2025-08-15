#!/bin/bash
set -e

echo "Starting Soccer AI Laravel application setup..."

# Generate application key if it doesn't exist
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate
else
    echo "Application key already exists."
fi

# Wait for database to be ready
echo "Waiting for database to be ready..."
until timeout 1 bash -c "</dev/tcp/db/5432" 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 2
done

echo "Database is ready!"

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Install Node dependencies if node_modules doesn't exist or is empty
if [ ! -d "node_modules" ] || [ -z "$(ls -A node_modules)" ]; then
    echo "Installing Node dependencies..."
    npm install
fi

# Start PHP-FPM in the background
echo "Starting PHP-FPM..."
php-fpm &

# Start Vite dev server in the background for development
echo "Starting Vite development server..."
npm run dev -- --host 0.0.0.0 &

# Run any additional setup commands if needed
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

echo "Soccer AI Laravel application setup complete!"
echo "Vite dev server running on http://0.0.0.0:5173"
echo "Application available at http://localhost:8000"

# Keep the container running by waiting for background processes
wait 