<?php
// Simple status check page
header('Content-Type: text/html; charset=utf-8');

echo "<html><head><title>System Status</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
    .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
    .ok { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    .warning { background: #fff3cd; color: #856404; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style>";
echo "</head><body><div class='container'>";
echo "<h1>🔍 System Status Check</h1>";

// Check PHP
echo "<h2>PHP Status</h2>";
echo "<div class='status ok'>✅ PHP Version: " . phpversion() . "</div>";

// Check PDO
if (extension_loaded('pdo') && extension_loaded('pdo_mysql')) {
    echo "<div class='status ok'>✅ PDO MySQL Extension: Available</div>";
} else {
    echo "<div class='status error'>❌ PDO MySQL Extension: Missing</div>";
}

// Check config file
if (file_exists('config.php')) {
    echo "<div class='status ok'>✅ Config file: Found</div>";
    
    // Test database connection
    require_once 'config.php';
    $testResult = testConnection();
    
    if ($testResult['success']) {
        echo "<div class='status ok'>✅ Database: Connected (" . $testResult['products'] . " products)</div>";
    } else {
        echo "<div class='status warning'>⚠️ Database: " . $testResult['error'] . "</div>";
    }
} else {
    echo "<div class='status error'>❌ Config file: Missing</div>";
}

// Check get_products.php
if (file_exists('get_products.php')) {
    echo "<div class='status ok'>✅ Products API: File exists</div>";
    
    // Test the API
    try {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_products.php';
        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $response = file_get_contents($url, false, $context);
        
        if ($response) {
            $data = json_decode($response, true);
            if ($data && isset($data['success']) && $data['success']) {
                echo "<div class='status ok'>✅ Products API: Working (" . count($data['products']) . " products)</div>";
                echo "<div class='status ok'>📊 Data source: " . ($data['source'] ?? 'unknown') . "</div>";
            } else {
                echo "<div class='status warning'>⚠️ Products API: Invalid response</div>";
            }
        } else {
            echo "<div class='status error'>❌ Products API: No response</div>";
        }
    } catch (Exception $e) {
        echo "<div class='status error'>❌ Products API: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='status error'>❌ Products API: File missing</div>";
}

// Show recent logs
if (file_exists('debug.log')) {
    echo "<h2>Recent Log Entries</h2>";
    $logs = file_get_contents('debug.log');
    $logLines = array_slice(explode("\n", $logs), -10);
    echo "<pre>" . htmlspecialchars(implode("\n", $logLines)) . "</pre>";
} else {
    echo "<h2>No Log File Found</h2>";
}

// Show server info
echo "<h2>Server Information</h2>";
echo "<div class='status ok'>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</div>";
echo "<div class='status ok'>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</div>";
echo "<div class='status ok'>Current Directory: " . __DIR__ . "</div>";

echo "</div></body></html>";
?>
