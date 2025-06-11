<?php
require_once 'config.php';

setJSONHeaders();

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    $status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
    
    $sql = "SELECT * FROM contact_messages";
    $params = [];
    
    if ($status !== 'all') {
        $sql .= " WHERE status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll();
    
    sendJSONResponse([
        'success' => true,
        'messages' => $messages
    ]);
    
} catch (Exception $e) {
    logError("Error in get_messages.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Failed to fetch messages'], 500);
}
?>
