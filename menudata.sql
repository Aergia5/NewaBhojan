-- Insert categories
INSERT INTO categories (name, description) VALUES
('Appetizers', 'Traditional Newari starters and snacks'),
('Main Dishes', 'Hearty Newari main courses'),
('Sweets', 'Traditional Newari desserts and sweets'),
('Beverages', 'Newari drinks and alcoholic beverages'),
('Meal Sets', 'Complete Newari meal sets');

-- Insert menu items (matches the data in menu.php)
INSERT INTO menu_items (category_id, name, description, price, image_url) VALUES
(1, 'Bara', 'Savory lentil pancake, a Newari staple food often served with spicy chutney.', 250, 'pic/Bara.jpg'),
(1, 'Chatamari', 'Newari rice crepe topped with minced meat, eggs and spices - often called "Newari Pizza".', 300, 'pic/Chatamari.jpg'),
(2, 'Samay Baji', 'Traditional Newari platter with beaten rice, black soybeans, meat, ginger, garlic and more.', 280, 'pic/SamayBaji.jpg'),
(1, 'Choila', 'Spicy grilled buffalo meat marinated with traditional Newari spices and mustard oil.', 350, 'pic/choila.jpg'),
(5, 'Newari Khaja Set', 'Complete Newari meal with bara, choila, chatamari, aalu tama, and other side dishes.', 320, 'pic/Newari Khaja Set.jpg'),
(2, 'Aalu Tama', 'Traditional Newari curry made with bamboo shoots, potatoes and black-eyed peas.', 280, 'pic/Aloo-tama.jpg'),
(1, 'Kachila', 'Minced raw meat mixed with spices, mustard oil and beaten rice.', 420, 'pic/kachila.jpg'),
(3, 'Yomari', 'Sweet dumpling made of rice flour with chaku (molasses) filling, a festive delicacy.', 200, 'pic/Yomari.jpeg'),
(3, 'Juju Dhau', '"King of Yogurt" - creamy, sweetened yogurt from Bhaktapur, served in clay pots.', 320, 'pic/JujuDhau.jpg'),
(3, 'Lakhamari', 'Traditional Newari sweet biscuit, often served during festivals and weddings.', 180, 'pic/lakmari.jpg'),
(1, 'Sanya Khuna', 'Sweet rice pudding with jaggery, a traditional Newari dessert.', 220, 'pic/Sanyakhuna.jpg'),
(4, 'Aila', 'Traditional Newari alcoholic beverage made from fermented rice, millet or grains.', 180, 'pic/aila.jpg'),
(4, 'Thwon', 'Traditional Newari rice beer, mildly alcoholic with a sweet-sour taste.', 120, 'pic/thwon.jpg'),
(5, 'Newari Feast (For 2)', 'Complete Newari feast including all appetizers, main dishes, and desserts for two people.', 850, 'pic/Newari Khaja Set 2.jpg'),
(5, 'Newari Family Set (For 4)', 'Complete Newari meal set for a family of four with variety of dishes and desserts.', 1200, 'pic/Newari Khaja Set 4.jpg');

-- Create a sample admin user (password: admin123)
INSERT INTO users (first_name, last_name, email, password_hash, phone, address) VALUES
('Admin', 'User', 'admin@newabhojan.com', '$2y$12$QjSH496pcT5CEbzjD/vtVeHe03z.au/6P9E8xX8q3f7IgFZRSSvGy', '9841000000', 'Basantapur, Kathmandu');