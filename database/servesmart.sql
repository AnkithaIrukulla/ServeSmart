-- Create Database
CREATE DATABASE IF NOT EXISTS servesmart;
USE servesmart;

-- =========================
-- USERS TABLE
-- =========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('distributor','ngo') NOT NULL,
    location VARCHAR(100),
    address TEXT,
    pincode VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- FOOD TABLE
-- =========================
CREATE TABLE food (
    id INT AUTO_INCREMENT PRIMARY KEY,
    distributor_id INT NOT NULL,
    food_name VARCHAR(150) NOT NULL,
    plates INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    location VARCHAR(100),
    expiry DATETIME,
    address TEXT,
    pincode VARCHAR(10),
    status ENUM('available','sold') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (distributor_id) REFERENCES users(id)
    ON DELETE CASCADE
);

-- =========================
-- ORDERS TABLE
-- =========================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ngo_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('ordered','accepted','ready','delivered') DEFAULT 'ordered',
    payment_method ENUM('COD','UPI') DEFAULT 'COD',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (ngo_id) REFERENCES users(id)
    ON DELETE CASCADE
);

-- =========================
-- ORDER ITEMS TABLE
-- =========================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT DEFAULT 1,

    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES food(id)
    ON DELETE CASCADE
);

-- =========================
-- RATINGS TABLE (Optional but Recommended)
-- =========================
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    distributor_id INT,
    ngo_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (distributor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ngo_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================
-- INDEXES (Performance)
-- =========================
CREATE INDEX idx_food_location ON food(location);
CREATE INDEX idx_food_status ON food(status);
CREATE INDEX idx_orders_ngo ON orders(ngo_id);

-- =========================
-- SAMPLE DATA (Optional)
-- =========================

-- Insert Distributor
INSERT INTO users (name, email, password, role, location)
VALUES ('Distributor One', 'dist@test.com', '$2y$10$hashedpassword', 'distributor', 'Hyderabad');

-- Insert NGO
INSERT INTO users (name, email, password, role, location)
VALUES ('NGO One', 'ngo@test.com', '$2y$10$hashedpassword', 'ngo', 'Hyderabad');

-- Sample Food
INSERT INTO food (distributor_id, food_name, plates, price, location, expiry)
VALUES (1, 'Rice & Curry', 50, 20.00, 'Hyderabad', NOW() + INTERVAL 1 DAY);

