-- Create database and tables for the fragrance shop

CREATE DATABASE IF NOT EXISTS fragrance_shop;
USE fragrance_shop;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert main categories
INSERT INTO categories (name, description) VALUES 
('Masculine', 'Bold and powerful fragrances for men'),
('Sexy', 'Seductive and alluring scents'),
('Fresh', 'Clean and refreshing fragrances');

-- Subcategories table
CREATE TABLE IF NOT EXISTS subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Insert subcategories
INSERT INTO subcategories (category_id, name, description) VALUES 
(1, 'Spicy/Intense', 'Bold spicy fragrances with intense character'),
(1, 'Oud/Woody', 'Rich oud and woody masculine scents'),
(1, 'Sweet/Warm', 'Sweet and warm masculine fragrances'),
(2, 'Sensual/Evening', 'Seductive evening fragrances'),
(2, 'Gourmand', 'Sweet and edible scent profiles'),
(3, 'Aquatic/Clean', 'Fresh aquatic and clean fragrances'),
(3, 'Citrus/Modern', 'Modern citrus-based fresh scents'),
(3, 'Versatile Daily', 'Versatile fragrances for everyday wear');

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    subcategory_id INT,
    base_price DECIMAL(10,2) NOT NULL,
    final_price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id)
);

-- Insert actual products with your pricing
INSERT INTO products (name, subcategory_id, base_price, final_price, description, stock_quantity) VALUES 
-- Masculine - Spicy/Intense
('Asad', 1, 250.00, 300.00, 'Intense spicy fragrance with bold character', 25),
('Supremacy Not Only Intense', 1, 300.00, 350.00, 'Powerful and commanding intense fragrance', 20),
('Supremacy Noir', 1, 280.00, 330.00, 'Dark and mysterious spicy composition', 18),
('His Confession', 1, 230.00, 280.00, 'Confident and bold spicy scent', 22),
('Sharof The Club', 1, 270.00, 320.00, 'Exclusive club-worthy intense fragrance', 15),

-- Masculine - Oud/Woody
('Badee Oud For Glory', 2, 320.00, 370.00, 'Luxurious oud with woody undertones', 12),
('Armaf Club De Nuit', 2, 300.00, 350.00, 'Sophisticated woody fragrance for evening', 16),

-- Masculine - Sweet/Warm
('Khamrah Gahwa', 3, 350.00, 400.00, 'Sweet coffee-inspired warm fragrance', 10),
('Supremacy Collector', 3, 290.00, 340.00, 'Collectible sweet and warm composition', 14),

-- Sexy - Sensual/Evening
('Sharaf Blend', 4, 310.00, 360.00, 'Sensual blend perfect for evening wear', 18),
('9pm', 4, 270.00, 320.00, 'Seductive fragrance for night time', 20),
('Rayhaan Elixir', 4, 250.00, 300.00, 'Magical elixir of seduction', 22),
('The Kingdom', 4, 260.00, 310.00, 'Royal and commanding evening scent', 16),
('Liquid Brun', 4, 240.00, 290.00, 'Smooth and sensual liquid fragrance', 24),

-- Sexy - Gourmand
('Khamrah', 5, 320.00, 370.00, 'Sweet gourmand with irresistible appeal', 15),
('Bourbon', 5, 260.00, 310.00, 'Rich bourbon-inspired gourmand scent', 18),

-- Fresh - Aquatic/Clean
('Maahir Legacy Silver', 6, 250.00, 300.00, 'Clean aquatic fragrance with silver notes', 25),
('Amber Oud Aqua', 6, 280.00, 330.00, 'Aquatic freshness with amber warmth', 20),
('Afnan 9am Dive', 6, 270.00, 320.00, 'Fresh morning dive into aquatic notes', 22),

-- Fresh - Citrus/Modern
('Najdia', 7, 220.00, 270.00, 'Modern citrus with contemporary appeal', 30),
('Rasasi Hawas', 7, 300.00, 350.00, 'Fresh citrus with modern sophistication', 18),
('Odyssey Mega', 7, 230.00, 280.00, 'Epic citrus journey fragrance', 25),
('Rave Now Intense', 7, 210.00, 260.00, 'Intense modern citrus for active lifestyle', 28),

-- Fresh - Versatile Daily
('Armaf Iconic', 8, 230.00, 280.00, 'Iconic versatile fragrance for daily wear', 35),
('Fakhar Black', 8, 240.00, 290.00, 'Sophisticated daily wear fragrance', 30);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(200) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    order_status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Contact messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(300),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
