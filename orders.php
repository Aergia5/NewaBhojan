<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Disable error display
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Clear output buffers
while (ob_get_level()) ob_end_clean();

require_once 'db_connect.php';

session_start();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }
    
    // Validate required fields
    $required = ['firstName', 'lastName', 'phone', 'address', 'paymentMethod', 'items'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("$field is required");
        }
    }
    
    if (!is_array($input['items']) || count($input['items']) === 0) {
        throw new Exception("At least one item is required");
    }
    
    // Calculate totals
    $subtotal = 0;
    $items = [];
    
    foreach ($input['items'] as $item) {
        if (empty($item['id']) || empty($item['quantity']) || $item['quantity'] < 1) {
            throw new Exception("Invalid item data");
        }
        
        // Verify item exists and get current price
        $stmt = $pdo->prepare("SELECT item_id, price FROM menu_items WHERE item_id = ? AND is_available = TRUE");
        $stmt->execute([$item['id']]);
        $menuItem = $stmt->fetch();
        
        if (!$menuItem) {
            throw new Exception("Item not available");
        }
        
        $itemTotal = $menuItem['price'] * $item['quantity'];
        $subtotal += $itemTotal;
        
        $items[] = [
            'item_id' => $menuItem['item_id'],
            'quantity' => $item['quantity'],
            'price' => $menuItem['price']
        ];
    }
    
    $deliveryFee = 50.00;
    $total = $subtotal + $deliveryFee;
    
    // Create order
    $pdo->beginTransaction();
    
    try {
        $customerName = htmlspecialchars($input['firstName']) . ' ' . htmlspecialchars($input['lastName']);
        $phone = htmlspecialchars($input['phone']);
        $address = htmlspecialchars($input['address']);
        $instructions = isset($input['instructions']) ? htmlspecialchars($input['instructions']) : '';
        $paymentMethod = htmlspecialchars($input['paymentMethod']);
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        $stmt = $pdo->prepare("INSERT INTO orders (
            user_id, customer_name, customer_phone, delivery_address, 
            special_instructions, subtotal, delivery_fee, total_amount, 
            payment_method, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        
        $stmt->execute([
            $userId, $customerName, $phone, $address, 
            $instructions, $subtotal, $deliveryFee, $total, 
            $paymentMethod
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Add order items
        $stmt = $pdo->prepare("INSERT INTO order_items (
            order_id, item_id, quantity, price_at_order
        ) VALUES (?, ?, ?, ?)");
        
        foreach ($items as $item) {
            $stmt->execute([$orderId, $item['item_id'], $item['quantity'], $item['price']]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'orderId' => $orderId,
            'total' => $total
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}