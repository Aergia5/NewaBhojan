<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Clear output buffers
while (ob_get_level()) ob_end_clean();

require_once 'db_connect.php';

try {
    $category = isset($_GET['category']) ? $_GET['category'] : 'all';
    
    // Sample data - replace with your database query
    $menuItems = [
        [
            'item_id' => 1,
            'name' => "Bara",
            'description' => "Savory lentil pancake, a Newari staple food often served with spicy chutney.",
            'price' => 250,
            'image_url' => "pic/Bara.jpg",
            'category_name' => "appetizers"
        ],
        [
            'item_id' => 2,
            'name' => "Chatamari",
            'description' => "Newari rice crepe topped with minced meat, eggs and spices - often called 'Newari Pizza'.",
            'price' => 300,
            'image_url' => "pic/Chatamari.jpg",
            'category_name' => "appetizers"
        ],
        [
            'item_id' => 3,
            'name' => "Samay Baji",
            'description' => "Traditional Newari platter with beaten rice, black soybeans, meat, ginger, garlic and more.",
            'price' => 280,
            'image_url' => "pic/SamayBaji.jpg",
            'category_name' => "main"
        ],
        [
            'item_id' => 4,
            'name' => "Choila",
            'description' => "Spicy grilled buffalo meat marinated with traditional Newari spices and mustard oil.",
            'price' => 350,
            'image_url' => "pic/choila.jpg",
            'category_name' => "appetizers"
        ],
        [
            'item_id' => 5,
            'name' => "Newari Khaja Set",
            'description' => "Complete Newari meal with bara, choila, chatamari, aalu tama, and other side dishes.",
            'price' => 320,
            'image_url' => "pic/Newari Khaja Set.jpg",
            'category_name' => "main"
        ],
        [
            'item_id' => 6,
            'name' => "Aalu Tama",
            'description' => "Traditional Newari curry made with bamboo shoots, potatoes and black-eyed peas.",
            'price' => 280,
            'image_url' => "pic/Aloo-tama.jpg",
            'category_name' => "main"
        ],
        [
            'item_id' => 7,
            'name' => "Kachila",
            'description' => "Minced raw meat mixed with spices, mustard oil and beaten rice.",
            'price' => 420,
            'image_url' => "pic/kachila.jpg",
            'category_name' => "appetizers"
        ],
        [
            'item_id' => 8,
            'name' => "Yomari",
            'description' => "Sweet dumpling made of rice flour with chaku (molasses) filling, a festive delicacy.",
            'price' => 200,
            'image_url' => "pic/Yomari.jpeg",
            'category_name' => "sweets"
        ],
        [
            'item_id' => 9,
            'name' => "Juju Dhau",
            'description' => "\"King of Yogurt\" - creamy, sweetened yogurt from Bhaktapur, served in clay pots.",
            'price' => 320,
            'image_url' => "pic/JujuDhau.jpg",
            'category_name' => "sweets"
        ],
        [
            'item_id' => 10,
            'name' => "Lakhamari",
            'description' => "Traditional Newari sweet biscuit, often served during festivals and weddings.",
            'price' => 180,
            'image_url' => "pic/lakmari.jpg",
            'category_name' => "sweets"
        ],
        [
            'item_id' => 11,
            'name' => "Sanya Khuna",
            'description' => "Sweet rice pudding with jaggery, a traditional Newari dessert.",
            'price' => 220,
            'image_url' => "pic/Sanyakhuna.jpg",
            'category_name' => "appetizers"
        ],
        [
            'item_id' => 12,
            'name' => "Aila",
            'description' => "Traditional Newari alcoholic beverage made from fermented rice, millet or grains.",
            'price' => 180,
            'image_url' => "pic/aila.jpg",
            'category_name' => "beverages"
        ],
        [
            'item_id' => 13,
            'name' => "Thwon",
            'description' => "Traditional Newari rice beer, mildly alcoholic with a sweet-sour taste.",
            'price' => 120,
            'image_url' => "pic/thwon.jpg",
            'category_name' => "beverages"
        ],
        [
            'item_id' => 14,
            'name' => "Newari Feast (For 2)",
            'description' => "Complete Newari feast including all appetizers, main dishes, and desserts for two people.",
            'price' => 850,
            'image_url' => "pic/Newari Khaja Set 2.jpg",
            'category_name' => "sets"
        ],
        [
            'item_id' => 15,
            'name' => "Newari Family Set (For 4)",
            'description' => "Complete Newari meal set for a family of four with variety of dishes and desserts.",
            'price' => 1200,
            'image_url' => "pic/Newari Khaja Set 4.jpg",
            'category_name' => "sets"
        ]
    ];

    // Filter by category if needed
    if ($category !== 'all') {
        $menuItems = array_filter($menuItems, function($item) use ($category) {
            return strtolower($item['category_name']) === strtolower($category);
        });
    }

    echo json_encode([
        'success' => true,
        'data' => array_values($menuItems) // reindex array after filtering
    ]);
    
} catch (Exception $e) {
    // Log the error
    error_log('Menu API Error: ' . $e->getMessage());
    
    // Return proper JSON error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving menu items',
        'error' => $e->getMessage()
    ]);
}