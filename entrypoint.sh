#!/bin/bash
set -e

# Run database migrations if MySQL is available
if [ -n "$MYSQLHOST" ]; then
    echo "Running database migrations..."
    sed -e '/^CREATE DATABASE /Id' -e '/^USE /Id' /var/www/html/database.sql | mysql \
        -h "$MYSQLHOST" \
        -P "${MYSQLPORT:-3306}" \
        -u "$MYSQLUSER" \
        -p"$MYSQLPASSWORD" \
        "$MYSQLDATABASE" && echo "Migrations done." || echo "Migration skipped (may already exist)."
fi

# Start Apache
exec apache2-foreground
