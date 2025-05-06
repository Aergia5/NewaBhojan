<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Clear any previous output
if (ob_get_length()) ob_clean();

require_once 'db_connect.php';

session_start();

$action = $_POST['action'] ?? '';

if ($action === 'place_order') {
    // Get order data
    $customerName = $_POST['firstName'] . ' ' . $_POST['lastName'];
    $customerPhone = $_POST['phone'];
    $deliveryAddress = $_POST['address'];
    $deliveryInstructions = $_POST['instructions'] ?? '';
    $paymentMethod = $_POST['paymentMethod'];
    $cartItems = json_decode($_POST['cartItems'], true);
    
    // Calculate totals
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $deliveryFee = 50; // Fixed delivery fee
    $total = $subtotal + $deliveryFee;
    
    // Get user ID if logged in
    $userId = $_SESSION['user_id'] ?? null;
    
    try {
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_phone, delivery_address, delivery_instructions, payment_method, subtotal, delivery_fee, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $customerName, $customerPhone, $deliveryAddress, $deliveryInstructions, $paymentMethod, $subtotal, $deliveryFee, $total]);
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, price_at_order) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'orderId' => $orderId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()]);
    }
} elseif ($action === 'get_orders' && isset($_SESSION['user_id'])) {
    // Get user's order history
    $stmt = $pdo->prepare("SELECT o.*, 
                          (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.order_id) as item_count
                          FROM orders o 
                          WHERE o.user_id = ?
                          ORDER BY o.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $orders]);
} elseif ($action === 'get_order_details' && isset($_POST['order_id'])) {
    // Get order details
    $orderId = $_POST['order_id'];
    
    // Verify the order belongs to the user (if logged in)
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT 1 FROM orders WHERE order_id = ? AND user_id = ?");
        $stmt->execute([$orderId, $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }
    }
    
    // Get order items
    $stmt = $pdo->prepare("SELECT oi.*, m.name, m.image_url 
                          FROM order_items oi 
                          JOIN menu_items m ON oi.item_id = m.item_id
                          WHERE oi.order_id = ?");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $items]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action or unauthorized']);
}
?>