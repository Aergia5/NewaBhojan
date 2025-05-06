<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newa Bhojan - Authentic Newari Cuisine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f5f2;
        }
        
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://media-cdn.tripadvisor.com/media/photo-o/12/fb/64/53/samay-baji-traditional.jpg');
            background-size: cover;
            background-position: center;
        }
        
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .food-card {
            transition: all 0.3s ease;
        }
        
        .price-tag {
            background-color: #e53e3e;
            color: white;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -15px;
            right: -15px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .nepali-font {
            font-family: 'Preeti', sans-serif;
        }
        
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e53e3e;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .category-btn.active {
            background-color: #e53e3e;
            color: white;
        }

        /* Mobile menu styles */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .mobile-menu.open {
            max-height: 500px;
            transition: max-height 0.5s ease-in;
        }

        /* Prevent scrolling when mobile menu is open */
        body.no-scroll {
            overflow: hidden;
        }
        
        /* Animation for food cards */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .food-card {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal.open {
            display: flex;
        }
        
        .modal-container {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        /* Form styles */
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #4a5568;
        }
        
        .form-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            transition: border-color 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #e53e3e;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }
        
        /* Order summary styles */
        .order-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .order-total {
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: bold;
        }
        
        /* Tab styles for auth forms */
        .auth-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }
        
        .auth-tab {
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        
        .auth-tab.active {
            border-bottom-color: #e53e3e;
            color: #e53e3e;
        }
        
        /* Cart modal styles */
        #cartModal {
            transition: all 0.3s ease;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
        }
        
        .quantity-btn {
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            cursor: pointer;
        }
        
        .quantity-value {
            margin: 0 0.5rem;
            min-width: 20px;
            text-align: center;
        }
        
        /* Cart item styles */
        .cart-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-right: 1rem;
        }
        
        .cart-item-details {
            flex-grow: 1;
        }
        
        .cart-item-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .cart-item-price {
            color: #e53e3e;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .cart-item-actions {
            display: flex;
            align-items: center;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            cursor: pointer;
            border-radius: 0.25rem;
        }
        
        .quantity-btn:hover {
            background-color: #e2e8f0;
        }
        
        .quantity-value {
            margin: 0 0.5rem;
            min-width: 20px;
            text-align: center;
        }
        
        .remove-item-btn {
            color: #e53e3e;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
        }
        
        .remove-item-btn:hover {
            color: #c53030;
        }

        /* Loading spinner */
        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <img src="pic/NB-Logo.png" alt="Newa Bhojan Logo" class="h-10 mr-2">
                <h1 class="text-xl font-bold text-red-600">Newa Bhojan</h1>
            </div>
            
            <nav class="hidden md:flex space-x-8">
                <a href="#home" class="text-gray-800 hover:text-red-600 font-medium">Home</a>
                <a href="#menu" class="text-gray-800 hover:text-red-600 font-medium">Menu</a>
                <a href="#about" class="text-gray-800 hover:text-red-600 font-medium">About</a>
                <a href="#contact" class="text-gray-800 hover:text-red-600 font-medium">Contact</a>
            </nav>
            
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <i class="fas fa-shopping-cart text-2xl text-gray-700 cursor-pointer" id="cartIcon"></i>
                    <span class="cart-badge">0</span>
                </div>
                <button id="authButton" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition hidden md:block">
                    <?php echo isset($_SESSION['user_id']) ? 'My Account' : 'Login'; ?>
                </button>
                <button id="mobileMenuButton" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="mobile-menu bg-white md:hidden">
            <div class="container mx-auto px-4 py-2 flex flex-col space-y-3">
                <a href="#home" class="block py-2 text-gray-800 hover:text-red-600 font-medium border-b border-gray-100">Home</a>
                <a href="#menu" class="block py-2 text-gray-800 hover:text-red-600 font-medium border-b border-gray-100">Menu</a>
                <a href="#about" class="block py-2 text-gray-800 hover:text-red-600 font-medium border-b border-gray-100">About</a>
                <a href="#contact" class="block py-2 text-gray-800 hover:text-red-600 font-medium border-b border-gray-100">Contact</a>
                <button id="mobileAuthButton" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition w-full mb-2">
                    <?php echo isset($_SESSION['user_id']) ? 'My Account' : 'Login'; ?>
                </button>
            </div>
        </div>
    </header>

    <!-- Auth Modal (for both login and signup) -->
    <div id="authModal" class="modal">
        <div class="modal-container">
            <div class="flex justify-between items-center mb-6">
                <div class="auth-tabs">
                    <div id="loginTab" class="auth-tab active">Login</div>
                    <div id="signupTab" class="auth-tab">Sign Up</div>
                </div>
                <button id="closeAuthModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" class="auth-form active">
                <div class="form-group">
                    <label for="loginEmail" class="form-label">Email Address</label>
                    <input type="email" id="loginEmail" name="email" required class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" id="loginPassword" name="password" required class="form-input">
                </div>
                
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md font-medium mb-4">
                    <span id="loginBtnText">Login</span>
                    <span id="loginSpinner" class="spinner hidden"></span>
                </button>
                
                <div class="text-center">
                    <a href="#" id="forgotPasswordLink" class="text-red-600 hover:underline text-sm">Forgot password?</a>
                </div>
            </form>
            
            <!-- Signup Form -->
            <form id="signupForm" class="auth-form" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-group">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" id="firstName" name="firstName" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required class="form-input">
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label for="signupEmail" class="form-label">Email Address</label>
                    <input type="email" id="signupEmail" name="email" required class="form-input">
                </div>
                
                <div class="form-group mb-4">
                    <label for="signupPassword" class="form-label">Password</label>
                    <input type="password" id="signupPassword" name="password" required class="form-input">
                </div>
                
                <div class="form-group mb-4">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required class="form-input">
                </div>
                
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md font-medium">
                    <span id="signupBtnText">Create Account</span>
                    <span id="signupSpinner" class="spinner hidden"></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-container">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
                <button id="closeForgotPasswordModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <form id="forgotPasswordForm">
                <div class="form-group mb-4">
                    <label for="forgotEmail" class="form-label">Email Address</label>
                    <input type="email" id="forgotEmail" name="email" required class="form-input">
                </div>
                
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md font-medium mb-4">
                    <span id="forgotBtnText">Send Reset Link</span>
                    <span id="forgotSpinner" class="spinner hidden"></span>
                </button>
                
                <div class="text-center">
                    <p class="text-gray-600">Remember your password? <a href="#" id="backToLoginLink" class="text-red-600 hover:underline">Back to login</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="modal">
        <div class="modal-container">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Checkout</h2>
                <button id="closeCheckoutModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <form id="checkoutForm">
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">Delivery Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="form-group">
                            <label for="checkoutFirstName" class="form-label">First Name</label>
                            <input type="text" id="checkoutFirstName" name="firstName" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="checkoutLastName" class="form-label">Last Name</label>
                            <input type="text" id="checkoutLastName" name="lastName" required class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="checkoutPhone" class="form-label">Phone Number</label>
                        <input type="tel" id="checkoutPhone" name="phone" required class="form-input">
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="checkoutAddress" class="form-label">Delivery Address</label>
                        <textarea id="checkoutAddress" name="address" rows="3" required class="form-input"></textarea>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="checkoutInstructions" class="form-label">Delivery Instructions (Optional)</label>
                        <textarea id="checkoutInstructions" name="instructions" rows="2" class="form-input"></textarea>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">Payment Method</h3>
                    
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" id="cashOnDelivery" name="paymentMethod" value="cash" checked class="mr-2">
                            <label for="cashOnDelivery">Cash on Delivery</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="khalti" name="paymentMethod" value="khalti" class="mr-2">
                            <label for="khalti">Khalti</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="esewa" name="paymentMethod" value="esewa" class="mr-2">
                            <label for="esewa">eSewa</label>
                        </div>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-lg font-bold mb-4">Order Summary</h3>
                    <div id="checkoutOrderSummary">
                        <!-- Order items will be dynamically inserted here -->
                    </div>
                    <div class="order-total flex justify-between">
                        <span>Total:</span>
                        <span id="checkoutTotal">Rs 0</span>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-md font-medium">
                    <span id="checkoutBtnText">Place Order</span>
                    <span id="checkoutSpinner" class="spinner hidden"></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Authentic Newari Cuisine</h1>
            <p class="text-xl md:text-2xl mb-8">Experience the rich flavors of Nepal's indigenous Newar community</p>
            <a href="#menu" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full text-lg transition inline-block">
                Order Now
            </a>
        </div>
    </section>

<!-- Menu Section -->
<section id="menu" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Our Menu</h2>
        
        <!-- Menu Categories -->
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <button class="category-btn active px-4 py-2 rounded-full bg-gray-100 hover:bg-red-600 hover:text-white transition" data-category="all">All Items</button>
            <button class="category-btn px-4 py-2 rounded-full bg-gray-100 hover:bg-red-600 hover:text-white transition" data-category="appetizers">Appetizers</button>
            <button class="category-btn px-4 py-2 rounded-full bg-gray-100 hover:bg-red-600 hover:text-white transition" data-category="main">Main Dishes</button>
            <button class="category-btn px-4 py-2 rounded-full bg-gray-100 hover:bg-red-600 hover:text-white transition" data-category="sweets">Sweets</button>
            <button class="category-btn px-4 py-2 rounded-full bg-gray-100 hover:bg-red-600 hover:text-white transition" data-category="beverages">Beverages</button>
            <button class="category-btn px-4 py-2 rounded-full bg-gray-100 hover:bg-red-600 hover:text-white transition" data-category="sets">Meal Sets</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8" id="foodItemsContainer">
            <!-- Appetizers -->
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="appetizers">
                <img src="pic/Bara.jpg" alt="Bara" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Bara</h3>
                    <p class="text-gray-600 mb-4">Savory lentil pancake, a Newari staple food often served with spicy chutney.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 250</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="appetizers">
                <img src="pic/Chatamari.jpg" alt="Chatamari" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Chatamari</h3>
                    <p class="text-gray-600 mb-4">Newari rice crepe topped with minced meat, eggs and spices - often called "Newari Pizza".</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 300</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="main">
                <img src="pic/SamayBaji.jpg" alt="Samay Baji" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Samay Baji</h3>
                    <p class="text-gray-600 mb-4">Traditional Newari platter with beaten rice, black soybeans, meat, ginger, garlic and more.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 280</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="appetizers">
                <img src="pic/choila.jpg" alt="Choila" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Choila</h3>
                    <p class="text-gray-600 mb-4">Spicy grilled buffalo meat marinated with traditional Newari spices and mustard oil.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 350</span>
                    </div>
                </div>
            </div>
            
            <!-- Main Dishes -->
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="main">
                <img src="pic/Newari Khaja Set.jpg" alt="Newari Khaja Set" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Newari Khaja Set</h3>
                    <p class="text-gray-600 mb-4">Complete Newari meal with bara, choila, chatamari, aalu tama, and other side dishes.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 320</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="main">
                <img src="pic/Aloo-tama.jpg" alt="Aalu Tama" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Aalu Tama</h3>
                    <p class="text-gray-600 mb-4">Traditional Newari curry made with bamboo shoots, potatoes and black-eyed peas.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 280</span>
                    </div>
                </div>
            </div>

            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="appetizers">
                <img src="pic/kachila.jpg" alt="Kachila" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Kachila</h3>
                    <p class="text-gray-600 mb-4">Minced raw meat mixed with spices, mustard oil and beaten rice.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 420</span>
                    </div>
                </div>
            </div>
            
            <!-- Sweets -->
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="sweets">
                <img src="pic/Yomari.jpeg" alt="Yomari" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Yomari</h3>
                    <p class="text-gray-600 mb-4">Sweet dumpling made of rice flour with chaku (molasses) filling, a festive delicacy.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 200</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="sweets">
                <img src="pic/JujuDhau.jpg" alt="Juju Dhau" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Juju Dhau</h3>
                    <p class="text-gray-600 mb-4">"King of Yogurt" - creamy, sweetened yogurt from Bhaktapur, served in clay pots.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 320</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="sweets">
                <img src="pic/lakmari.jpg" alt="Lakhamari" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Lakhamari</h3>
                    <p class="text-gray-600 mb-4">Traditional Newari sweet biscuit, often served during festivals and weddings.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 180</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="appetizers">
                <img src="pic/Sanyakhuna.jpg" alt="Sanya Khuna" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Sanya Khuna</h3>
                    <p class="text-gray-600 mb-4">Sweet rice pudding with jaggery, a traditional Newari dessert.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 220</span>
                    </div>
                </div>
            </div>
            
            <!-- Beverages -->
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="beverages">
                <img src="pic/aila.jpg" alt="Aila" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Aila</h3>
                    <p class="text-gray-600 mb-4">Traditional Newari alcoholic beverage made from fermented rice, millet or grains.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 180</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="beverages">
                <img src="pic/thwon.jpg" alt="Thwon" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Thwon</h3>
                    <p class="text-gray-600 mb-4">Traditional Newari rice beer, mildly alcoholic with a sweet-sour taste.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 120</span>
                    </div>
                </div>
            </div>
            
            <!-- Meal Sets -->
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="sets">
                <img src="pic/Newari Khaja Set 2.jpg" alt="Newari Feast" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Newari Feast (For 2)</h3>
                    <p class="text-gray-600 mb-4">Complete Newari feast including all appetizers, main dishes, and desserts for two people.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 850</span>
                    </div>
                </div>
            </div>
            
            <div class="food-card bg-white rounded-lg overflow-hidden shadow-md relative" data-category="sets">
                <img src="pic/Newari Khaja Set 4.jpg" alt="Newari Family Set" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Newari Family Set (For 4)</h3>
                    <p class="text-gray-600 mb-4">Complete Newari meal set for a family of four with variety of dishes and desserts.</p>
                    <div class="flex justify-between items-center">
                        <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                            Add to Cart
                        </button>
                        <span class="text-gray-500 text-sm">Rs 1200</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Why Choose Us -->
    <section id="about" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Why Choose Newa Bhojan?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-lg bg-white">
                    <div class="text-red-600 text-4xl mb-4">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Authentic Ingredients</h3>
                    <p class="text-gray-600">We use only traditional Newari ingredients sourced directly from local farmers and producers.</p>
                </div>
                
                <div class="text-center p-6 rounded-lg bg-white">
                    <div class="text-red-600 text-4xl mb-4">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Traditional Recipes</h3>
                    <p class="text-gray-600">Our dishes are prepared following centuries-old Newari recipes passed down through generations.</p>
                </div>
                
                <div class="text-center p-6 rounded-lg bg-white">
                    <div class="text-red-600 text-4xl mb-4">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Fast Delivery</h3>
                    <p class="text-gray-600">Get your Newari feast delivered hot and fresh within 45 minutes anywhere in Kathmandu Valley.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Newa Bhojan</h3>
                    <p class="text-gray-400">Bringing authentic Newari cuisine to your doorstep since 2010.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white">Home</a></li>
                        <li><a href="#menu" class="text-gray-400 hover:text-white">Menu</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> Basantapur, Kathmandu</li>
                        <li class="flex items-center"><i class="fas fa-phone-alt mr-2"></i> +977 9841000000</li>
                        <li class="flex items-center"><i class="fas fa-envelope mr-2"></i> info@newabhojan.com</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Opening Hours</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>Sunday - Thursday: 10AM - 9PM</li>
                        <li>Friday: 10AM - 8PM</li>
                        <li>Saturday: 11AM - 8PM</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- Shopping Cart Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" id="cartModal">
        <div class="absolute right-0 top-0 h-full bg-white w-full md:w-1/3 overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Your Cart</h2>
                    <button id="closeCart" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                
                <div class="space-y-4" id="cartItems">
                    <!-- Cart items will be added here dynamically -->
                    <div class="text-center py-8 text-gray-500" id="emptyCartMessage">
                        <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-6 pt-4 hidden" id="cartSummary">
                    <div class="flex justify-between mb-2">
                        <span>Subtotal:</span>
                        <span class="font-bold" id="subtotal">Rs 0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Delivery Fee:</span>
                        <span class="font-bold" id="deliveryFee">Rs 50</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold mt-4">
                        <span>Total:</span>
                        <span id="total">Rs 50</span>
                    </div>
                    
                    <button id="proceedToCheckout" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-md mt-6 font-medium transition">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notificationToast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg hidden">
        <div class="flex items-center">
            <span id="notificationMessage"></span>
            <button id="closeNotification" class="ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        // Global variables
        const cart = [];
        let cartCount = 0;
        const cartBadge = document.querySelector('.cart-badge');
        const body = document.body;

        // API Configuration
        const API_BASE_URL = window.location.origin; // Adjust if your API is on a different domain
        const AUTH_TOKEN = '<?php echo isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : ''; ?>';

        // Helper function for API calls
        async function makeApiRequest(endpoint, method = 'GET', data = null) {
            const headers = {
                'Content-Type': 'application/json',
            };
            
            if (AUTH_TOKEN) {
                headers['Authorization'] = `Bearer ${AUTH_TOKEN}`;
            }
            
            const config = {
                method,
                headers,
            };
            
            if (data) {
                config.body = JSON.stringify(data);
            }
            
            try {
                const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
                const responseData = await response.json();
                
                if (!response.ok) {
                    throw new Error(responseData.message || 'Request failed');
                }
                
                return responseData;
            } catch (error) {
                console.error('API Error:', error);
                showNotification(error.message, 'error');
                throw error;
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const toast = document.getElementById('notificationToast');
            const messageEl = document.getElementById('notificationMessage');
            
            toast.className = `fixed bottom-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-3 rounded-md shadow-lg flex items-center`;
            messageEl.textContent = message;
            toast.classList.remove('hidden');
            
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 5000);
        }

        // Close notification
        document.getElementById('closeNotification').addEventListener('click', function() {
            document.getElementById('notificationToast').classList.add('hidden');
        });

        // Toggle loading state
        function toggleLoading(buttonId, isLoading) {
            const btnText = document.getElementById(`${buttonId}Text`);
            const spinner = document.getElementById(`${buttonId}Spinner`);
            
            if (isLoading) {
                btnText.classList.add('hidden');
                spinner.classList.remove('hidden');
            } else {
                btnText.classList.remove('hidden');
                spinner.classList.add('hidden');
            }
        }

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('open');
            body.classList.toggle('no-scroll');
            
            const icon = mobileMenuButton.querySelector('i');
            if (mobileMenu.classList.contains('open')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('open');
                body.classList.remove('no-scroll');
                mobileMenuButton.querySelector('i').classList.remove('fa-times');
                mobileMenuButton.querySelector('i').classList.add('fa-bars');
            });
        });

        // Menu category filtering
        const categoryButtons = document.querySelectorAll('.category-btn');
        
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Update active button
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter food cards
                document.querySelectorAll('.food-card').forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Load menu items from API
        async function loadMenuItems() {
            try {
                const response = await makeApiRequest('/newabhojan/api/menu');
                const container = document.getElementById('foodItemsContainer');
                container.innerHTML = '';
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'food-card bg-white rounded-lg overflow-hidden shadow-md relative';
                        card.dataset.category = item.category_name.toLowerCase().replace(' ', '-');
                        
                        card.innerHTML = `
                        <div class="flex flex-col h-full">
                            <img src="${item.image_url}" alt="${item.name}" class="w-full h-40 object-cover">
                            <div class="p-3 flex flex-col flex-grow">
                                <h3 class="text-lg font-bold mb-1 text-gray-800 truncate">${item.name}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">${item.description}</p>
                                <div class="mt-auto flex justify-between items-center">
                                    <button class="add-to-cart bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition text-sm"
                                            data-id="${item.item_id}" 
                                            data-name="${item.name}" 
                                            data-price="${item.price}" 
                                            data-image="${item.image_url}">
                                        Add to Cart
                                    </button>
                                    <span class="text-gray-500 text-sm font-medium">Rs ${item.price}</span>
                                </div>
                            </div>
                        </div>
                    `;
                        
                        container.appendChild(card);
                    });
                    
                    // Attach event listeners to new cart buttons
                    document.querySelectorAll('.add-to-cart').forEach(button => {
                        button.addEventListener('click', function() {
                            const item = {
                                id: this.dataset.id,
                                name: this.dataset.name,
                                price: parseFloat(this.dataset.price),
                                image: this.dataset.image
                            };
                            addToCart(item);
                        });
                    });
                } else {
                    container.innerHTML = `
                        <div class="col-span-4 text-center py-12 text-gray-500">
                            <i class="fas fa-utensils text-4xl mb-4"></i>
                            <p>No menu items available at the moment</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Failed to load menu:', error);
                document.getElementById('foodItemsContainer').innerHTML = `
                    <div class="col-span-4 text-center py-12 text-gray-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <p>Failed to load menu items. Please try again later.</p>
                    </div>
                `;
            }
        }

        // Cart functionality
        function addToCart(item) {
            const existingItem = cart.find(cartItem => cartItem.id === item.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ ...item, quantity: 1 });
            }
            
            cartCount++;
            cartBadge.textContent = cartCount;
            updateCartUI();
            
            showNotification(`${item.name} added to cart!`);
        }

        function removeFromCart(index) {
            if (index >= 0 && index < cart.length) {
                const removedItem = cart[index];
                cartCount -= removedItem.quantity;
                cart.splice(index, 1);
                cartBadge.textContent = cartCount;
                updateCartUI();
                
                showNotification(`${removedItem.name} removed from cart`, 'error');
            }
        }

        function updateCartUI() {
            const cartItemsContainer = document.getElementById('cartItems');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            const cartSummary = document.getElementById('cartSummary');
            
            cartItemsContainer.innerHTML = '';
            
            if (cart.length === 0) {
                emptyCartMessage.classList.remove('hidden');
                cartSummary.classList.add('hidden');
                cartItemsContainer.appendChild(emptyCartMessage);
                return;
            }
            
            emptyCartMessage.classList.add('hidden');
            cartSummary.classList.remove('hidden');
            
            let subtotal = 0;
            
            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                cartItem.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <h4 class="cart-item-title">${item.name}</h4>
                        <p class="cart-item-price">Rs ${item.price.toFixed(2)}</p>
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                <button class="quantity-btn decrease-quantity" data-index="${index}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity-value">${item.quantity}</span>
                                <button class="quantity-btn increase-quantity" data-index="${index}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button class="remove-item-btn" data-index="${index}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
                cartItemsContainer.appendChild(cartItem);
            });
            
            const deliveryFee = 50;
            const total = subtotal + deliveryFee;
            
            document.getElementById('subtotal').textContent = `Rs ${subtotal.toFixed(2)}`;
            document.getElementById('total').textContent = `Rs ${total.toFixed(2)}`;
            
            // Add event listeners to dynamic elements
            document.querySelectorAll('.remove-item-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    removeFromCart(index);
                });
            });
            
            document.querySelectorAll('.increase-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    cart[index].quantity++;
                    cartCount++;
                    cartBadge.textContent = cartCount;
                    updateCartUI();
                });
            });
            
            document.querySelectorAll('.decrease-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                        cartCount--;
                    } else {
                        removeFromCart(index);
                    }
                    updateCartUI();
                });
            });
        }

        // Cart modal controls
        const cartModal = document.getElementById('cartModal');
        const cartIcon = document.getElementById('cartIcon');
        
        cartIcon.addEventListener('click', function() {
            cartModal.classList.remove('hidden');
            body.classList.add('no-scroll');
            updateCartUI();
        });

        document.getElementById('closeCart').addEventListener('click', function() {
            cartModal.classList.add('hidden');
            body.classList.remove('no-scroll');
        });

        cartModal.addEventListener('click', function(e) {
            if (e.target === cartModal) {
                cartModal.classList.add('hidden');
                body.classList.remove('no-scroll');
            }
        });

        // Proceed to Checkout
        document.getElementById('proceedToCheckout').addEventListener('click', function() {
            if (cart.length === 0) {
                showNotification('Your cart is empty!', 'error');
                return;
            }
            
            updateCheckoutOrderSummary();
            document.getElementById('checkoutModal').classList.add('open');
            body.classList.add('no-scroll');
        });

        function updateCheckoutOrderSummary() {
            const checkoutOrderSummary = document.getElementById('checkoutOrderSummary');
            checkoutOrderSummary.innerHTML = '';
            
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                const itemElement = document.createElement('div');
                itemElement.className = 'order-summary-item';
                itemElement.innerHTML = `
                    <span>${item.name} (${item.quantity}x)</span>
                    <span>Rs ${itemTotal.toFixed(2)}</span>
                `;
                checkoutOrderSummary.appendChild(itemElement);
            });
            
            const deliveryFee = 50;
            const total = subtotal + deliveryFee;
            
            const deliveryElement = document.createElement('div');
            deliveryElement.className = 'order-summary-item';
            deliveryElement.innerHTML = `
                <span>Delivery Fee</span>
                <span>Rs ${deliveryFee.toFixed(2)}</span>
            `;
            checkoutOrderSummary.appendChild(deliveryElement);
            
            document.getElementById('checkoutTotal').textContent = `Rs ${total.toFixed(2)}`;
        }

        // Auth Modal Functionality
        const authModal = document.getElementById('authModal');
        const authButton = document.getElementById('authButton');
        const mobileAuthButton = document.getElementById('mobileAuthButton');
        const closeAuthModal = document.getElementById('closeAuthModal');
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');
        const loginTab = document.getElementById('loginTab');
        const signupTab = document.getElementById('signupTab');
        
        // Open auth modal
        authButton.addEventListener('click', function() {
            if (<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                // User is logged in - redirect to profile or show account info
                window.location.href = '#profile'; // You should implement a profile page
            } else {
                authModal.classList.add('open');
                body.classList.add('no-scroll');
                showLoginForm();
            }
        });
        
        mobileAuthButton.addEventListener('click', function() {
            if (<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                // User is logged in - redirect to profile or show account info
                window.location.href = '#profile'; // You should implement a profile page
            } else {
                authModal.classList.add('open');
                body.classList.add('no-scroll');
                mobileMenu.classList.remove('open');
                mobileMenuButton.querySelector('i').classList.remove('fa-times');
                mobileMenuButton.querySelector('i').classList.add('fa-bars');
                showLoginForm();
            }
        });
        
        // Close auth modal
        closeAuthModal.addEventListener('click', function() {
            authModal.classList.remove('open');
            body.classList.remove('no-scroll');
        });
        
        // Close auth modal when clicking outside
        authModal.addEventListener('click', function(e) {
            if (e.target === authModal) {
                authModal.classList.remove('open');
                body.classList.remove('no-scroll');
            }
        });
        
        // Switch between login and signup tabs
        loginTab.addEventListener('click', function() {
            showLoginForm();
        });
        
        signupTab.addEventListener('click', function() {
            showSignupForm();
        });
        
        function showLoginForm() {
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
            loginForm.style.display = 'block';
            signupForm.style.display = 'none';
        }
        
        function showSignupForm() {
            loginTab.classList.remove('active');
            signupTab.classList.add('active');
            loginForm.style.display = 'none';
            signupForm.style.display = 'block';
        }
        
        // Handle login form submission
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            if (!email || !password) {
                showNotification('Email and password are required', 'error');
                return;
            }
            
            try {
                toggleLoading('login', true);
                
                const response = await makeApiRequest('/auth/login', 'POST', {
                    email,
                    password
                });
                
                showNotification('Login successful!', 'success');
                
                // Refresh the page to update session status
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } catch (error) {
                console.error('Login error:', error);
            } finally {
                toggleLoading('login', false);
            }
        });
        
        // Handle signup form submission
        signupForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Basic validation
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                showNotification('All fields are required', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showNotification('Passwords do not match', 'error');
                return;
            }
            
            if (password.length < 8) {
                showNotification('Password must be at least 8 characters', 'error');
                return;
            }
            
            try {
                toggleLoading('signup', true);
                
                const response = await makeApiRequest('/auth/signup', 'POST', {
                    firstName,
                    lastName,
                    email,
                    password,
                    confirmPassword
                });
                
                showNotification('Account created successfully! Please login.', 'success');
                showLoginForm();
                signupForm.reset();
            } catch (error) {
                console.error('Signup error:', error);
            } finally {
                toggleLoading('signup', false);
            }
        });
        
        // Forgot Password Modal Functionality
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        const forgotPasswordLink = document.getElementById('forgotPasswordLink');
        const closeForgotPasswordModal = document.getElementById('closeForgotPasswordModal');
        const backToLoginLink = document.getElementById('backToLoginLink');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        
        // Open forgot password modal
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            authModal.classList.remove('open');
            forgotPasswordModal.classList.add('open');
        });
        
        // Close forgot password modal
        closeForgotPasswordModal.addEventListener('click', function() {
            forgotPasswordModal.classList.remove('open');
            body.classList.remove('no-scroll');
        });
        
        // Back to login link
        backToLoginLink.addEventListener('click', function(e) {
            e.preventDefault();
            forgotPasswordModal.classList.remove('open');
            authModal.classList.add('open');
            showLoginForm();
        });
        
        // Handle forgot password form submission
        forgotPasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('forgotEmail').value;
            
            if (!email) {
                showNotification('Email is required', 'error');
                return;
            }
            
            try {
                toggleLoading('forgot', true);
                
                const response = await makeApiRequest('/auth/forgot-password', 'POST', {
                    email
                });
                
                showNotification('If this email exists, a reset link has been sent', 'success');
                forgotPasswordForm.reset();
                forgotPasswordModal.classList.remove('open');
            } catch (error) {
                console.error('Forgot password error:', error);
            } finally {
                toggleLoading('forgot', false);
            }
        });
        
        // Checkout Modal Functionality
        const checkoutModal = document.getElementById('checkoutModal');
        const closeCheckoutModal = document.getElementById('closeCheckoutModal');
        const checkoutForm = document.getElementById('checkoutForm');
        
        // Close checkout modal
        closeCheckoutModal.addEventListener('click', function() {
            checkoutModal.classList.remove('open');
            body.classList.remove('no-scroll');
        });
        
        // Close checkout modal when clicking outside
        checkoutModal.addEventListener('click', function(e) {
            if (e.target === checkoutModal) {
                checkoutModal.classList.remove('open');
                body.classList.remove('no-scroll');
            }
        });
        
        // Handle checkout form submission
        checkoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Gather form data
            const formData = {
                firstName: document.getElementById('checkoutFirstName').value,
                lastName: document.getElementById('checkoutLastName').value,
                phone: document.getElementById('checkoutPhone').value,
                address: document.getElementById('checkoutAddress').value,
                instructions: document.getElementById('checkoutInstructions').value,
                paymentMethod: document.querySelector('input[name="paymentMethod"]:checked').value,
                cartItems: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price
                }))
            };
            
            // Validate required fields
            if (!formData.firstName || !formData.lastName || !formData.phone || !formData.address) {
                showNotification('Please fill all required fields', 'error');
                return;
            }
            
            try {
                toggleLoading('checkout', true);
                
                const response = await makeApiRequest('/orders', 'POST', formData);
                
                // Show success message
                showNotification(`Order placed successfully! Your order ID: ${response.orderId}`, 'success');
                
                // Clear cart and close modals
                cart.length = 0;
                cartCount = 0;
                cartBadge.textContent = '0';
                updateCartUI();
                
                checkoutModal.classList.remove('open');
                cartModal.classList.add('hidden');
                body.classList.remove('no-scroll');
                
                // Clear the form
                checkoutForm.reset();
            } catch (error) {
                console.error('Checkout error:', error);
            } finally {
                toggleLoading('checkout', false);
            }
        });
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Load menu items
            loadMenuItems();
            
            // Update auth button based on login status
            if (<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                authButton.textContent = 'My Account';
                mobileAuthButton.textContent = 'My Account';
            }
        });
    </script>
</body>
</html>