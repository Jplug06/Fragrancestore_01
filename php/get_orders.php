<?php
require_once 'config.php';

setJSONHeaders();

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    $status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
    
    $sql = "SELECT * FROM orders";
    $params = [];
    
    if ($status !== 'all') {
        $sql .= " WHERE order_status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY order_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    
    sendJSONResponse([
        'success' => true,
        'orders' => $orders
    ]);
    
} catch (Exception $e) {
    logError("Error in get_orders.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Failed to fetch orders'], 500);
}
?>
