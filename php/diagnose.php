<?php
// Comprehensive diagnostic tool
header('Content-Type: text/html; charset=utf-8');

echo "<html><head><title>Fragrance Shop Diagnostics</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
    h1, h2 { color: #2c3e50; }
    .test { margin: 15px 0; padding: 15px; border-radius: 5px; }
    .pass { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .fail { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; margin: 10px 0; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";
echo "</head><body><div class='container'>";
echo "<h1>🔍 Fragrance Shop Diagnostics</h1>";
echo "<p>Let's check what's causing the database connection issue...</p>";

// Test 1: PHP Configuration
echo "<h2>1. PHP Configuration</h2>";
echo "<div class='test info'>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Current Script:</strong> " . __FILE__ . "<br>";
echo "</div>";

// Test 2: PDO Extension
echo "<h2>2. PDO MySQL Extension</h2>";
if (extension_loaded('pdo') && extension_loaded('pdo_mysql')) {
    echo "<div class='test pass'>✅ PDO and PDO MySQL extensions are loaded</div>";
} else {
    echo "<div class='test fail'>❌ PDO or PDO MySQL extension is missing</div>";
    echo "<div class='info'>You need to enable PDO and PDO MySQL in your PHP configuration</div>";
}

// Test 3: Database Connection Parameters
echo "<h2>3. Database Connection Test</h2>";
$configs = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'db' => 'fragrance_shop'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'db' => 'fragrance_shop'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'db' => 'fragrance_shop'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'password', 'db' => 'fragrance_shop'],
];

$connected = false;
$workingConfig = null;

foreach ($configs as $config) {
    echo "<div class='test info'>";
    echo "<strong>Testing:</strong> {$config['host']} / {$config['user']} / " . 
         (empty($config['pass']) ? '(no password)' : '(with password)') . "<br>";
    
    try {
        // Test basic MySQL connection first
        $pdo = new PDO("mysql:host={$config['host']}", $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ Basic MySQL connection successful<br>";
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['db']}'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Database '{$config['db']}' exists<br>";
            
            // Try to connect to the specific database
            $pdo = new PDO("mysql:host={$config['host']};dbname={$config['db']}", $config['user'], $config['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "✅ Connected to database '{$config['db']}'<br>";
            $connected = true;
            $workingConfig = $config;
            echo "</div><div class='test pass'>🎉 This configuration works!</div>";
            break;
        } else {
            echo "❌ Database '{$config['db']}' does not exist<br>";
            
            // Show available databases
            $stmt = $pdo->query("SHOW DATABASES");
            $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<strong>Available databases:</strong> " . implode(', ', $databases) . "<br>";
        }
        
    } catch (PDOException $e) {
        echo "❌ Connection failed: " . $e->getMessage() . "<br>";
    }
    echo "</div>";
}

if (!$connected) {
    echo "<div class='test fail'>";
    echo "<h3>❌ No working database configuration found!</h3>";
    echo "<p><strong>Common solutions:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP/WAMP/MAMP is running</li>";
    echo "<li>Start the MySQL service</li>";
    echo "<li>Check if the database 'fragrance_shop' exists</li>";
    echo "<li>Verify your MySQL username and password</li>";
    echo "</ul>";
    echo "</div>";
} else {
    // Test 4: Check tables and data
    echo "<h2>4. Database Structure Check</h2>";
    try {
        $pdo = new PDO("mysql:host={$workingConfig['host']};dbname={$workingConfig['db']}", 
                      $workingConfig['user'], $workingConfig['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<div class='test info'>";
        echo "<strong>Tables found:</strong> " . implode(', ', $tables) . "<br>";
        echo "</div>";
        
        if (in_array('products', $tables)) {
            echo "<div class='test pass'>✅ Products table exists</div>";
            
            // Check products count
            $stmt = $pdo->query("SELECT COUNT(*) FROM products");
            $count = $stmt->fetchColumn();
            
            echo "<div class='test info'>";
            echo "<strong>Products in database:</strong> $count<br>";
            echo "</div>";
            
            if ($count > 0) {
                echo "<div class='test pass'>✅ Products found in database ($count items)</div>";
                
                // Show sample products
                $stmt = $pdo->query("SELECT id, name, final_price FROM products LIMIT 5");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<div class='test info'>";
                echo "<strong>Sample products:</strong><br>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Name</th><th>Price</th></tr>";
                foreach ($products as $product) {
                    echo "<tr><td>{$product['id']}</td><td>{$product['name']}</td><td>GH₵{$product['final_price']}</td></tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div class='test fail'>❌ Products table is empty</div>";
            }
        } else {
            echo "<div class='test fail'>❌ Products table does not exist</div>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='test fail'>❌ Database structure check failed: " . $e->getMessage() . "</div>";
    }
}

// Test 5: File permissions and paths
echo "<h2>5. File System Check</h2>";
$files_to_check = [
    'php/config.php',
    'php/get_products.php',
    'js/products.js',
    'products.html'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<div class='test pass'>✅ $file exists</div>";
    } else {
        echo "<div class='test fail'>❌ $file not found</div>";
    }
}

// Test 6: Generate working config
if ($connected) {
    echo "<h2>6. 🔧 Working Configuration</h2>";
    echo "<div class='test pass'>";
    echo "<p>Use this configuration in your config.php file:</p>";
    echo "<div class='code'>";
    echo "define('DB_HOST', '{$workingConfig['host']}');<br>";
    echo "define('DB_USER', '{$workingConfig['user']}');<br>";
    echo "define('DB_PASS', '" . ($workingConfig['pass'] ?: '') . "');<br>";
    echo "define('DB_NAME', '{$workingConfig['db']}');";
    echo "</div>";
    echo "</div>";
}

echo "<h2>7. 🚀 Next Steps</h2>";
echo "<div class='test info'>";
if ($connected) {
    echo "<p><strong>Good news!</strong> Your database connection is working. The issue might be:</p>";
    echo "<ul>";
    echo "<li>Check that your config.php file has the correct settings shown above</li>";
    echo "<li>Make sure the get_products.php file is accessible</li>";
    echo "<li>Clear your browser cache and try again</li>";
    echo "</ul>";
} else {
    echo "<p><strong>Database connection failed.</strong> Please:</p>";
    echo "<ul>";
    echo "<li>Start your MySQL server (XAMPP/WAMP/MAMP)</li>";
    echo "<li>Create the 'fragrance_shop' database</li>";
    echo "<li>Run the setup.sql script to create tables</li>";
    echo "<li>Check your MySQL username and password</li>";
    echo "</ul>";
}
echo "</div>";

echo "</div></body></html>";
?>
