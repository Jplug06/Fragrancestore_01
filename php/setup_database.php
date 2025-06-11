<?php
// Simple database setup script
header('Content-Type: text/html; charset=utf-8');

echo "<html><head><title>Database Setup</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
    .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
    .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>";
echo "</head><body><div class='container'>";
echo "<h1>🛠️ Database Setup</h1>";

// Database configurations to try
$configs = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => ''],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'password'],
];

$connected = false;
$pdo = null;

// Try to connect
foreach ($configs as $config) {
    try {
        $pdo = new PDO("mysql:host={$config['host']}", $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div class='success'>✅ Connected to MySQL with {$config['host']}/{$config['user']}</div>";
        $connected = true;
        break;
    } catch (PDOException $e) {
        continue;
    }
}

if (!$connected) {
    echo "<div class='error'>❌ Could not connect to MySQL. Please make sure MySQL is running.</div>";
    exit;
}

try {
    // Create database
    echo "<div class='info'>Creating database 'fragrance_shop'...</div>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS fragrance_shop");
    echo "<div class='success'>✅ Database created successfully</div>";
    
    // Use the database
    $pdo->exec("USE fragrance_shop");
    
    // Create products table
    echo "<div class='info'>Creating products table...</div>";
    $pdo->exec("
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
        )
    ");
    echo "<div class='success'>✅ Products table created</div>";
    
    // Check if products exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "<div class='info'>Inserting sample products...</div>";
        
        $products = [
            ['Asad', 'Spicy/Intense', 'Masculine', 250.00, 300.00, 'Intense spicy fragrance with bold character', 25],
            ['Supremacy Not Only Intense', 'Spicy/Intense', 'Masculine', 300.00, 350.00, 'Powerful and commanding intense fragrance', 20],
            ['Supremacy Noir', 'Spicy/Intense', 'Masculine', 280.00, 330.00, 'Dark and mysterious spicy composition', 18],
            ['His Confession', 'Spicy/Intense', 'Masculine', 230.00, 280.00, 'Confident and bold spicy scent', 22],
            ['Sharof The Club', 'Spicy/Intense', 'Masculine', 270.00, 320.00, 'Exclusive club-worthy intense fragrance', 15],
            ['Badee Oud For Glory', 'Oud/Woody', 'Masculine', 320.00, 370.00, 'Luxurious oud with woody undertones', 12],
            ['Armaf Club De Nuit', 'Oud/Woody', 'Masculine', 300.00, 350.00, 'Sophisticated woody fragrance for evening', 16],
            ['Khamrah Gahwa', 'Sweet/Warm', 'Masculine', 350.00, 400.00, 'Sweet coffee-inspired warm fragrance', 10],
            ['Supremacy Collector', 'Sweet/Warm', 'Masculine', 290.00, 340.00, 'Collectible sweet and warm composition', 14],
            ['Sharaf Blend', 'Sensual/Evening', 'Sexy', 310.00, 360.00, 'Sensual blend perfect for evening wear', 18],
            ['9pm', 'Sensual/Evening', 'Sexy', 270.00, 320.00, 'Seductive fragrance for night time', 20],
            ['Rayhaan Elixir', 'Sensual/Evening', 'Sexy', 250.00, 300.00, 'Magical elixir of seduction', 22],
            ['The Kingdom', 'Sensual/Evening', 'Sexy', 260.00, 310.00, 'Royal and commanding evening scent', 16],
            ['Liquid Brun', 'Sensual/Evening', 'Sexy', 240.00, 290.00, 'Smooth and sensual liquid fragrance', 24],
            ['Khamrah', 'Gourmand', 'Sexy', 320.00, 370.00, 'Sweet gourmand with irresistible appeal', 15],
            ['Bourbon', 'Gourmand', 'Sexy', 260.00, 310.00, 'Rich bourbon-inspired gourmand scent', 18],
            ['Maahir Legacy Silver', 'Aquatic/Clean', 'Fresh', 250.00, 300.00, 'Clean aquatic fragrance with silver notes', 25],
            ['Amber Oud Aqua', 'Aquatic/Clean', 'Fresh', 280.00, 330.00, 'Aquatic freshness with amber warmth', 20],
            ['Afnan 9am Dive', 'Aquatic/Clean', 'Fresh', 270.00, 320.00, 'Fresh morning dive into aquatic notes', 22],
            ['Najdia', 'Citrus/Modern', 'Fresh', 220.00, 270.00, 'Modern citrus with contemporary appeal', 30],
            ['Rasasi Hawas', 'Citrus/Modern', 'Fresh', 300.00, 350.00, 'Fresh citrus with modern sophistication', 18],
            ['Odyssey Mega', 'Citrus/Modern', 'Fresh', 230.00, 280.00, 'Epic citrus journey fragrance', 25],
            ['Rave Now Intense', 'Citrus/Modern', 'Fresh', 210.00, 260.00, 'Intense modern citrus for active lifestyle', 28],
            ['Armaf Iconic', 'Versatile Daily', 'Fresh', 230.00, 280.00, 'Iconic versatile fragrance for daily wear', 35],
            ['Fakhar Black', 'Versatile Daily', 'Fresh', 240.00, 290.00, 'Sophisticated daily wear fragrance', 30]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO products (name, subcategory, category, base_price, final_price, description, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($products as $product) {
            $stmt->execute($product);
        }
        
        echo "<div class='success'>✅ Inserted " . count($products) . " products</div>";
    } else {
        echo "<div class='info'>Products table already has $count products</div>";
    }
    
    // Create admin table
    echo "<div class='info'>Creating admin users table...</div>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert admin user
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
    
    echo "<div class='success'>✅ Admin user created (username: admin, password: admin123)</div>";
    
    echo "<div class='success'><h2>🎉 Setup Complete!</h2>";
    echo "<p>Your database is now ready. You can:</p>";
    echo "<ul>";
    echo "<li><a href='../products.html'>View Products Page</a></li>";
    echo "<li><a href='../admin.html'>Access Admin Panel</a></li>";
    echo "<li><a href='diagnose.php'>Run Diagnostics</a></li>";
    echo "</ul></div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Setup failed: " . $e->getMessage() . "</div>";
}

echo "</div></body></html>";
?>
