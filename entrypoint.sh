#!/bin/bash
set -e

# Run database migrations if MySQL is available
if [ -n "$MYSQLHOST" ]; then
    echo "Running database migrations..."
    mysql \
        -h "$MYSQLHOST" \
        -P "${MYSQLPORT:-3306}" \
        -u "$MYSQLUSER" \
        -p"$MYSQLPASSWORD" \
        "$MYSQLDATABASE" < /var/www/html/database.sql && echo "Migrations done." || echo "Migration skipped (may already exist)."
fi

# Start Apache
exec apache2-foreground
