<?php
require_once 'config.php';

setJSONHeaders();

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        sendJSONResponse(['success' => false, 'message' => 'Database connection failed'], 500);
    }
    
    // Get order statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total_orders FROM orders");
    $total_orders = $stmt->fetch()['total_orders'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as pending_orders FROM orders WHERE order_status = 'pending'");
    $pending_orders = $stmt->fetch()['pending_orders'];
    
    // Get message statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total_messages FROM contact_messages");
    $total_messages = $stmt->fetch()['total_messages'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as unread_messages FROM contact_messages WHERE status = 'unread'");
    $unread_messages = $stmt->fetch()['unread_messages'];
    
    sendJSONResponse([
        'success' => true,
        'stats' => [
            'total_orders' => $total_orders,
            'pending_orders' => $pending_orders,
            'total_messages' => $total_messages,
            'unread_messages' => $unread_messages
        ]
    ]);
    
} catch (Exception $e) {
    logError("Error in get_dashboard_stats.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
}
?>
