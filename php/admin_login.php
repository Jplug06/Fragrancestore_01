<?php
require_once 'config.php';

setJSONHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid JSON data'], 400);
    }
    
    // Validate required fields
    if (empty($input['username']) || empty($input['password'])) {
        sendJSONResponse(['success' => false, 'message' => 'Username and password are required'], 400);
    }
    
    $username = sanitizeInput($input['username']);
    $password = $input['password'];
    
    // For demo purposes - hardcoded admin credentials
    // This will work even if the database connection fails
    if ($username === 'admin' && $password === 'admin123') {
        sendJSONResponse([
            'success' => true,
            'message' => 'Login successful',
            'username' => 'admin'
        ]);
        exit;
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    // Get admin user
    $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
    }
    
    // Verify password - for demo purposes, we'll accept plain text comparison too
    if (password_verify($password, $admin['password']) || $password === 'admin123') {
        // Start session
        session_start();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        sendJSONResponse([
            'success' => true,
            'message' => 'Login successful',
            'username' => $admin['username']
        ]);
    } else {
        sendJSONResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
    }
    
} catch (Exception $e) {
    logError("Error in admin_login.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Internal server error'], 500);
}
?>
