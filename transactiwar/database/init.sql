-- USERS TABLE

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(30) UNIQUE NOT NULL, 
    email VARCHAR(254) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    balance NUMERIC(10,2) NOT NULL DEFAULT 100.00,
    bio TEXT, 
    profile_image_path VARCHAR(255),
    is_blocked BOOLEAN DEFAULT FALSE,
    failed_attempts INTEGER DEFAULT 0,
    last_failed_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- TRANSACTIONS TABLE

CREATE TABLE transactions (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    amount NUMERIC(10,2) NOT NULL,   
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sender
        FOREIGN KEY (sender_id)
        REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_receiver
        FOREIGN KEY (receiver_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);


-- ACTIVITY LOGS

CREATE TABLE activity_logs (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    webpage VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- INDEXES

CREATE INDEX idx_transactions_sender
ON transactions(sender_id);

CREATE INDEX idx_transactions_receiver
ON transactions(receiver_id);

CREATE INDEX idx_logs_username
ON activity_logs(username);


CREATE INDEX idx_username_search
ON users(username);


-- PREVENT NEGATIVE BALANCE

CREATE OR REPLACE FUNCTION prevent_negative_balance()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.balance < 0 THEN
        RAISE EXCEPTION 'Balance cannot be negative';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_balance_trigger
BEFORE UPDATE ON users
FOR EACH ROW
EXECUTE FUNCTION prevent_negative_balance();

ALTER TABLE transactions
ADD CONSTRAINT amount_must_be_positive
CHECK (amount > 0);

-- To prevent self transfers
ALTER TABLE transactions
ADD CONSTRAINT no_self_transfer
CHECK (sender_id <> receiver_id);


ALTER TABLE users
ADD CONSTRAINT valid_email_format
CHECK (email LIKE '%@%');

ALTER TABLE users
ADD CONSTRAINT username_not_empty
CHECK (TRIM(username) <> '');

