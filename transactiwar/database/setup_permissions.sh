#!/bin/bash
set -e

# This script runs automatically inside the db container on first boot.
# It uses the variables from your .env file ($DB_USER, $DB_PASS, $POSTGRES_DB, $POSTGRES_USER)

echo "Creating restricted app user: $DB_USER"

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL

    -- 1. Create the user dynamically
    CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';
    GRANT CONNECT ON DATABASE $POSTGRES_DB TO $DB_USER;
    GRANT USAGE ON SCHEMA public TO $DB_USER;

    -- 2. TABLE-SPECIFIC PERMISSIONS
    GRANT SELECT, INSERT, UPDATE ON users TO $DB_USER;
    GRANT SELECT, INSERT ON transactions TO $DB_USER;
    GRANT SELECT, INSERT ON activity_logs TO $DB_USER;

    -- 3. SEQUENCE PERMISSIONS
    GRANT USAGE, SELECT ON SEQUENCE 
        users_id_seq, 
        transactions_id_seq, 
        activity_logs_id_seq 
    TO $DB_USER;

EOSQL

echo "Permissions granted successfully for $DB_USER"