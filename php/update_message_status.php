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
    
    $message_id = isset($input['message_id']) ? (int)$input['message_id'] : 0;
    $status = isset($input['status']) ? sanitizeInput($input['status']) : '';
    
    if ($message_id <= 0) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid message ID'], 400);
    }
    
    $valid_statuses = ['unread', 'read', 'replied'];
    if (!in_array($status, $valid_statuses)) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid status'], 400);
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
    $result = $stmt->execute([$status, $message_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        sendJSONResponse([
            'success' => true,
            'message' => 'Message status updated successfully'
        ]);
    } else {
        sendJSONResponse(['success' => false, 'message' => 'Message not found or no changes made'], 404);
    }
    
} catch (Exception $e) {
    logError("Error in update_message_status.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Internal server error'], 500);
}
?>
