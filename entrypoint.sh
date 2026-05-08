#!/bin/bash

PORT="${PORT:-10000}"

echo "Starting app on port $PORT..."

# Update Apache port configuration
sed -i "s/Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Apache configured for port $PORT"

# Run database migrations if MySQL is available
DB_HOST="${MYSQLHOST:-${MYSQL_HOST:-${DB_HOST:-}}}"
DB_PORT="${MYSQLPORT:-${MYSQL_PORT:-${DB_PORT:-3306}}}"
DB_USER="${MYSQLUSER:-${MYSQL_USER:-${DB_USER:-}}}"
DB_PASSWORD="${MYSQLPASSWORD:-${MYSQL_PASSWORD:-${DB_PASS:-}}}"
DB_DATABASE="${MYSQLDATABASE:-${MYSQL_DATABASE:-${DB_NAME:-}}}"

if [ -n "$DB_HOST" ] && [ -n "$DB_DATABASE" ]; then
    echo "Running database migrations against $DB_HOST:$DB_PORT/$DB_DATABASE ..."
    sed -e '/^CREATE DATABASE /Id' -e '/^USE /Id' /var/www/html/database.sql | mysql \
        -h "$DB_HOST" \
        -P "$DB_PORT" \
        -u "$DB_USER" \
        -p"$DB_PASSWORD" \
        "$DB_DATABASE" \
        && echo "Migrations done." \
        || echo "Migration skipped (may already exist or DB not ready)."
else
    echo "No DB_HOST set — skipping migrations."
fi

echo "Starting Apache..."
exec apache2-foreground
