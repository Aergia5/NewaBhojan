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

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['action'])) {
        throw new Exception('Invalid request');
    }

    $action = $input['action'];
    $response = [];
    
    switch ($action) {
        case 'login':
            if (empty($input['email']) || empty($input['password'])) {
                throw new Exception('Email and password are required');
            }
            
            // Validate credentials
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$input['email']]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($input['password'], $user['password_hash'])) {
                throw new Exception('Invalid credentials');
            }
            
            // Start session
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            
            $response = [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['user_id'],
                    'firstName' => $user['first_name'],
                    'email' => $user['email']
                ]
            ];
            break;
            
        case 'signup':
            // Validate input
            $required = ['firstName', 'lastName', 'email', 'password', 'confirmPassword'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    throw new Exception("$field is required");
                }
            }
            
            if ($input['password'] !== $input['confirmPassword']) {
                throw new Exception('Passwords do not match');
            }
            
            if (strlen($input['password']) < 8) {
                throw new Exception('Password must be at least 8 characters');
            }
            
            // Check if email exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$input['email']]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Email already registered');
            }
            
            // Create user
            $passwordHash = password_hash($input['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$input['firstName'], $input['lastName'], $input['email'], $passwordHash]);
            
            $response = [
                'success' => true,
                'message' => 'Registration successful'
            ];
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}