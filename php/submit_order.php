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
    $required_fields = ['customer_name', 'customer_phone', 'product_id', 'product_name', 'product_price'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            sendJSONResponse(['success' => false, 'message' => "Missing required field: $field"], 400);
        }
    }
    
    // Sanitize inputs
    $customer_name = sanitizeInput($input['customer_name']);
    $customer_phone = sanitizeInput($input['customer_phone']);
    $product_id = (int)$input['product_id'];
    $product_name = sanitizeInput($input['product_name']);
    $product_price = (float)$input['product_price'];
    $notes = isset($input['notes']) ? sanitizeInput($input['notes']) : '';
    
    // Validate phone number
    if (!isValidPhone($customer_phone)) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid phone number'], 400);
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        // If no database connection, still accept the order (log it)
        logError("Order received but no DB: $customer_name - $product_name - $product_price");
        sendJSONResponse([
            'success' => true,
            'message' => 'Order received successfully',
            'order_id' => time() // Use timestamp as fake order ID
        ]);
    }
    
    try {
        // Insert order
        $sql = "INSERT INTO orders (customer_name, customer_phone, product_id, product_name, product_price, notes) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $customer_name,
            $customer_phone,
            $product_id,
            $product_name,
            $product_price,
            $notes
        ]);
        
        if ($result) {
            $order_id = $pdo->lastInsertId();
            logError("Order saved to DB: ID $order_id - $customer_name - $product_name");
            
            sendJSONResponse([
                'success' => true,
                'message' => 'Order submitted successfully',
                'order_id' => $order_id
            ]);
        } else {
            throw new Exception("Failed to insert order");
        }
    } catch (Exception $e) {
        // Database insert failed, but still accept the order
        logError("Order DB insert failed but accepted: $customer_name - $product_name - " . $e->getMessage());
        sendJSONResponse([
            'success' => true,
            'message' => 'Order received successfully',
            'order_id' => time()
        ]);
    }
    
} catch (Exception $e) {
    logError("Error in submit_order.php: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Internal server error'], 500);
}
?>
