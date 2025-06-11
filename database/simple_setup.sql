-- Simple database setup for fragrance shop
-- Run this in your MySQL/phpMyAdmin

-- Create database
CREATE DATABASE IF NOT EXISTS fragrance_shop;
USE fragrance_shop;

-- Create a simple products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    subcategory VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    final_price DECIMAL(10,2) NOT NULL,
    description TEXT,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample products
INSERT INTO products (name, subcategory, category, base_price, final_price, description, stock_quantity) VALUES 
('Asad', 'Spicy/Intense', 'Masculine', 250.00, 300.00, 'Intense spicy fragrance with bold character', 25),
('Supremacy Not Only Intense', 'Spicy/Intense', 'Masculine', 300.00, 350.00, 'Powerful and commanding intense fragrance', 20),
('Badee Oud For Glory', 'Oud/Woody', 'Masculine', 320.00, 370.00, 'Luxurious oud with woody undertones', 12),
('Sharaf Blend', 'Sensual/Evening', 'Sexy', 310.00, 360.00, 'Sensual blend perfect for evening wear', 18),
('Maahir Legacy Silver', 'Aquatic/Clean', 'Fresh', 250.00, 300.00, 'Clean aquatic fragrance with silver notes', 25);

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin user (password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
