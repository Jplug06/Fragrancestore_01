<?php
// Enhanced database configuration with comprehensive fallback
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Multiple database configurations to try
$db_configs = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'name' => 'fragrance_shop'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'name' => 'fragrance_shop'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'name' => 'fragrance_shop'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'password', 'name' => 'fragrance_shop'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'admin', 'name' => 'fragrance_shop'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'name' => 'test'],
];

// Global variable to store working connection
$GLOBALS['db_connection'] = null;
$GLOBALS['db_config'] = null;

function getDBConnection() {
    global $db_configs;
    
    // Return existing connection if available
    if ($GLOBALS['db_connection'] !== null) {
        return $GLOBALS['db_connection'];
    }
    
    // Try each configuration
    foreach ($db_configs as $config) {
        try {
            // First try to connect to MySQL server
            $pdo = new PDO("mysql:host={$config['host']}", $config['user'], $config['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if database exists
            $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['name']}'");
            if ($stmt->rowCount() == 0) {
                // Database doesn't exist, try to create it
                try {
                    $pdo->exec("CREATE DATABASE {$config['name']}");
                    logError("Created database: {$config['name']}");
                } catch (Exception $e) {
                    continue; // Try next config
                }
            }
            
            // Now connect to the specific database
            $pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4",
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            
            // Test the connection
            $pdo->query("SELECT 1");
            
            // Store working connection and config
            $GLOBALS['db_connection'] = $pdo;
            $GLOBALS['db_config'] = $config;
            
            // Ensure tables exist
            createTablesIfNeeded($pdo);
            
            logError("Database connected successfully: {$config['host']}/{$config['user']}/{$config['name']}");
            return $pdo;
            
        } catch (PDOException $e) {
            logError("Config failed {$config['host']}/{$config['user']}: " . $e->getMessage());
            continue;
        }
    }
    
    logError("All database configurations failed");
    return null;
}

function createTablesIfNeeded($pdo) {
    try {
        // Check if products table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
        if ($stmt->rowCount() == 0) {
            // Create products table with image support
            $pdo->exec("
                CREATE TABLE products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(200) NOT NULL,
                    brand VARCHAR(100) NOT NULL DEFAULT 'Premium',
                    subcategory VARCHAR(100) NOT NULL DEFAULT 'General',
                    category VARCHAR(50) NOT NULL DEFAULT 'Fragrance',
                    base_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    final_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    description TEXT,
                    image_url VARCHAR(500),
                    stock_quantity INT DEFAULT 1,
                    featured BOOLEAN DEFAULT FALSE,
                    rating DECIMAL(3,2) DEFAULT 4.5,
                    size VARCHAR(20) DEFAULT '100ml',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Insert sample products
            insertSampleProducts($pdo);
            logError("Created products table and inserted sample data");
        }
        
        // Check if admin_users table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("
                CREATE TABLE admin_users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Insert admin user
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
            $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
            logError("Created admin_users table");
        }
        
        // Check if orders table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'orders'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("
                CREATE TABLE orders (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    customer_name VARCHAR(200) NOT NULL,
                    customer_phone VARCHAR(20) NOT NULL,
                    product_id INT,
                    product_name VARCHAR(200) NOT NULL,
                    product_price DECIMAL(10,2) NOT NULL,
                    order_status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
                    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    notes TEXT
                )
            ");
            logError("Created orders table");
        }
        
        // Check if contact_messages table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("
                CREATE TABLE contact_messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(200) NOT NULL,
                    email VARCHAR(200) NOT NULL,
                    phone VARCHAR(20),
                    subject VARCHAR(300),
                    message TEXT NOT NULL,
                    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            logError("Created contact_messages table");
        }
        
    } catch (Exception $e) {
        logError("Error creating tables: " . $e->getMessage());
    }
}

function insertSampleProducts($pdo) {
    $products = [
        // Masculine - Spicy/Intense
        ['Asad', 'Lattafa', 'Spicy/Intense', 'Masculine', 250.00, 300.00, 'Bold and intense spicy fragrance with warm amber and spices. Perfect for confident men who want to make a statement.', 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 25, 1, 4.8, '100ml'],
        ['Supremacy Not Only Intense', 'Afnan', 'Spicy/Intense', 'Masculine', 300.00, 350.00, 'Powerful and commanding fragrance with intense spices and woody undertones. A true masterpiece for evening wear.', 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 20, 1, 4.7, '100ml'],
        ['Supremacy Noir', 'Afnan', 'Spicy/Intense', 'Masculine', 280.00, 330.00, 'Dark and mysterious composition with black pepper, cardamom, and smoky woods. Sophisticated and alluring.', 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=400&h=400&fit=crop', 18, 0, 4.6, '100ml'],
        ['His Confession', 'Maison Alhambra', 'Spicy/Intense', 'Masculine', 230.00, 280.00, 'Confident and bold spicy scent with cinnamon, nutmeg, and warm vanilla. Perfect for the modern gentleman.', 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 22, 0, 4.5, '100ml'],
        ['Sharof The Club', 'Lattafa', 'Spicy/Intense', 'Masculine', 270.00, 320.00, 'Exclusive club-worthy fragrance with premium spices and luxurious woods. For those who demand excellence.', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 15, 0, 4.7, '100ml'],

        // Masculine - Oud/Woody
        ['Badee Oud For Glory', 'Lattafa', 'Oud/Woody', 'Masculine', 320.00, 370.00, 'Luxurious oud fragrance with rich woody undertones and precious saffron. A true Middle Eastern treasure.', 'https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=400&h=400&fit=crop', 12, 1, 4.9, '100ml'],
        ['Armaf Club De Nuit', 'Armaf', 'Oud/Woody', 'Masculine', 300.00, 350.00, 'Sophisticated woody fragrance perfect for evening occasions. Elegant blend of oud and modern woods.', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 16, 0, 4.6, '100ml'],
        ['Oud Mood', 'Lattafa', 'Oud/Woody', 'Masculine', 340.00, 390.00, 'Premium oud composition with rose and amber. Luxurious and long-lasting with exceptional projection.', 'https://images.unsplash.com/photo-1557170334-a9632e77c6e4?w=400&h=400&fit=crop', 10, 1, 4.8, '100ml'],

        // Masculine - Sweet/Warm
        ['Khamrah Gahwa', 'Lattafa', 'Sweet/Warm', 'Masculine', 350.00, 400.00, 'Sweet coffee-inspired fragrance with warm spices and vanilla. Gourmand masterpiece for coffee lovers.', 'https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?w=400&h=400&fit=crop', 10, 1, 4.9, '100ml'],
        ['Supremacy Collector', 'Afnan', 'Sweet/Warm', 'Masculine', 290.00, 340.00, 'Collectible sweet and warm composition with honey, vanilla, and warm spices. Limited edition quality.', 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 14, 0, 4.5, '100ml'],

        // Sexy - Sensual/Evening
        ['Sharaf Blend', 'Lattafa', 'Sensual/Evening', 'Sexy', 310.00, 360.00, 'Sensual blend perfect for romantic evenings. Mysterious and captivating with floral and woody notes.', 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 18, 1, 4.7, '100ml'],
        ['9pm', 'Afnan', 'Sensual/Evening', 'Sexy', 270.00, 320.00, 'Seductive fragrance designed for night time adventures. Bold and confident with magnetic appeal.', 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 20, 0, 4.6, '100ml'],
        ['Rayhaan Elixir', 'Lattafa', 'Sensual/Evening', 'Sexy', 250.00, 300.00, 'Magical elixir of seduction with exotic florals and warm amber. Enchanting and irresistible.', 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=400&h=400&fit=crop', 22, 0, 4.5, '100ml'],
        ['The Kingdom', 'Afnan', 'Sensual/Evening', 'Sexy', 260.00, 310.00, 'Royal and commanding evening scent with regal presence. Sophisticated and powerful.', 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 16, 0, 4.6, '100ml'],
        ['Liquid Brun', 'Maison Alhambra', 'Sensual/Evening', 'Sexy', 240.00, 290.00, 'Smooth and sensual liquid fragrance with creamy textures and warm embrace.', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 24, 0, 4.4, '100ml'],

        // Sexy - Gourmand
        ['Khamrah', 'Lattafa', 'Gourmand', 'Sexy', 320.00, 370.00, 'Sweet gourmand with irresistible appeal. Rich vanilla, caramel, and warm spices create pure temptation.', 'https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=400&h=400&fit=crop', 15, 1, 4.8, '100ml'],
        ['Bourbon', 'Maison Alhambra', 'Gourmand', 'Sexy', 260.00, 310.00, 'Rich bourbon-inspired gourmand scent with whiskey notes and sweet vanilla. Sophisticated indulgence.', 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=400&fit=crop', 18, 0, 4.5, '100ml'],

        // Fresh - Aquatic/Clean
        ['Maahir Legacy Silver', 'Lattafa', 'Aquatic/Clean', 'Fresh', 250.00, 300.00, 'Clean aquatic fragrance with silver notes and fresh marine breeze. Perfect for daily wear.', 'https://images.unsplash.com/photo-1557170334-a9632e77c6e4?w=400&h=400&fit=crop', 25, 1, 4.6, '100ml'],
        ['Amber Oud Aqua', 'Maison Alhambra', 'Aquatic/Clean', 'Fresh', 280.00, 330.00, 'Unique blend of aquatic freshness with warm amber. Modern and sophisticated.', 'https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?w=400&h=400&fit=crop', 20, 0, 4.5, '100ml'],
        ['Afnan 9am Dive', 'Afnan', 'Aquatic/Clean', 'Fresh', 270.00, 320.00, 'Fresh morning dive into aquatic notes with energizing citrus and clean musk.', 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 22, 0, 4.4, '100ml'],

        // Fresh - Citrus/Modern
        ['Najdia', 'Lattafa', 'Citrus/Modern', 'Fresh', 220.00, 270.00, 'Modern citrus fragrance with contemporary appeal. Bright, fresh, and energizing for active lifestyles.', 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop', 30, 0, 4.3, '100ml'],
        ['Rasasi Hawas', 'Rasasi', 'Citrus/Modern', 'Fresh', 300.00, 350.00, 'Fresh citrus with modern sophistication. Premium quality with excellent longevity.', 'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=400&h=400&fit=crop', 18, 1, 4.7, '100ml'],
        ['Odyssey Mega', 'Armaf', 'Citrus/Modern', 'Fresh', 230.00, 280.00, 'Epic citrus journey with modern woods and fresh herbs. Adventure in a bottle.', 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=400&h=400&fit=crop', 25, 0, 4.4, '100ml'],
        ['Rave Now Intense', 'Lattafa', 'Citrus/Modern', 'Fresh', 210.00, 260.00, 'Intense modern citrus for active lifestyle. Energizing and long-lasting performance.', 'https://images.unsplash.com/photo-1563170351-be82bc888aa4?w=400&h=400&fit=crop', 28, 0, 4.3, '100ml'],

        // Fresh - Versatile Daily
        ['Armaf Iconic', 'Armaf', 'Versatile Daily', 'Fresh', 230.00, 280.00, 'Iconic versatile fragrance perfect for daily wear. Fresh, clean, and universally appealing.', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop', 35, 1, 4.5, '100ml'],
        ['Fakhar Black', 'Lattafa', 'Versatile Daily', 'Fresh', 240.00, 290.00, 'Sophisticated daily wear fragrance with modern elegance. Perfect for office and casual wear.', 'https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=400&h=400&fit=crop', 30, 0, 4.4, '100ml']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, brand, subcategory, category, base_price, final_price, description, image_url, stock_quantity, featured, rating, size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($products as $product) {
        try {
            $stmt->execute($product);
        } catch (Exception $e) {
            // Continue if product already exists
            continue;
        }
    }
}

// Set JSON response headers
function setJSONHeaders() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0);
    }
}

// Send JSON response
function sendJSONResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Validate and sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate phone number
function isValidPhone($phone) {
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    return strlen($phone) >= 10;
}

// Log errors
function logError($message) {
    $logFile = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Test database connection
function testConnection() {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
            $result = $stmt->fetch();
            return ['success' => true, 'products' => $result['count']];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    return ['success' => false, 'error' => 'No database connection'];
}
?>

