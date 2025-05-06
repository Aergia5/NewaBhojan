<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db_connect.php';

session_start();
// Helper functions
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

try {
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    // For preflight requests
    if ($requestMethod === 'OPTIONS') {
        exit(0);
    }
    
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $endpoint = rtrim(str_replace('/api', '', $requestUri), '/');

    switch ($endpoint) {
        case '/auth/login':
            if ($requestMethod === 'POST') handleLogin();
            break;
            
        case '/auth/signup':
            if ($requestMethod === 'POST') handleSignup();
            break;
            
        case '/auth/logout':
            if ($requestMethod === 'POST') handleLogout();
            break;
            
        case '/auth/forgot-password':
            if ($requestMethod === 'POST') handleForgotPassword();
            break;
            
        case '/menu':
            if ($requestMethod === 'GET') handleGetMenu();
            break;
            
        case '/menu/categories':
            if ($requestMethod === 'GET') handleGetCategories();
            break;
            
        case '/orders':
            if ($requestMethod === 'POST') handleCreateOrder();
            if ($requestMethod === 'GET') handleGetOrders();
            break;
            
        case '/orders/history':
            if ($requestMethod === 'GET') {
                requireAuth();
                handleOrderHistory();
            }
            break;
            
        case '/user/profile':
            if ($requestMethod === 'GET') {
                requireAuth();
                handleGetProfile();
            }
            if ($requestMethod === 'PUT') {
                requireAuth();
                handleUpdateProfile();
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

// Main API router
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = rtrim(str_replace('/api', '', $requestUri), '/');

try {
    switch ($endpoint) {
        case '/auth/login':
            if ($requestMethod === 'POST') handleLogin();
            break;
            
        case '/auth/signup':
            if ($requestMethod === 'POST') handleSignup();
            break;
            
        case '/auth/logout':
            if ($requestMethod === 'POST') handleLogout();
            break;
            
        case '/auth/forgot-password':
            if ($requestMethod === 'POST') handleForgotPassword();
            break;
            
        case '/auth/reset-password':
            if ($requestMethod === 'POST') handleResetPassword();
            break;
            
        case '/menu':
            if ($requestMethod === 'GET') handleGetMenu();
            break;
            
        case '/menu/categories':
            if ($requestMethod === 'GET') handleGetCategories();
            break;
            
        case '/orders':
            if ($requestMethod === 'POST') handleCreateOrder();
            if ($requestMethod === 'GET') handleGetOrders();
            break;
            
        case '/orders/history':
            if ($requestMethod === 'GET') {
                requireAuth();
                handleOrderHistory();
            }
            break;
            
        case '/user/profile':
            if ($requestMethod === 'GET') {
                requireAuth();
                handleGetProfile();
            }
            if ($requestMethod === 'PUT') {
                requireAuth();
                handleUpdateProfile();
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

// Authentication Handlers
function handleLogin() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !verifyPassword($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        return;
    }
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_login'] = time();
    
    // Update last login time
    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?")
        ->execute([$user['user_id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['user_id'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ]
    ]);
}

function handleSignup() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $email = sanitizeInput($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirmPassword'] ?? '';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    if (!validateEmail($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    if (strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
        return;
    }
    
    if ($password !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        return;
    }
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        return;
    }
    
    // Create user
    $passwordHash = hashPassword($password);
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->execute([$firstName, $lastName, $email, $passwordHash]);
    
    if ($stmt->rowCount() === 1) {
        $userId = $pdo->lastInsertId();
        
        // Automatically log in the user
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $firstName;
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $userId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
}

function handleLogout() {
    // Clear session data
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function handleForgotPassword() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $email = sanitizeInput($data['email'] ?? '');
    
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        return;
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Don't reveal if email exists for security
        echo json_encode(['success' => true, 'message' => 'If this email exists, a reset link has been sent']);
        return;
    }
    
    // Generate token
    $token = generateToken();
    $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiration
    
    // Delete any existing tokens
    $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = ?")
        ->execute([$user['user_id']]);
    
    // Insert new token
    $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user['user_id'], $token, $expiresAt]);
    
    // In production, you would send an email here with the reset link
    // Example: https://yourdomain.com/reset-password?token=$token
    
    echo json_encode(['success' => true, 'message' => 'If this email exists, a reset link has been sent']);
}

function handleResetPassword() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $token = sanitizeInput($data['token'] ?? '');
    $newPassword = $data['newPassword'] ?? '';
    $confirmPassword = $data['confirmPassword'] ?? '';
    
    if (empty($token) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Token is required']);
        return;
    }
    
    if (empty($newPassword) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'New password is required']);
        return;
    }
    
    if (strlen($newPassword) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
        return;
    }
    
    if ($newPassword !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        return;
    }
    
    // Verify token
    $stmt = $pdo->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND expires_at > NOW() AND used = 0");
    $stmt->execute([$token]);
    $tokenRecord = $stmt->fetch();
    
    if (!$tokenRecord) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        return;
    }
    
    // Update password
    $passwordHash = hashPassword($newPassword);
    $pdo->beginTransaction();
    
    try {
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?")
            ->execute([$passwordHash, $tokenRecord['user_id']]);
        
        $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token_id = ?")
            ->execute([$tokenRecord['token_id']]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to reset password']);
    }
}

// Menu Handlers
function handleGetMenu() {
    global $pdo;
    
    $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
    
    $query = "SELECT m.*, c.name AS category_name 
              FROM menu_items m 
              JOIN categories c ON m.category_id = c.category_id
              WHERE m.is_available = TRUE";
    
    $params = [];
    
    if ($categoryId) {
        $query .= " AND m.category_id = ?";
        $params[] = $categoryId;
    }
    
    $query .= " ORDER BY c.name, m.name";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $menuItems = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $menuItems]);
}

function handleGetCategories() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $categories]);
}

// Order Handlers
function handleCreateOrder() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['firstName', 'lastName', 'phone', 'address', 'paymentMethod', 'items'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "$field is required"]);
            return;
        }
    }
    
    $items = $data['items'];
    if (!is_array($items) || count($items) === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'At least one item is required']);
        return;
    }
    
    // Calculate totals
    $subtotal = 0;
    $itemDetails = [];
    
    // Verify items and get current prices
    foreach ($items as $item) {
        if (empty($item['id']) || empty($item['quantity']) || $item['quantity'] < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid item data']);
            return;
        }
        
        $stmt = $pdo->prepare("SELECT item_id, price, is_available FROM menu_items WHERE item_id = ?");
        $stmt->execute([$item['id']]);
        $menuItem = $stmt->fetch();
        
        if (!$menuItem || !$menuItem['is_available']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Item not available']);
            return;
        }
        
        $quantity = (int)$item['quantity'];
        $itemTotal = $menuItem['price'] * $quantity;
        $subtotal += $itemTotal;
        
        $itemDetails[] = [
            'item_id' => $menuItem['item_id'],
            'quantity' => $quantity,
            'price' => $menuItem['price']
        ];
    }
    
    $deliveryFee = 50.00; // Fixed delivery fee
    $total = $subtotal + $deliveryFee;
    
    // Create order
    $pdo->beginTransaction();
    
    try {
        $customerName = sanitizeInput($data['firstName']) . ' ' . sanitizeInput($data['lastName']);
        $phone = sanitizeInput($data['phone']);
        $address = sanitizeInput($data['address']);
        $instructions = sanitizeInput($data['instructions'] ?? '');
        $paymentMethod = sanitizeInput($data['paymentMethod']);
        $userId = isAuthenticated() ? $_SESSION['user_id'] : null;
        
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
        
        foreach ($itemDetails as $item) {
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
        error_log("Order error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to place order']);
    }
}

function handleGetOrders() {
    global $pdo;
    
    // For admin - get all orders
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC");
    $orders = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $orders]);
}

function handleOrderHistory() {
    global $pdo;
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT o.*, COUNT(oi.order_item_id) as item_count
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $orders]);
}

// User Profile Handlers
function handleGetProfile() {
    global $pdo;
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, phone, address FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
}

function handleUpdateProfile() {
    global $pdo;
    
    $userId = $_SESSION['user_id'];
    $data = json_decode(file_get_contents('php://input'), true);
    
    $firstName = sanitizeInput($data['firstName'] ?? '');
    $lastName = sanitizeInput($data['lastName'] ?? '');
    $phone = sanitizeInput($data['phone'] ?? '');
    $address = sanitizeInput($data['address'] ?? '');
    
    if (empty($firstName) || empty($lastName)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'First name and last name are required']);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE user_id = ?");
    $stmt->execute([$firstName, $lastName, $phone, $address, $userId]);
    
    if ($stmt->rowCount() > 0) {
        // Update session name
        $_SESSION['first_name'] = $firstName;
        
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $userId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $phone,
                'address' => $address
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
}

// Example endpoint handler
function handleGetMenu() {
    global $pdo;
    
    header('Content-Type: application/json');
    
    try {
        $stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}