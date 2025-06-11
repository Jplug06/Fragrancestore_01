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
    $required_fields = ['name', 'email', 'subject', 'message'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            sendJSONResponse(['success' => false, 'message' => "Missing required field: $field"], 400);
        }
    }
    
    // Sanitize inputs
    $name = sanitizeInput($input['name']);
    $email = sanitizeInput($input['email']);
    $phone = isset($input['phone']) ? sanitizeInput($input['phone']) : '';
    $subject = sanitizeInput($input['subject']);
    $message = sanitizeInput($input['message']);
    
    // Validate email
    if (!isValidEmail($email)) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid email address'], 400);
    }
    
    // Validate phone if provided
    if (!empty($phone) && !isValidPhone($phone)) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid phone number'], 400);
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    // Insert contact message
    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$name, $email, $phone, $subject, $message]);
    
    if ($result) {
        $message_id = $pdo->lastInsertId();
        
        sendJSONResponse([
            'success' => true,
            'message' => 'Message sent successfully',
            'message_id' => $message_id
        ]);
    } else {
        sendJSONResponse(['success' => false, 'message' => 'Failed to send message'], 500);
    }
    
} catch (Exception $e) {
    logError("Error in submit_contact.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Internal server error'], 500);
}
?>
