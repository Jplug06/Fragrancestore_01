<?php
require_once 'config.php';

setJSONHeaders();

try {
    $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($order_id <= 0) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid order ID'], 400);
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        sendJSONResponse(['success' => false, 'message' => 'Order not found'], 404);
    }
    
    sendJSONResponse([
        'success' => true,
        'order' => $order
    ]);
    
} catch (Exception $e) {
    logError("Error in get_order_details.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Failed to fetch order details'], 500);
}
?>
