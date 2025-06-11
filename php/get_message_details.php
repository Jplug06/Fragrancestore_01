<?php
require_once 'config.php';

setJSONHeaders();

try {
    $message_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($message_id <= 0) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid message ID'], 400);
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $message = $stmt->fetch();
    
    if (!$message) {
        sendJSONResponse(['success' => false, 'message' => 'Message not found'], 404);
    }
    
    sendJSONResponse([
        'success' => true,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    logError("Error in get_message_details.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Failed to fetch message details'], 500);
}
?>
