-- Simple Shop MVP — full setup script
-- In phpMyAdmin: open the SQL tab, paste this ENTIRE file, then click Go.
-- Safe to run more than once (drops and recreates tables, re-seeds products).

CREATE DATABASE IF NOT EXISTS ecommerce_mvp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_mvp;

-- Drop child table first (foreign keys)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL DEFAULT 'assets/images/placeholder.svg',
    stock INT UNSIGNED NOT NULL DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(180) NOT NULL,
    shipping_address TEXT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(120) NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO products (name, slug, description, price, image_url, stock) VALUES
('Classic Cotton Tee', 'classic-cotton-tee', 'Soft unisex tee for everyday wear. 100% cotton.', 19.99, 'assets/images/product-tee.svg', 50),
('Wireless Earbuds', 'wireless-earbuds', 'Compact earbuds with charging case. Up to 20 hours playback.', 49.99, 'assets/images/product-earbuds.svg', 30),
('Ceramic Mug', 'ceramic-mug', '350ml mug, dishwasher safe. Matte finish.', 12.50, 'assets/images/product-mug.svg', 80),
('Notebook Set', 'notebook-set', 'Pack of 3 dotted notebooks, A5 size.', 15.00, 'assets/images/product-notebook.svg', 40),
('Desk Lamp', 'desk-lamp', 'Adjustable LED lamp with warm/cool modes.', 34.99, 'assets/images/product-lamp.svg', 25);
