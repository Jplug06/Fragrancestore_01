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
    
    $order_id = isset($input['order_id']) ? (int)$input['order_id'] : 0;
    
    if ($order_id <= 0) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid order ID'], 400);
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $result = $stmt->execute([$order_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        sendJSONResponse([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    } else {
        sendJSONResponse(['success' => false, 'message' => 'Order not found'], 404);
    }
    
} catch (Exception $e) {
    logError("Error in delete_order.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Internal server error'], 500);
}
?>
